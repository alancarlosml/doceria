<?php

namespace App\Http\Controllers;

use App\Models\Table;
use Illuminate\Http\Request;

class TableController extends Controller
{
    /**
     * Display a listing of tables.
     */
    public function index()
    {
        $tables = Table::orderBy('number')->get();
        return view('tables.index', compact('tables'));
    }

    /**
     * Show the form for creating a new table.
     */
    public function create()
    {
        return view('tables.create');
    }

    /**
     * Store a newly created table.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'number' => 'required|string|max:255|unique:tables',
            'capacity' => 'required|integer|min:1|max:20',
            'status' => 'required|in:disponivel,ocupada,reservada',
            'active' => 'boolean',
        ]);

        $validated['active'] = $request->has('active');

        Table::create($validated);

        return redirect()->route('tables.index')
            ->with('success', 'Mesa criada com sucesso!');
    }

    /**
     * Display the specified table.
     */
    public function show(Table $table)
    {
        $currentSale = $table->sales()->where('status', '!=', 'finalizado')->where('status', '!=', 'cancelado')->first();
        return view('tables.show', compact('table', 'currentSale'));
    }

    /**
     * Show the form for editing the specified table.
     */
    public function edit(Table $table)
    {
        return view('tables.edit', compact('table'));
    }

    /**
     * Update the specified table.
     */
    public function update(Request $request, Table $table)
    {
        $validated = $request->validate([
            'number' => 'required|string|max:255|unique:tables,number,' . $table->id,
            'capacity' => 'required|integer|min:1|max:20',
            'status' => 'required|in:disponivel,ocupada,reservada',
            'active' => 'boolean',
        ]);

        $validated['active'] = $request->has('active');

        $table->update($validated);

        return redirect()->route('tables.index')
            ->with('success', 'Mesa atualizada com sucesso!');
    }

    /**
     * Remove the specified table.
     */
    public function destroy(Table $table)
    {
        // Check if table has active sales
        $activeSales = $table->sales()->where('status', '!=', 'finalizado')->where('status', '!=', 'cancelado')->count();
        if ($activeSales > 0) {
            return redirect()->route('tables.index')
                ->with('error', 'Não é possível excluir mesa que possui vendas ativas!');
        }

        $table->delete();

        return redirect()->route('tables.index')
            ->with('success', 'Mesa excluída com sucesso!');
    }

    /**
     * Toggle table active status.
     */
    public function toggleStatus(Table $table)
    {
        $table->update(['active' => !$table->active]);

        return response()->json([
            'success' => true,
            'active' => $table->active,
            'message' => $table->active ? 'Mesa ativada!' : 'Mesa desativada!'
        ]);
    }

    /**
     * Update table status.
     */
    public function updateStatus(Request $request, Table $table)
    {
        $validated = $request->validate([
            'status' => 'required|in:disponivel,ocupada,reservada',
        ]);

        $table->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Status da mesa atualizado com sucesso!'
        ]);
    }

    /**
     * Get table status for API.
     */
    public function getStatus()
    {
        $tables = Table::where('active', true)
            ->orderBy('number')
            ->get(['id', 'number', 'capacity', 'status']);

        return response()->json($tables);
    }
}
