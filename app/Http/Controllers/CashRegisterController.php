<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
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

        // Filter by status
        if ($request->filled('status') && $request->status !== 'todos') {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('opened_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('opened_at', '<=', $request->date_to);
        }

        $cashRegisters = $query->orderBy('created_at', 'desc')->paginate(15);
        $openRegister = CashRegister::where('status', 'aberto')->first();

        return view('admin.cash_register.cash_registers', compact('cashRegisters', 'openRegister'));
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

        return view('admin.cash_register.cash_register-form', ['cashRegister' => null, 'isEditing' => false]);
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
        $cashRegister->load(['user', 'sales.customer', 'sales.items.product', 'expenses']);

        // Estatísticas do caixa
        $totalSales = $cashRegister->getTotalSales();
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

        return view('admin.cash_register.cash_register-show', compact(
            'cashRegister',
            'totalSales',
            'totalExpenses',
            'totalRevenues',
            'expectedBalance',
            'salesCount',
            'expensesCount',
            'revenuesCount',
            'topSale',
            'paymentMethods'
        ));
    }

    /**
     * Show the form for editing the specified cash register.
     */
    public function edit(CashRegister $cashRegister)
    {
        // Only allow editing open registers
        if ($cashRegister->status !== 'aberto') {
            return redirect()->route('cash-registers.show', $cashRegister)
                ->with('error', 'Apenas caixas abertos podem ser editados!');
        }

        return view('admin.cash_register.cash_register-form', ['cashRegister' => $cashRegister, 'isEditing' => true]);
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
     * Close the specified cash register.
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
        ]);

        $cashRegister->update([
            'closing_balance' => $validated['closing_balance'],
            'closed_at' => now(),
            'status' => 'fechado',
            'closing_notes' => $validated['closing_notes'],
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
        $totalExpenses = $openRegister->getTotalExpenses();
        $totalRevenues = $openRegister->getTotalRevenues();
        $expectedBalance = $openRegister->getExpectedBalance();

        return response()->json([
            'has_open_register' => true,
            'opening_balance' => $openRegister->opening_balance,
            'total_sales' => $totalSales,
            'total_expenses' => $totalExpenses,
            'total_revenues' => $totalRevenues,
            'expected_balance' => $expectedBalance,
            'current_balance' => $openRegister->closing_balance ?? $expectedBalance,
        ]);
    }
}
