<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Models\Category;
use App\Models\Menu;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Product::with('category');

        // Filter by search query (name)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('active', false);
            }
        }

        $products = $query
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->orderBy('categories.order')
            ->orderBy('products.name') // Ordenação secundária por nome do produto
            ->select('products.*')
            ->get();

        return view('admin.product.products', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $categories = Category::where('active', true)->get();

        return view('admin.product.product-form', ['product' => null, 'categories' => $categories, 'isEditing' => false]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['active'] = $request->has('active');

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($validated);

        // Set availability for all days by default
        $daysOfWeek = ['segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado', 'domingo'];
        foreach ($daysOfWeek as $day) {
            Menu::create([
                'product_id' => $product->id,
                'day_of_week' => $day,
                'available' => true,
            ]);
        }

        return redirect()->route('products.index')
            ->with('success', 'Produto criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product): View
    {
        $product->load('category');

        // Otimização: calcular todas as estatísticas em uma única query usando agregados
        $stats = $product->saleItems()
            ->selectRaw('
                SUM(quantity) as total_sales,
                SUM(subtotal) as total_revenue,
                COUNT(DISTINCT sale_id) as total_orders
            ')
            ->first();

        $totalSales = (int) ($stats->total_sales ?? 0);
        $totalRevenue = (float) ($stats->total_revenue ?? 0);
        $totalOrders = (int) ($stats->total_orders ?? 0);

        // Produtos similares (mesma categoria)
        $similarProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('active', true)
            ->orderBy('name')
            ->limit(5)
            ->get();

        return view('admin.product.product-show', compact(
            'product',
            'totalSales',
            'totalRevenue',
            'totalOrders',
            'similarProducts'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product): View
    {
        $categories = Category::where('active', true)->get();

        return view('admin.product.product-form', ['product' => $product, 'categories' => $categories, 'isEditing' => true]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $validated = $request->validated();
        $validated['active'] = $request->has('active');

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Produto atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        // Check if product has sales
        if ($product->saleItems()->count() > 0) {
            return redirect()->route('products.index')
                ->with('error', 'Não é possível excluir produto que possui vendas!');
        }

        // Delete image
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Produto excluído com sucesso!');
    }

    /**
     * Toggle product active status.
     */
    public function toggleStatus(Product $product): JsonResponse
    {
        $product->update(['active' => !$product->active]);

        return response()->json([
            'success' => true,
            'active' => $product->active,
            'message' => $product->active ? 'Produto ativado!' : 'Produto desativado!'
        ]);
    }

    /**
     * Update product availability for specific day.
     */
    public function updateAvailability(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'day_of_week' => 'required|in:segunda,terca,quarta,quinta,sexta,sabado,domingo',
            'available' => 'required|boolean',
        ]);

        Menu::updateOrCreate(
            [
                'product_id' => $product->id,
                'day_of_week' => $validated['day_of_week'],
            ],
            [
                'available' => $validated['available'],
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Disponibilidade atualizada com sucesso!'
        ]);
    }

    /**
     * Get products available for specific day (API).
     * @deprecated Use API controller
     */
    public function getAvailableForDay($dayOfWeek)
    {
        $products = Product::where('active', true)
            ->whereHas('productDays', function($query) use ($dayOfWeek) {
                $query->where('day_of_week', $dayOfWeek)
                      ->where('available', true);
            })
            ->with('category')
            ->orderBy('category_id')
            ->orderBy('name')
            ->get();

        return response()->json($products);
    }

    /**
     * Get products by category (API).
     * @deprecated Use API controller
     */
    public function getByCategory(Category $category)
    {
        $products = $category->products()
            ->where('active', true)
            ->orderBy('name')
            ->get();

        return response()->json($products);
    }
}
