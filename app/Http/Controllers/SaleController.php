<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Customer;
use App\Models\Table;
use App\Models\Motoboy;
use App\Models\User;
use App\Models\CashRegister;
use App\Models\Product;
use App\Models\SaleItem;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SaleController extends Controller
{
    /**
     * Display the main POS interface
     */
    public function pos()
    {
        // Verificar se há caixa aberto
        $openCashRegister = CashRegister::where('status', 'aberto')->first();
        
        if (!$openCashRegister) {
            return redirect()->route('cash-registers.index')
                ->with('error', 'É necessário abrir o caixa antes de realizar vendas!');
        }

        // Buscar produtos ativos do dia
        $currentDay = now()->locale('pt_BR')->dayName;
        $dayMapping = [
            'Monday' => 'segunda',
            'Tuesday' => 'terca',
            'Wednesday' => 'quarta',
            'Thursday' => 'quinta',
            'Friday' => 'sexta',
            'Saturday' => 'sabado',
            'Sunday' => 'domingo',
        ];
        $currentDayPt = $dayMapping[$currentDay] ?? 'segunda';

        // Produtos do menu de hoje
        $products = Product::where('active', true)
            ->whereHas('menus', function($query) use ($currentDayPt) {
                $query->where('day_of_week', $currentDayPt)
                      ->where('available', true);
            })
            ->with('category')
            ->orderBy('category_id')
            ->orderBy('name')
            ->get();

        // Agrupar por categoria
        $categories = Category::where('active', true)
            ->whereHas('products', function($query) use ($currentDayPt) {
                $query->where('active', true)
                      ->whereHas('menus', function($q) use ($currentDayPt) {
                          $q->where('day_of_week', $currentDayPt)
                            ->where('available', true);
                      });
            })
            ->withCount(['products' => function($query) use ($currentDayPt) {
                $query->where('active', true)
                      ->whereHas('menus', function($q) use ($currentDayPt) {
                          $q->where('day_of_week', $currentDayPt)
                            ->where('available', true);
                      });
            }])
            ->orderBy('name')
            ->get();

        // Mesas disponíveis
        $tables = Table::where('active', true)
            ->where('status', 'disponivel')
            ->orderBy('number')
            ->get();

        // Mesas ocupadas com vendas pendentes
        $occupiedTables = Table::where('active', true)
            ->where('status', 'ocupada')
            ->with(['sales' => function($query) {
                $query->whereIn('status', ['pendente', 'em_preparo', 'pronto'])
                      ->with(['items.product', 'customer'])
                      ->latest();
            }])
            ->orderBy('number')
            ->get();

        // Clientes recentes
        $recentCustomers = Customer::orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Motoboys disponíveis
        $motoboys = Motoboy::where('active', true)
            ->orderBy('name')
            ->get();

        // Vendas pendentes do dia
        $pendingSales = Sale::where('cash_register_id', $openCashRegister->id)
            ->whereIn('status', ['pendente', 'em_preparo', 'pronto', 'saiu_entrega'])
            ->with(['customer', 'table', 'items.product', 'motoboy'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.sale.pos', compact(
            'openCashRegister',
            'products',
            'categories',
            'tables',
            'occupiedTables',
            'recentCustomers',
            'motoboys',
            'pendingSales',
            'currentDayPt'
        ));
    }

    /**
     * Display a listing of sales.
     */
    public function index(Request $request)
    {
        $query = Sale::with(['customer', 'table', 'motoboy', 'user', 'cashRegister']);

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by code or customer name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($customerQuery) use ($search) {
                      $customerQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $sales = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('sales.index', compact('sales'));
    }

    /**
     * Store a newly created sale (API for POS)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'table_id' => 'nullable|exists:tables,id',
            'motoboy_id' => 'nullable|exists:motoboys,id',
            'type' => 'required|in:balcao,delivery,encomenda',
            'payment_method' => 'nullable|in:dinheiro,cartao_credito,cartao_debito,pix,transferencia',
            'delivery_date' => 'nullable|date|after:today',
            'delivery_time' => 'nullable|date_format:H:i',
            'notes' => 'nullable|string',
            'delivery_address' => 'nullable|string',
            'delivery_fee' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.notes' => 'nullable|string',
            'discount' => 'nullable|numeric|min:0',
        ]);

        // Validações adicionais baseadas no tipo
        if ($validated['type'] === 'delivery') {
            if (empty($validated['delivery_address'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'O endereço de entrega é obrigatório para deliveries!'
                ], 422);
            }
            if (empty($validated['motoboy_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selecione um motoboy para fazer a entrega!'
                ], 422);
            }
        }

        if ($validated['type'] === 'encomenda') {
            if (empty($validated['delivery_date'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'A data de entrega é obrigatória para encomendas!'
                ], 422);
            }
            if (empty($validated['delivery_time'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'O horário de entrega é obrigatório para encomendas!'
                ], 422);
            }
        }

        // Check if there's an open cash register
        $openCashRegister = CashRegister::where('status', 'aberto')->first();
        if (!$openCashRegister) {
            return response()->json([
                'success' => false,
                'message' => 'É necessário abrir o caixa antes de realizar vendas!'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Calculate totals
            $subtotal = 0;
            $deliveryFee = $validated['delivery_fee'] ?? 0; // Use custom delivery fee

            // Determine initial status based on type
            $initialStatus = match($validated['type']) {
                'balcao' => 'pendente',
                'delivery' => 'pendente',
                'encomenda' => 'pendente',
                default => 'pendente'
            };

            // Create sale
            $sale = Sale::create([
                'cash_register_id' => $openCashRegister->id,
                'user_id' => Auth::id(),
                'customer_id' => $validated['customer_id'] ?? null,
                'table_id' => $validated['table_id'] ?? null,
                'motoboy_id' => $validated['motoboy_id'] ?? null,
                'type' => $validated['type'],
                'status' => $initialStatus,
                'subtotal' => 0,
                'discount' => $validated['discount'] ?? 0,
                'delivery_fee' => $deliveryFee,
                'total' => 0,
                'payment_method' => $validated['payment_method'] ?? null,
                'delivery_date' => $validated['delivery_date'] ?? null,
                'delivery_time' => $validated['delivery_time'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'delivery_address' => $validated['delivery_address'] ?? null,
            ]);

            // Create sale items
            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);
                $unitPrice = $product->price;
                $itemSubtotal = $unitPrice * $item['quantity'];
                $subtotal += $itemSubtotal;

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $unitPrice,
                    'subtotal' => $itemSubtotal,
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            // Calculate final total
            $total = $subtotal - ($validated['discount'] ?? 0) + $deliveryFee;
            
            // Update sale totals
            $sale->update([
                'subtotal' => $subtotal,
                'total' => $total,
            ]);

            // Update table status if it's a table order
            if ($sale->table_id) {
                $sale->table->update(['status' => 'ocupada']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Venda criada com sucesso!',
                'sale' => $sale->load(['items.product', 'customer', 'table'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar venda: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get POS data for a sale (for loading into POS interface)
     */
    public function getPosData(Sale $sale)
    {
        $sale->load(['customer', 'table', 'motoboy', 'user', 'cashRegister', 'items.product.category']);

        // Format items for POS
        $items = $sale->items->map(function ($item) {
            return [
                'product_id' => $item->product_id,
                'name' => $item->product->name,
                'price' => $item->unit_price,
                'category' => $item->product->category?->name ?? 'Sem categoria',
                'quantity' => $item->quantity,
                'subtotal' => $item->subtotal,
                'notes' => $item->notes ?? ''
            ];
        });

        // Return sale data formatted for POS
        return response()->json([
            'success' => true,
            'sale' => [
                'id' => $sale->id,
                'type' => $sale->type,
                'status' => $sale->status,
                'customer_id' => $sale->customer_id,
                'table_id' => $sale->table_id,
                'motoboy_id' => $sale->motoboy_id,
                'payment_method' => $sale->payment_method,
                'delivery_date' => $sale->delivery_date,
                'delivery_time' => $sale->delivery_time,
                'delivery_address' => $sale->delivery_address,
                'delivery_fee' => $sale->delivery_fee,
                'discount' => $sale->discount,
                'notes' => $sale->notes,
                'subtotal' => $sale->subtotal,
                'total' => $sale->total,
                'items' => $items
            ]
        ]);
    }

    /**
     * Display the specified sale.
     */
    public function show(Sale $sale)
    {
        $sale->load(['customer', 'table', 'motoboy', 'user', 'cashRegister', 'items.product.category']);
        return view('sales.show', compact('sale'));
    }

    /**
     * Update sale status (AJAX)
     */
    public function updateStatus(Request $request, Sale $sale)
    {
        $validated = $request->validate([
            'status' => 'required|in:pendente,em_preparo,pronto,saiu_entrega,entregue,cancelado,finalizado',
            'notes' => 'nullable|string',
        ]);

        $sale->update($validated);

        // Update table status when sale is finalized
        if ($sale->status === 'finalizado' && $sale->table_id) {
            $sale->table->update(['status' => 'disponivel']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Status atualizado com sucesso!',
            'sale' => $sale->load(['items.product', 'customer', 'table'])
        ]);
    }

    /**
     * Cancel sale
     */
    public function cancel(Sale $sale)
    {
        if ($sale->isCancelado() || $sale->isFinalizado()) {
            return response()->json([
                'success' => false,
                'message' => 'Apenas vendas pendentes ou em preparo podem ser canceladas!'
            ], 400);
        }

        $sale->update(['status' => 'cancelado']);

        // Free up the table
        if ($sale->table_id) {
            $sale->table->update(['status' => 'disponivel']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Venda cancelada com sucesso!'
        ]);
    }

    /**
     * Finalize sale (close table/complete payment)
     */
    public function finalize(Request $request, Sale $sale)
    {
        $validated = $request->validate([
            'payment_method' => 'required|in:dinheiro,cartao_credito,cartao_debito,pix,transferencia',
            'discount' => 'nullable|numeric|min:0',
        ]);

        if ($sale->isCancelado() || $sale->isFinalizado()) {
            return response()->json([
                'success' => false,
                'message' => 'Esta venda já foi finalizada ou cancelada!'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Update sale
            $total = $sale->subtotal - ($validated['discount'] ?? 0) + $sale->delivery_fee;
            
            $sale->update([
                'status' => 'finalizado',
                'payment_method' => $validated['payment_method'],
                'discount' => $validated['discount'] ?? 0,
                'total' => $total,
            ]);

            // Free up the table
            if ($sale->table_id) {
                $sale->table->update(['status' => 'disponivel']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Venda finalizada com sucesso!',
                'sale' => $sale->load(['items.product', 'customer', 'table'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao finalizar venda: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get sales statistics
     */
    public function statistics(Request $request)
    {
        $period = $request->get('period', 'today');

        $query = Sale::where('status', '!=', 'cancelado');

        switch ($period) {
            case 'today':
                $query->whereDate('created_at', today());
                break;
            case 'week':
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('created_at', now()->month)
                      ->whereYear('created_at', now()->year);
                break;
        }

        $totalSales = $query->count();
        $totalRevenue = $query->sum('total');
        $totalDelivery = $query->where('type', 'delivery')->count();
        $totalBalcao = $query->where('type', 'balcao')->count();
        $totalEncomenda = $query->where('type', 'encomenda')->count();

        return response()->json([
            'total_sales' => $totalSales,
            'total_revenue' => $totalRevenue,
            'total_delivery' => $totalDelivery,
            'total_balcao' => $totalBalcao,
            'total_encomenda' => $totalEncomenda,
        ]);
    }
}
