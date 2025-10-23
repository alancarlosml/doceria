<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\CashRegister;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    /**
     * Display a listing of expenses.
     */
    public function index(Request $request)
    {
        $query = Expense::with(['user']);

        // Filter by type (entrada/saida)
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        // Search by description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('description', 'like', "%{$search}%");
        }

        $expenses = $query->orderBy('date', 'desc')->paginate(15);

        return view('admin.expenses', compact('expenses'));
    }

    /**
     * Show the form for creating a new expense.
     */
    public function create()
    {
        $isEditing = false;

        return view('admin.expense-form', compact( 'isEditing'));
    }

    /**
     * Store a newly created expense.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:entrada,saida',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'payment_method' => 'nullable|in:dinheiro,cartao_credito,cartao_debito,pix,transferencia,boleto',
            'notes' => 'nullable|string',
        ]);

        $validated['user_id'] = Auth::id();

        Expense::create($validated);

        return redirect()->route('expenses.index')
            ->with('success', 'Despesa registrada com sucesso!');
    }

    /**
     * Display the specified expense.
     */
    public function show(Expense $expense)
    {
        return view('expenses.show', compact('expense'));
    }

    /**
     * Show the form for editing the specified expense.
     */
    public function edit(Expense $expense)
    {
        $isEditing = true;

        return view('admin.expense-form', compact('expense', 'isEditing'));
    }

    /**
     * Update the specified expense.
     */
    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'type' => 'required|in:entrada,saida',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'payment_method' => 'nullable|in:dinheiro,cartao_credito,cartao_debito,pix,transferencia,boleto',
            'notes' => 'nullable|string',
        ]);

        $expense->update($validated);

        return redirect()->route('expenses.index')
            ->with('success', 'Despesa atualizada com sucesso!');
    }

    /**
     * Remove the specified expense.
     */
    public function destroy(Expense $expense)
    {
        $expense->delete();

        return redirect()->route('expenses.index')
            ->with('success', 'Despesa excluída com sucesso!');
    }

    /**
     * Get expenses statistics.
     */
    public function statistics(Request $request)
    {
        $period = $request->get('period', 'month');

        $query = Expense::query();

        switch ($period) {
            case 'today':
                $query->whereDate('date', today());
                break;
            case 'week':
                $query->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('date', now()->month)
                      ->whereYear('date', now()->year);
                break;
        }

        $totalExpenses = $query->sum('amount');
        $totalRevenues = $query->where('type', 'entrada')->sum('amount');
        $totalSaidas = $query->where('type', 'saida')->sum('amount');

        return response()->json([
            'total_expenses' => $totalExpenses,
            'total_revenues' => $totalRevenues,
            'total_saidas' => $totalSaidas,
            'net_result' => $totalRevenues - $totalSaidas,
        ]);
    }
}
