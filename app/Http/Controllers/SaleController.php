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
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
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
     * Show the form for creating a new sale.
     */
    public function create(Request $request)
    {
        // Check if there's an open cash register
        $openCashRegister = CashRegister::where('status', 'aberto')->first();

        if (!$openCashRegister) {
            return redirect()->route('cash-registers.index')
                ->with('error', 'É necessário abrir o caixa antes de realizar vendas!');
        }

        $customers = Customer::orderBy('name')->get();
        $tables = Table::where('active', true)->where('status', 'disponivel')->orderBy('number')->get();
        $motoboys = Motoboy::where('active', true)->orderBy('name')->get();
        $products = Product::where('active', true)->with('category')->get();

        // Get current day of week for menu
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

        return view('sales.create', compact(
            'customers',
            'tables',
            'motoboys',
            'products',
            'openCashRegister',
            'currentDayPt'
        ));
    }

    /**
     * Store a newly created sale.
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
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.notes' => 'nullable|string',
        ]);

        // Check if there's an open cash register
        $openCashRegister = CashRegister::where('status', 'aberto')->first();
        if (!$openCashRegister) {
            return back()->withInput()
                ->with('error', 'É necessário abrir o caixa antes de realizar vendas!');
        }

        DB::transaction(function() use ($validated, $openCashRegister) {
            // Calculate totals
            $subtotal = 0;
            $deliveryFee = 0;

            // Calculate delivery fee for delivery orders
            if ($validated['type'] === 'delivery') {
                $deliveryFee = 5.00; // Default delivery fee
            }

            // Create sale
            $sale = Sale::create([
                'cash_register_id' => $openCashRegister->id,
                'user_id' => Auth::id(),
                'customer_id' => $validated['customer_id'],
                'table_id' => $validated['table_id'],
                'motoboy_id' => $validated['motoboy_id'],
                'type' => $validated['type'],
                'status' => 'pendente',
                'subtotal' => 0, // Will be calculated from items
                'discount' => 0,
                'delivery_fee' => $deliveryFee,
                'total' => 0, // Will be calculated
                'payment_method' => $validated['payment_method'],
                'delivery_date' => $validated['delivery_date'],
                'delivery_time' => $validated['delivery_time'],
                'notes' => $validated['notes'],
                'delivery_address' => $validated['delivery_address'],
            ]);

            // Create sale items and calculate subtotal
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

            // Update sale totals
            $total = $subtotal + $deliveryFee;
            $sale->update([
                'subtotal' => $subtotal,
                'total' => $total,
            ]);

            // Update table status if it's a table order
            if ($sale->table_id) {
                $sale->table->update(['status' => 'ocupada']);
            }
        });

        return redirect()->route('sales.show', $sale ?? 1)
            ->with('success', 'Venda criada com sucesso!');
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
     * Show the form for editing the specified sale.
     */
    public function edit(Sale $sale)
    {
        // Only allow editing pending sales
        if (!$sale->isPendente()) {
            return redirect()->route('sales.show', $sale)
                ->with('error', 'Apenas vendas pendentes podem ser editadas!');
        }

        $customers = Customer::orderBy('name')->get();
        $tables = Table::where('active', true)->orderBy('number')->get();
        $motoboys = Motoboy::where('active', true)->orderBy('name')->get();
        $products = Product::where('active', true)->with('category')->get();

        return view('sales.edit', compact('sale', 'customers', 'tables', 'motoboys', 'products'));
    }

    /**
     * Update the specified sale.
     */
    public function update(Request $request, Sale $sale)
    {
        // Only allow updating pending sales
        if (!$sale->isPendente()) {
            return back()->with('error', 'Apenas vendas pendentes podem ser editadas!');
        }

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
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.notes' => 'nullable|string',
        ]);

        DB::transaction(function() use ($validated, $sale) {
            // Calculate delivery fee
            $deliveryFee = 0;
            if ($validated['type'] === 'delivery') {
                $deliveryFee = 5.00;
            }

            // Delete existing items
            $sale->items()->delete();

            // Calculate new subtotal
            $subtotal = 0;
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

            // Update sale
            $total = $subtotal + $deliveryFee;
            $sale->update([
                'customer_id' => $validated['customer_id'],
                'table_id' => $validated['table_id'],
                'motoboy_id' => $validated['motoboy_id'],
                'type' => $validated['type'],
                'payment_method' => $validated['payment_method'],
                'delivery_date' => $validated['delivery_date'],
                'delivery_time' => $validated['delivery_time'],
                'notes' => $validated['notes'],
                'delivery_address' => $validated['delivery_address'],
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'total' => $total,
            ]);

            // Update table status
            if ($sale->table_id) {
                $sale->table->update(['status' => 'ocupada']);
            }
        });

        return redirect()->route('sales.show', $sale)
            ->with('success', 'Venda atualizada com sucesso!');
    }

    /**
     * Update sale status.
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
            'message' => 'Status da venda atualizado com sucesso!'
        ]);
    }

    /**
     * Cancel the specified sale.
     */
    public function cancel(Sale $sale)
    {
        if ($sale->isCancelado() || $sale->isFinalizado()) {
            return back()->with('error', 'Apenas vendas pendentes ou em preparo podem ser canceladas!');
        }

        $sale->update(['status' => 'cancelado']);

        // Free up the table if it was a table order
        if ($sale->table_id) {
            $sale->table->update(['status' => 'disponivel']);
        }

        return redirect()->route('sales.show', $sale)
            ->with('success', 'Venda cancelada com sucesso!');
    }

    /**
     * Get sales statistics.
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
