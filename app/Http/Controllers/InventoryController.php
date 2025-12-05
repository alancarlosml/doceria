<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = InventoryItem::with('lastUpdatedBy');

        // Filtrar por estoque baixo
        if ($request->filled('low_stock')) {
            $query->whereRaw('current_quantity <= min_quantity');
        }

        // Filtrar por ativo/inativo
        if ($request->filled('active')) {
            $query->where('active', $request->active === '1');
        }

        // Busca por nome
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $items = $query->orderBy('name')->paginate(15);

        // Estatísticas
        $totalItems = InventoryItem::where('active', true)->count();
        $lowStockItems = InventoryItem::where('active', true)
            ->whereRaw('current_quantity <= min_quantity')
            ->count();
        $criticalStockItems = InventoryItem::where('active', true)
            ->whereRaw('current_quantity < (min_quantity * 0.5)')
            ->count();

        return view('admin.inventory.index', compact('items', 'totalItems', 'lowStockItems', 'criticalStockItems'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.inventory.form', ['isEditing' => false]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:inventory_items,name',
            'current_quantity' => 'required|numeric|min:0',
            'min_quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'notes' => 'nullable|string',
            'active' => 'boolean',
        ]);

        $validated['active'] = $request->has('active');
        $validated['last_updated_by'] = Auth::id();

        InventoryItem::create($validated);

        return redirect()->route('inventory.index')
            ->with('success', 'Insumo criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(InventoryItem $inventory)
    {
        $inventory->load('lastUpdatedBy');
        
        // Histórico de atualizações (últimas 10)
        $recentUpdates = InventoryItem::where('id', $inventory->id)
            ->whereNotNull('updated_at')
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.inventory.show', compact('inventory', 'recentUpdates'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InventoryItem $inventory)
    {
        return view('admin.inventory.form', ['inventory' => $inventory, 'isEditing' => true]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, InventoryItem $inventory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:inventory_items,name,' . $inventory->id,
            'current_quantity' => 'required|numeric|min:0',
            'min_quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'notes' => 'nullable|string',
            'active' => 'boolean',
        ]);

        $validated['active'] = $request->has('active');
        $validated['last_updated_by'] = Auth::id();

        $inventory->update($validated);

        return redirect()->route('inventory.index')
            ->with('success', 'Insumo atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InventoryItem $inventory)
    {
        $inventory->delete();

        return redirect()->route('inventory.index')
            ->with('success', 'Insumo excluído com sucesso!');
    }

    /**
     * Atualizar quantidade (vistoria rápida)
     */
    public function updateQuantity(Request $request, InventoryItem $inventory)
    {
        $validated = $request->validate([
            'current_quantity' => 'required|numeric|min:0',
        ]);

        $validated['last_updated_by'] = Auth::id();
        $inventory->update($validated);

        // Se for requisição AJAX, retornar JSON
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Quantidade atualizada com sucesso!',
                'item' => [
                    'id' => $inventory->id,
                    'current_quantity' => $inventory->current_quantity,
                    'is_low_stock' => $inventory->isLowStock(),
                    'is_critical_stock' => $inventory->isCriticalStock(),
                ]
            ]);
        }

        return redirect()->route('inventory.index')
            ->with('success', 'Quantidade atualizada com sucesso!');
    }

    /**
     * Toggle active status.
     */
    public function toggleStatus(InventoryItem $inventory)
    {
        $inventory->update(['active' => !$inventory->active]);

        return response()->json([
            'success' => true,
            'active' => $inventory->active,
            'message' => $inventory->active ? 'Insumo ativado!' : 'Insumo desativado!'
        ]);
    }

    /**
     * Página de vistoria (atualização rápida de todos os itens)
     */
    public function inspection()
    {
        $items = InventoryItem::where('active', true)
            ->orderBy('name')
            ->get();

        return view('admin.inventory.inspection', compact('items'));
    }

    /**
     * Salvar vistoria (atualização em lote)
     */
    public function saveInspection(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:inventory_items,id',
            'items.*.current_quantity' => 'required|numeric|min:0',
        ]);

        $updatedCount = 0;
        foreach ($validated['items'] as $itemData) {
            $item = InventoryItem::find($itemData['id']);
            if ($item) {
                $item->update([
                    'current_quantity' => $itemData['current_quantity'],
                    'last_updated_by' => Auth::id(),
                ]);
                $updatedCount++;
            }
        }

        return redirect()->route('inventory.index')
            ->with('success', "Vistoria concluída! {$updatedCount} itens atualizados.");
    }
}
