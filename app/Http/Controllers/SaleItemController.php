<?php

namespace App\Http\Controllers;

use App\Models\SaleItem;
use App\Models\Sale;
use App\Models\Product;
use Illuminate\Http\Request;

class SaleItemController extends Controller
{
    /**
     * Display a listing of sale items.
     */
    public function index(Request $request)
    {
        $query = SaleItem::with(['sale', 'product']);

        // Filter by sale
        if ($request->filled('sale_id')) {
            $query->where('sale_id', $request->sale_id);
        }

        // Filter by product
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        $saleItems = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('sale_items.index', compact('saleItems'));
    }

    /**
     * Show the form for creating a new sale item.
     */
    public function create(Request $request)
    {
        $sale = Sale::findOrFail($request->sale_id);
        $products = Product::where('active', true)->with('category')->orderBy('name')->get();

        return view('sale_items.create', compact('sale', 'products'));
    }

    /**
     * Store a newly created sale item.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $sale = Sale::findOrFail($validated['sale_id']);
        $product = Product::findOrFail($validated['product_id']);

        // Only allow adding items to pending sales
        if (!$sale->isPendente()) {
            return back()->with('error', 'Apenas vendas pendentes podem ter itens adicionados!');
        }

        $unitPrice = $product->price;
        $subtotal = $unitPrice * $validated['quantity'];

        SaleItem::create([
            'sale_id' => $validated['sale_id'],
            'product_id' => $validated['product_id'],
            'quantity' => $validated['quantity'],
            'unit_price' => $unitPrice,
            'subtotal' => $subtotal,
            'notes' => $validated['notes'],
        ]);

        // Recalculate sale totals
        $sale->calculateTotal();

        return redirect()->route('sales.show', $sale)
            ->with('success', 'Item adicionado à venda com sucesso!');
    }

    /**
     * Display the specified sale item.
     */
    public function show(SaleItem $saleItem)
    {
        return view('sale_items.show', compact('saleItem'));
    }

    /**
     * Show the form for editing the specified sale item.
     */
    public function edit(SaleItem $saleItem)
    {
        $products = Product::where('active', true)->with('category')->orderBy('name')->get();
        return view('sale_items.edit', compact('saleItem', 'products'));
    }

    /**
     * Update the specified sale item.
     */
    public function update(Request $request, SaleItem $saleItem)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $sale = $saleItem->sale;
        $product = Product::findOrFail($validated['product_id']);

        // Only allow updating items from pending sales
        if (!$sale->isPendente()) {
            return back()->with('error', 'Apenas vendas pendentes podem ter itens editados!');
        }

        $unitPrice = $product->price;
        $subtotal = $unitPrice * $validated['quantity'];

        $saleItem->update([
            'product_id' => $validated['product_id'],
            'quantity' => $validated['quantity'],
            'unit_price' => $unitPrice,
            'subtotal' => $subtotal,
            'notes' => $validated['notes'],
        ]);

        // Recalculate sale totals
        $sale->calculateTotal();

        return redirect()->route('sales.show', $sale)
            ->with('success', 'Item da venda atualizado com sucesso!');
    }

    /**
     * Remove the specified sale item.
     */
    public function destroy(SaleItem $saleItem)
    {
        $sale = $saleItem->sale;

        // Only allow removing items from pending sales
        if (!$sale->isPendente()) {
            return back()->with('error', 'Apenas vendas pendentes podem ter itens removidos!');
        }

        $saleItem->delete();

        // Recalculate sale totals
        $sale->calculateTotal();

        return redirect()->route('sales.show', $sale)
            ->with('success', 'Item removido da venda com sucesso!');
    }

    /**
     * Get sale items for a specific sale (API).
     */
    public function getBySale(Sale $sale)
    {
        $items = $sale->items()->with('product.category')->get();

        return response()->json($items);
    }
}
