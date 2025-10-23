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
    public function index()
    {
        $cashRegisters = CashRegister::with('user')->orderBy('created_at', 'desc')->get();
        $openRegister = CashRegister::where('status', 'aberto')->first();
        return view('cash_registers.index', compact('cashRegisters', 'openRegister'));
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

        return view('cash_registers.create');
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
        $cashRegister->load('user', 'sales', 'expenses');
        $totalSales = $cashRegister->getTotalSales();
        $totalExpenses = $cashRegister->getTotalExpenses();
        $totalRevenues = $cashRegister->getTotalRevenues();
        $expectedBalance = $cashRegister->getExpectedBalance();

        return view('cash_registers.show', compact(
            'cashRegister',
            'totalSales',
            'totalExpenses',
            'totalRevenues',
            'expectedBalance'
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

        return view('cash_registers.edit', compact('cashRegister'));
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
