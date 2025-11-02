<?php

namespace App\Http\Controllers;

use App\Models\Table;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TableController extends Controller
{
    /**
     * Display a listing of tables.
     */
    public function index()
    {
        $tables = Table::orderBy('number')->get();
        return view('admin.table.tables', compact('tables'));
    }

    /**
     * Show the form for creating a new table.
     */
    public function create()
    {
        return view('admin.table.table-form');
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
        return view('admin.table.table-show', compact('table', 'currentSale'));
    }

    /**
     * Show the form for editing the specified table.
     */
    public function edit(Table $table)
    {
        return view('admin.table.table-form', compact('table'));
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

    /**
     * Change table for active sale.
     * Move customer from current table to new table.
     */
    public function changeTable(Request $request, Table $table)
    {
        $user = Auth::user();

        // Validar permissão para mudar mesa
        if (!$user->hasRole('admin') && !$user->hasPermission('tables.change')) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para mudar mesas!'
            ], 403);
        }

        $validated = $request->validate([
            'new_table_id' => 'required|exists:tables,id',
        ]);

        $newTableId = $validated['new_table_id'];
        $newTable = Table::findOrFail($newTableId);

        try {
            DB::beginTransaction();

            // Validar que mesa origem tem venda ativa
            $currentSale = $table->sales()
                ->where('status', '!=', 'finalizado')
                ->where('status', '!=', 'cancelado')
                ->first();

            if (!$currentSale) {
                return response()->json([
                    'success' => false,
                    'message' => 'A mesa origem não possui uma venda ativa!'
                ], 422);
            }

            // Validar que mesa destino está disponível
            if ($newTable->status !== 'disponivel') {
                return response()->json([
                    'success' => false,
                    'message' => 'A mesa destino não está disponível! Status atual: ' . ucfirst($newTable->status)
                ], 422);
            }

            // Verificar se mesa destino não tem venda ativa
            $destinationActiveSale = $newTable->sales()
                ->where('status', '!=', 'finalizado')
                ->where('status', '!=', 'cancelado')
                ->first();

            if ($destinationActiveSale) {
                return response()->json([
                    'success' => false,
                    'message' => 'A mesa destino já possui uma venda ativa!'
                ], 422);
            }

            // Atualizar venda para nova mesa
            $currentSale->update(['table_id' => $newTableId]);

            // Liberar mesa origem
            $table->update(['status' => 'disponivel']);

            // Ocupar mesa destino
            $newTable->update(['status' => 'ocupada']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Cliente movido da mesa {$table->number} para mesa {$newTable->number} com sucesso!",
                'sale' => $currentSale->fresh(['table', 'customer']),
                'old_table' => $table,
                'new_table' => $newTable
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Erro ao mudar mesa: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Limpa mesas órfãs (mesas ocupadas/reservadas sem vendas ativas)
     */
    public function cleanOrphans(Request $request)
    {
        try {
            DB::beginTransaction();

            $orphanTables = collect();

            // Mesas ocupadas sem vendas ativas
            $occupiedOrphans = Table::where('status', 'ocupada')
                ->where('active', true)
                ->get()
                ->filter(function ($table) {
                    $activeSale = $table->sales()
                        ->where('status', '!=', 'finalizado')
                        ->where('status', '!=', 'cancelado')
                        ->first();
                    
                    return !$activeSale;
                });

            // Mesas reservadas sem vendas pendentes
            $reservedOrphans = Table::where('status', 'reservada')
                ->where('active', true)
                ->get()
                ->filter(function ($table) {
                    $pendingSale = $table->sales()
                        ->where('status', 'pendente')
                        ->first();
                    
                    return !$pendingSale;
                });

            $orphanTables = $orphanTables
                ->merge($occupiedOrphans)
                ->merge($reservedOrphans)
                ->unique('id');

            if ($orphanTables->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Nenhuma mesa órfã encontrada!',
                    'corrected' => 0,
                    'tables' => []
                ]);
            }

            $correctedTables = [];
            foreach ($orphanTables as $table) {
                $oldStatus = $table->status;
                $table->update(['status' => 'disponivel']);
                $correctedTables[] = [
                    'id' => $table->id,
                    'number' => $table->number,
                    'old_status' => $oldStatus,
                    'new_status' => 'disponivel'
                ];
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "{$orphanTables->count()} mesa(s) órfã(s) corrigida(s) com sucesso!",
                'corrected' => $orphanTables->count(),
                'tables' => $correctedTables
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Erro ao limpar mesas órfãs: ' . $e->getMessage()
            ], 500);
        }
    }
}
