<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\Encomenda;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CashRegisterController extends Controller
{
    /**
     * Display a listing of cash registers.
     */
    public function index(Request $request)
    {
        $query = CashRegister::with('user');

        // Determinar período do filtro
        $periodo = $request->get('periodo', 'mes_atual');
        $dateFrom = null;
        $dateTo = null;

        switch ($periodo) {
            case 'mes_atual':
                $dateFrom = now()->startOfMonth()->format('Y-m-d');
                $dateTo = now()->endOfMonth()->format('Y-m-d');
                break;
            case 'mes_anterior':
                $dateFrom = now()->subMonth()->startOfMonth()->format('Y-m-d');
                $dateTo = now()->subMonth()->endOfMonth()->format('Y-m-d');
                break;
            case 'customizado':
                $dateFrom = $request->get('date_from');
                $dateTo = $request->get('date_to');
                break;
        }

        // Filter by status
        if ($request->filled('status') && $request->status !== 'todos') {
            $query->where('status', $request->status);
        }

        // Filter by date range (período)
        if ($dateFrom) {
            $query->whereDate('opened_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('opened_at', '<=', $dateTo);
        }

        $cashRegisters = $query->orderBy('opened_at', 'desc')->paginate(15);
        $openRegister = CashRegister::where('status', 'aberto')->first();

        // Calcular total de encomendas finalizadas no período (sem taxa de entrega)
        $encomendasQuery = Encomenda::where('status', 'entregue');
        if ($dateFrom) {
            $encomendasQuery->whereDate('updated_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $encomendasQuery->whereDate('updated_at', '<=', $dateTo);
        }
        
        $totalEncomendasPeriodo = $encomendasQuery->selectRaw('SUM(total - COALESCE(delivery_fee, 0)) as total_sem_taxa')->value('total_sem_taxa') ?? 0;
        $countEncomendasPeriodo = Encomenda::where('status', 'entregue')
            ->when($dateFrom, fn($q) => $q->whereDate('updated_at', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('updated_at', '<=', $dateTo))
            ->count();

        return view('admin.cash_register.cash_registers', compact(
            'cashRegisters', 
            'openRegister', 
            'periodo', 
            'dateFrom', 
            'dateTo',
            'totalEncomendasPeriodo',
            'countEncomendasPeriodo'
        ));
    }

    /**
     * Show the form for creating a new cash register.
     */
    public function create()
    {
        // Check if there's already an open register
        $openRegister = CashRegister::where('status', 'aberto')->first();
        if ($openRegister) {
            return redirect()->route('cash-registers.index')
                ->with('error', 'Já existe um caixa aberto!');
        }

        return view('admin.cash_register.cash_register-form', ['cashRegister' => null, 'isEditing' => false, 'isClosing' => false]);
    }

    /**
     * Store a newly created cash register.
     */
    public function store(Request $request)
    {
        // Check if there's already an open register
        $openRegister = CashRegister::where('status', 'aberto')->first();
        if ($openRegister) {
            return back()->with('error', 'Já existe um caixa aberto!');
        }

        $validated = $request->validate([
            'opening_balance' => 'required|numeric|min:0',
            'opening_notes' => 'nullable|string',
        ]);

        CashRegister::create([
            'user_id' => Auth::id(),
            'opening_balance' => $validated['opening_balance'],
            'status' => 'aberto',
            'opened_at' => now(),
            'opening_notes' => $validated['opening_notes'],
        ]);

        return redirect()->route('cash-registers.index')
            ->with('success', 'Caixa aberto com sucesso!');
    }

    /**
     * Display the specified cash register.
     */
    public function show(CashRegister $cashRegister)
    {
        $cashRegister->load(['user', 'sales.customer', 'sales.items.product', 'sales.motoboy', 'expenses']);

        // Estatísticas do caixa
        $totalSales = $cashRegister->getTotalSales();
        $totalEncomendas = $cashRegister->getTotalEncomendas();
        $totalExpenses = $cashRegister->getTotalExpenses();
        $totalRevenues = $cashRegister->getTotalRevenues();
        $expectedBalance = $cashRegister->getExpectedBalance();

        // Estatísticas adicionais
        $salesCount = $cashRegister->sales()->whereNotIn('status', ['cancelado'])->count();
        $expensesCount = $cashRegister->expenses()->where('type', 'saida')->count();
        $revenuesCount = $cashRegister->expenses()->where('type', 'entrada')->count();

        // Maior venda do dia
        $topSale = $cashRegister->sales()->whereNotIn('status', ['cancelado'])->orderBy('total', 'desc')->first();

        // Método de pagamento mais usado
        $paymentMethods = $cashRegister->sales()
            ->whereNotIn('status', ['cancelado'])
            ->selectRaw('payment_method, COUNT(*) as count, SUM(total) as total')
            ->groupBy('payment_method')
            ->orderBy('count', 'desc')
            ->first();

        // Calcular resumo diário do caixa
        $dailySummary = $this->calculateDailySummaryForRegister($cashRegister, $topSale, $paymentMethods->payment_method ?? null, $paymentMethods->count ?? 0);

        return view('admin.cash_register.cash_register-show', compact(
            'cashRegister',
            'totalSales',
            'totalEncomendas',
            'totalExpenses',
            'totalRevenues',
            'expectedBalance',
            'salesCount',
            'expensesCount',
            'revenuesCount',
            'topSale',
            'paymentMethods',
            'dailySummary'
        ));
    }

    /**
     * Show the form for closing the open cash register (entry point from menu).
     */
    public function closeForm()
    {
        $cashRegister = CashRegister::where('status', 'aberto')->first();

        if (!$cashRegister) {
            return redirect()->route('cash-registers.index', ['list' => 1])
                ->with('error', 'Nenhum caixa aberto para fechar!');
        }

        // Get pending orders for this register
        $pendingOrders = $cashRegister->sales()->where('status', 'pendente')->with(['customer', 'items.product'])->get();

        // Get alternative open registers for transfer (excluding current)
        $transferRegisters = CashRegister::where('status', 'aberto')
            ->where('id', '!=', $cashRegister->id)
            ->get();

        // Calculate expected balance (including pending orders)
        $expectedTotal = $cashRegister->getExpectedBalance();

        return view('admin.cash_register.cash_register-form', [
            'cashRegister' => $cashRegister,
            'isEditing' => false,
            'isClosing' => true,
            'pendingOrders' => $pendingOrders,
            'transferRegisters' => $transferRegisters,
            'expectedTotal' => $expectedTotal,
        ]);
    }

    /**
     * Calculate daily summary for cash register show page
     */
    private function calculateDailySummaryForRegister(CashRegister $cashRegister, $topSale = null, $topPaymentMethod = null, $topPaymentCount = 0)
    {
        // Usar todas as vendas finalizadas do caixa (já carregadas)
        $completedSales = $cashRegister->sales->whereNotIn('status', ['cancelado']);

        // Agrupar formas de pagamento
        $paymentMethods = $completedSales->groupBy('payment_method')->map(function($sales, $method) {
            return [
                'method' => $method,
                'count' => $sales->count(),
                'total' => $sales->sum('total')
            ];
        })->values();

        // Encomendas entregues vinculadas a este caixa
        $finalizedOrders = Encomenda::where('status', 'entregue')
            ->where('cash_register_id', $cashRegister->id)
            ->get();

        // Encomendas (delivery) - vendas do tipo 'delivery'
        $deliveryOrders = $completedSales->filter(function($sale) {
            return $sale->type === 'delivery';
        });

        // Valores por motoboy (para entregas)
        $motoboyEarnings = [];
        if ($deliveryOrders->isNotEmpty()) {
            foreach ($deliveryOrders as $order) {
                $motoboyId = $order->motoboy_id ?? null;
                if ($motoboyId) {
                    if (!isset($motoboyEarnings[$motoboyId])) {
                        $motoboyEarnings[$motoboyId] = [
                            'name' => $order->motoboy ? $order->motoboy->name : 'Motoboy #' . $motoboyId,
                            'orders_count' => 0,
                            'total_value' => 0,
                        ];
                    }
                    $motoboyEarnings[$motoboyId]['orders_count']++;
                    $motoboyEarnings[$motoboyId]['total_value'] += $order->delivery_fee;
                }
            }
        }

        // Mapear métodos de pagamento para resumo
        $paymentSummary = [
            'pix' => $paymentMethods->firstWhere('method', 'pix')['total'] ?? 0,
            'cartao' => ($paymentMethods->firstWhere('method', 'cartao_credito')['total'] ?? 0) +
                       ($paymentMethods->firstWhere('method', 'cartao_debito')['total'] ?? 0) +
                       ($paymentMethods->firstWhere('method', 'cartao')['total'] ?? 0),
            'dinheiro' => $paymentMethods->firstWhere('method', 'dinheiro')['total'] ?? 0,
            'outros' => $paymentMethods->whereNotIn('method', ['pix', 'cartao_credito', 'cartao_debito', 'cartao', 'dinheiro'])->sum('total')
        ];

        // Determinar data do caixa (abertura) para exibição
        $caixaDateFormatted = $cashRegister->opened_at->format('d/m/Y');

        // Calcular totais usando dados do caixa
        $totalSales = $cashRegister->getTotalSales();
        $totalExpenses = $cashRegister->getTotalExpenses();
        $totalRevenues = $cashRegister->getTotalRevenues();
        
        // Total de encomendas finalizadas
        $totalFinalizedOrders = $finalizedOrders->sum('total');
        
        // Vendas (todas as vendas são contabilizadas, encomendas são separadas)
        $salesExcludingOrders = $completedSales->sum('total');
        
        // Resultado Final = Saldo Inicial + Vendas (excluindo encomendas) + Encomendas Finalizadas - Despesas + Receitas
        $finalResult = $cashRegister->opening_balance + 
                      $salesExcludingOrders + 
                      $totalFinalizedOrders + 
                      $totalRevenues - 
                      $totalExpenses;

        // Total de entregas sem as taxas (subtotal - desconto, sem delivery_fee)
        $deliveryOrdersTotalWithoutFees = $deliveryOrders->sum(function($order) {
            return $order->subtotal - ($order->discount ?? 0);
        });

        return [
            'date' => $caixaDateFormatted,
            'total_sales' => $totalSales,
            'sales_count' => $completedSales->count(),
            'payment_methods' => $paymentSummary,
            'paid_orders_count' => $finalizedOrders->count(),
            'paid_orders_total' => $totalFinalizedOrders,
            'delivery_orders_count' => $deliveryOrders->count(),
            'delivery_orders_total' => $deliveryOrdersTotalWithoutFees,
            'motoboy_earnings' => array_values($motoboyEarnings),
            'opening_balance' => $cashRegister->opening_balance,
            'current_expected' => $cashRegister->getExpectedBalance(),
            'final_result' => $finalResult,
            'total_expenses' => $totalExpenses,
            'total_revenues' => $totalRevenues,
        ];
    }

    /**
     * Calculate daily summary for cash register closing
     */
    private function calculateDailySummary(CashRegister $cashRegister)
    {
        $today = now()->toDateString();

        // Vendas finalizadas do dia
        $completedSales = $cashRegister->sales()
            ->whereNotIn('status', ['cancelado'])
            ->whereDate('created_at', $today)
            ->with(['customer', 'items.product', 'motoboy'])
            ->get();

        // Formas de pagamento
        $paymentMethods = $completedSales->groupBy('payment_method')->map(function($sales, $method) {
            return [
                'method' => $method,
                'count' => $sales->count(),
                'total' => $sales->sum('total')
            ];
        })->values();

        // Encomendas (delivery) - vendas do tipo 'delivery'
        $deliveryOrders = $completedSales->filter(function($sale) {
            return $sale->type === 'delivery';
        });

        // Valores por motoboy (para entregas)
        $motoboyEarnings = [];
        foreach ($deliveryOrders as $order) {
            $motoboyId = $order->motoboy_id ?? null;
            if ($motoboyId) {
                if (!isset($motoboyEarnings[$motoboyId])) {
                    $motoboy = $order->motoboy; // Assumindo relacionamento
                    $motoboyEarnings[$motoboyId] = [
                        'name' => $motoboy ? $motoboy->name : 'Motoboy #' . $motoboyId,
                        'orders_count' => 0,
                        'total_value' => 0,
                    ];
                }
                $motoboyEarnings[$motoboyId]['orders_count']++;
                $motoboyEarnings[$motoboyId]['total_value'] += $order->total;
            }
        }

        // Mapear métodos de pagamento para resumo
        $paymentSummary = [
            'pix' => $paymentMethods->firstWhere('method', 'pix')['total'] ?? 0,
            'cartao' => ($paymentMethods->firstWhere('method', 'cartao_credito')['total'] ?? 0) +
                       ($paymentMethods->firstWhere('method', 'cartao_debito')['total'] ?? 0),
            'dinheiro' => $paymentMethods->firstWhere('method', 'dinheiro')['total'] ?? 0,
            'outros' => $paymentMethods->whereNotIn('method', ['pix', 'cartao_credito', 'cartao_debito', 'dinheiro'])->sum('total')
        ];

        // Total de entregas sem as taxas (subtotal - desconto, sem delivery_fee)
        $deliveryOrdersTotalWithoutFees = $deliveryOrders->sum(function($order) {
            return $order->subtotal - ($order->discount ?? 0);
        });

        return [
            'date' => now()->format('d/m/Y'),
            'total_sales' => $completedSales->sum('total'),
            'sales_count' => $completedSales->count(),
            'payment_methods' => $paymentSummary,
            'paid_orders_count' => $completedSales->count(), // Todas são pagas/finalizadas
            'paid_orders_total' => $completedSales->sum('total'),
            'delivery_orders_count' => $deliveryOrders->count(),
            'delivery_orders_total' => $deliveryOrdersTotalWithoutFees,
            'motoboy_earnings' => array_values($motoboyEarnings),
            'opening_balance' => $cashRegister->opening_balance,
            'current_expected' => $cashRegister->getExpectedBalance(),
        ];
    }

    /**
     * Show the form for editing the specified cash register (used from list view).
     */
    public function edit(CashRegister $cashRegister)
    {
        // Only allow editing open registers
        if ($cashRegister->status !== 'aberto') {
            return redirect()->route('cash-registers.show', $cashRegister)
                ->with('error', 'Apenas caixas abertos podem ser editados!');
        }

        return view('admin.cash_register.cash_register-form', [
            'cashRegister' => $cashRegister,
            'isEditing' => true,
            'isClosing' => false
        ]);
    }

    /**
     * Update the specified cash register.
     */
    public function update(Request $request, CashRegister $cashRegister)
    {
        // Only allow updating open registers
        if ($cashRegister->status !== 'aberto') {
            return back()->with('error', 'Apenas caixas abertos podem ser editados!');
        }

        $validated = $request->validate([
            'opening_balance' => 'required|numeric|min:0',
            'opening_notes' => 'nullable|string',
        ]);

        $cashRegister->update($validated);

        return redirect()->route('cash-registers.show', $cashRegister)
            ->with('success', 'Caixa atualizado com sucesso!');
    }

    /**
     * Close the specified cash register with smart handling of pending orders.
     */
    public function close(Request $request, CashRegister $cashRegister)
    {
        // Only allow closing open registers
        if ($cashRegister->status !== 'aberto') {
            return back()->with('error', 'Apenas caixas abertos podem ser fechados!');
        }

        $validated = $request->validate([
            'closing_balance' => 'required|numeric|min:0',
            'closing_notes' => 'nullable|string',
            'pending_action' => 'required|string|in:allow_close,finalize_all,cancel_all,transfer',
            'transfer_to_register' => 'nullable|integer|exists:cash_registers,id',
        ]);

        // Check for pending orders
        $pendingOrders = $cashRegister->sales()->where('status', 'pendente')->get();

        if ($pendingOrders->count() > 0) {
            // Handle pending orders based on user choice
            switch ($validated['pending_action']) {
                case 'finalize_all':
                    // Automatically finalize all pending orders as paid
                    foreach ($pendingOrders as $order) {
                        // Convert to finalizado (assuming payment was completed)
                        $order->update([
                            'status' => 'finalizado',
                            'payment_method' => 'dinheiro', // Default method
                            'finalized_at' => now(),
                        ]);
                    }
                    $closingNotesSuffix = "\n\n• Finalizados automaticamente: {$pendingOrders->count()} pedidos pendentes.";
                    break;

                case 'cancel_all':
                    // Cancel all pending orders
                    foreach ($pendingOrders as $order) {
                        $order->update(['status' => 'cancelado']);
                    }
                    $closingNotesSuffix = "\n\n• Cancelados automaticamente: {$pendingOrders->count()} pedidos pendentes.";
                    break;

                case 'transfer':
                    // Transfer pending orders to another register
                    if (!$validated['transfer_to_register']) {
                        return back()->with('error', 'Selecione um caixa para transferir os pedidos pendentes.');
                    }

                    $transferRegister = CashRegister::find($validated['transfer_to_register']);
                    if (!$transferRegister || $transferRegister->status !== 'aberto') {
                        return back()->with('error', 'O caixa selecionado para transferência não está aberto.');
                    }

                    foreach ($pendingOrders as $order) {
                        $order->update(['cash_register_id' => $transferRegister->id]);
                    }
                    $closingNotesSuffix = "\n\n• Transferidos para caixa #{$transferRegister->id}: {$pendingOrders->count()} pedidos pendentes.";
                    break;

                case 'allow_close':
                    // Allow closing despite pending orders
                    $closingNotesSuffix = "\n\n• ⚠️ Fechamento com {$pendingOrders->count()} pedidos pendentes (não finalizados).";
                    break;

                default:
                    return back()->with('error', 'Ação para pedidos pendentes inválida.');
            }
        } else {
            $closingNotesSuffix = '';
        }

        // Close the cash register
        $cashRegister->update([
            'closing_balance' => $validated['closing_balance'],
            'closed_at' => now(),
            'status' => 'fechado',
            'closing_notes' => ($validated['closing_notes'] ?? '') . $closingNotesSuffix,
        ]);

        return redirect()->route('cash-registers.show', $cashRegister)
            ->with('success', 'Caixa fechado com sucesso!');
    }

    /**
     * Get cash register statistics.
     */
    public function sales(Request $request, CashRegister $cashRegister)
    {
        $query = $cashRegister->sales()->with(['customer', 'items.product', 'user']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by customer name or code
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        $sales = $query->orderBy('created_at', 'desc')->paginate(15);

        // Estatísticas das vendas do caixa
        $stats = [
            'total_sales_count' => $cashRegister->sales()->whereNotIn('status', ['cancelado'])->count(),
            'total_sales_amount' => $cashRegister->sales()->whereNotIn('status', ['cancelado'])->sum('total'),
            'cancelled_sales_count' => $cashRegister->sales()->where('status', 'cancelado')->count(),
            'cancelled_sales_amount' => $cashRegister->sales()->where('status', 'cancelado')->sum('total'),
        ];

        return view('admin.cash_register.cash_register_sales', compact('cashRegister', 'sales', 'stats'));
    }

    /**
     * Get cash register statistics.
     */
    public function statistics()
    {
        $openRegister = CashRegister::where('status', 'aberto')->first();

        if (!$openRegister) {
            return response()->json([
                'has_open_register' => false,
                'message' => 'Nenhum caixa aberto',
            ]);
        }

        $totalSales = $openRegister->getTotalSales();
        $totalEncomendas = $openRegister->getTotalEncomendas();
        $totalExpenses = $openRegister->getTotalExpenses();
        $totalRevenues = $openRegister->getTotalRevenues();
        $expectedBalance = $openRegister->getExpectedBalance();

        return response()->json([
            'has_open_register' => true,
            'opening_balance' => $openRegister->opening_balance,
            'total_sales' => $totalEncomendas,
            'total_encomendas' => $totalSales,
            'total_expenses' => $totalExpenses,
            'total_revenues' => $totalRevenues,
            'expected_balance' => $expectedBalance,
            'current_balance' => $openRegister->closing_balance ?? $expectedBalance,
        ]);
    }
}
