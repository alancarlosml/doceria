<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Encomenda;
use App\Models\Table;
use App\Models\Menu;
use App\Models\Product;
use App\Models\Category;
use App\Models\CarouselBanner;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PageController extends Controller
{
    /**
     * Display the main landing page with today's menu.
     * Esta página mostra os produtos selecionados no menus.blade.php
     */
    public function index()
    {
        // Get current day of week using numeric day (0=sunday, 1=monday, ..., 6=saturday)
        $numericDay = now()->dayOfWeek;

        // Map numeric days correctly to Portuguese names used in menu database
        $dayMapping = [
            0 => 'domingo',  // Sunday
            1 => 'segunda',  // Monday
            2 => 'terca',    // Tuesday
            3 => 'quarta',   // Wednesday
            4 => 'quinta',   // Thursday
            5 => 'sexta',    // Friday
            6 => 'sabado',   // Saturday
        ];

        $currentDayPt = $dayMapping[$numericDay] ?? 'segunda';

        Log::info('Carregando cardápio do dia', [
            'day_numeric' => $numericDay,
            'day_name' => $currentDayPt,
            'date' => now()->format('Y-m-d H:i:s')
        ]);

        // Buscar produtos do cardápio do dia usando a tabela 'menus'
        // Esta query busca APENAS os produtos que foram selecionados no menus.blade.php
        try {
            $menuItems = Menu::with(['product.category'])
                ->where('day_of_week', $currentDayPt)
                ->where('available', true)
                ->whereHas('product', function($query) {
                    $query->where('active', true); // Apenas produtos ativos
                })
                ->get();

            Log::info('Produtos encontrados no cardápio', [
                'total' => $menuItems->count(),
                'day' => $currentDayPt
            ]);

            // Se não houver produtos no menu de hoje, pegar produtos em destaque
            if ($menuItems->isEmpty()) {
                Log::warning('Nenhum produto encontrado no cardápio de ' . $currentDayPt);
                
                $featuredProducts = Product::where('active', true)
                    ->with('category')
                    ->orderBy('created_at', 'desc')
                    ->take(8)
                    ->get();
            } else {
                $featuredProducts = collect();
            }

        } catch (\Exception $e) {
            Log::error('Erro ao carregar cardápio na página inicial: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            $menuItems = collect();
            $featuredProducts = Product::where('active', true)
                ->with('category')
                ->orderBy('created_at', 'desc')
                ->take(8)
                ->get();
        }

        // Agrupar produtos por categoria para melhor exibição
        // Ordenar primeiro pela ordem da categoria, depois pelo nome
        $menuByCategory = $menuItems->sortBy(function($item) {
            $category = $item->product->category;
            if (!$category) {
                return [999999, 'Outros']; // Categorias sem categoria aparecem por último
            }
            return [$category->order ?? 999999, $category->name];
        })->groupBy(function($item) {
            return $item->product->category->name ?? 'Outros';
        });

        // Get categories for navigation
        $categories = Category::where('active', true)
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        // Get carousel banners
        $carouselBanners = CarouselBanner::getActiveBanners();

        // Nome formatado do dia para exibição
        $dayNamesDisplay = [
            'segunda' => 'Segunda-feira',
            'terca' => 'Terça-feira',
            'quarta' => 'Quarta-feira',
            'quinta' => 'Quinta-feira',
            'sexta' => 'Sexta-feira',
            'sabado' => 'Sábado',
            'domingo' => 'Domingo'
        ];

        $currentDayDisplay = $dayNamesDisplay[$currentDayPt] ?? ucfirst($currentDayPt);

        return view('pages.index', compact(
            'menuItems',
            'menuByCategory',
            'featuredProducts',
            'categories',
            'carouselBanners',
            'currentDayPt',
            'currentDayDisplay'
        ));
    }

    /**
     * Display gestor login page.
     */
    public function gestor()
    {
        return view('gestor.login');
    }

    /**
     * Display admin dashboard.
     */
    public function dashboard()
    {
        // Buscar vendas do dia atual
        $today = now()->format('Y-m-d');
        $todaySales = Sale::whereDate('created_at', $today)->sum('total');
        $todaySalesCount = Sale::whereDate('created_at', $today)->count();

        // Vendas pendentes (mesas ocupadas e pedidos em processamento)
        $pendingSales = Sale::whereIn('status', ['pendente', 'em_preparo', 'pronto'])
            ->with(['customer', 'table', 'motoboy', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Mesas ocupadas com vendas pendentes
        $occupiedTables = Table::where('status', 'ocupada')
            ->with(['sales' => function($query) {
                $query->whereIn('status', ['pendente', 'em_preparo', 'pronto', 'finalizado'])
                      ->with(['items.product', 'customer'])
                      ->latest();
            }])
            ->orderBy('number')
            ->get();

        // Entregas em andamento (apenas pedidos que realmente saíram para entrega)
        $upcomingDeliveries = Sale::where('status', 'saiu_entrega')
            ->where('type', 'delivery')
            ->with(['customer', 'motoboy'])
            ->orderBy('updated_at', 'desc')
            ->get();

        // Encomendas pendentes - buscar de ambos os modelos (Sale e Encomenda)
        // Encomendas do modelo Sale (apenas com delivery_date preenchida)
        $pendingEncomendasFromSales = Sale::whereIn('status', ['pendente', 'em_preparo', 'pronto'])
            ->where('type', 'encomenda')
            ->whereNotNull('delivery_date')
            ->with(['customer'])
            ->get();
        
        // Encomendas do modelo Encomenda (status: pendente, em_producao, pronto)
        // delivery_date é obrigatório neste modelo, então não precisa filtrar
        $pendingEncomendasFromModel = Encomenda::whereIn('status', ['pendente', 'em_producao', 'pronto'])
            ->with(['customer'])
            ->get();
        
        // Combinar e ordenar todas as encomendas
        $pendingEncomendas = $pendingEncomendasFromSales->concat($pendingEncomendasFromModel)
            ->sortBy(function($encomenda) {
                // Ordenar por data de entrega e depois por hora
                $date = $encomenda->delivery_date ? $encomenda->delivery_date->format('Y-m-d') : '9999-12-31';
                $time = $encomenda->delivery_time ? (is_string($encomenda->delivery_time) ? $encomenda->delivery_time : $encomenda->delivery_time->format('H:i:s')) : '23:59:59';
                return $date . ' ' . $time;
            })
            ->values();

        // Encomendas finalizadas hoje (pagas/entregues) - sem taxa de entrega
        $todayEncomendasCount = Encomenda::where('status', 'entregue')
            ->whereDate('updated_at', $today)
            ->count();
        $todayEncomendasTotal = Encomenda::where('status', 'entregue')
            ->whereDate('updated_at', $today)
            ->selectRaw('SUM(total - COALESCE(delivery_fee, 0)) as total_sem_taxa')
            ->value('total_sem_taxa') ?? 0;

        // Estatísticas rápidas
        $pendingSalesCount = $pendingSales->count();
        $occupiedTablesCount = $occupiedTables->count();
        $upcomingDeliveriesCount = $upcomingDeliveries->count();
        $pendingEncomendasCount = $pendingEncomendas->count();

        // Vendas recentes concluídas
        $recentSales = Sale::where('status', 'finalizado')
            ->with(['customer', 'items.product'])
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        // Itens com estoque baixo
        $lowStockItems = InventoryItem::where('active', true)
            ->whereRaw('current_quantity <= min_quantity')
            ->orderByRaw('current_quantity / NULLIF(min_quantity, 0)')
            ->take(5)
            ->get();

        $criticalStockItems = InventoryItem::where('active', true)
            ->whereRaw('current_quantity < (min_quantity * 0.5)')
            ->count();

        return view('gestor.dashboard', compact(
            'todaySales',
            'todaySalesCount',
            'pendingSales',
            'occupiedTables',
            'upcomingDeliveries',
            'pendingEncomendas',
            'pendingSalesCount',
            'occupiedTablesCount',
            'upcomingDeliveriesCount',
            'pendingEncomendasCount',
            'todayEncomendasCount',
            'todayEncomendasTotal',
            'recentSales',
            'lowStockItems',
            'criticalStockItems'
        ));
    }
}
