<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Product;
use App\Models\Category;
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
        $menuByCategory = $menuItems->groupBy(function($item) {
            return $item->product->category->name ?? 'Outros';
        });

        // Get categories for navigation
        $categories = Category::where('active', true)->orderBy('name')->get();

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
        // This will be protected by auth middleware
        return view('gestor.dashboard');
    }
}