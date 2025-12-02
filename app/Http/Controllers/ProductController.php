<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Normaliza valor monetário brasileiro para formato numérico
     * Converte "1.234,56" ou "10,50" para "1234.56" ou "10.50"
     */
    private function normalizeMonetaryValue($value)
    {
        if (empty($value)) {
            return null;
        }

        $value = trim($value);
        
        // Se contém vírgula, assume formato brasileiro (1.234,56)
        if (strpos($value, ',') !== false) {
            // Remove pontos (separadores de milhar) e substitui vírgula por ponto
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        }
        // Se não tem vírgula mas tem ponto, pode ser formato internacional (10.50)
        // Nesse caso, mantém como está
        
        return $value;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
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
    public function create()
    {
        $categories = Category::where('active', true)->get();

        return view('admin.product.product-form', ['product' => null, 'categories' => $categories, 'isEditing' => false]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Normalizar valores monetários (converter vírgula para ponto)
        $request->merge([
            'price' => $this->normalizeMonetaryValue($request->price),
            'cost_price' => $this->normalizeMonetaryValue($request->cost_price),
        ]);

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'active' => 'boolean',
        ]);

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
    public function show(Product $product)
    {
        $product->load('category');

        // Estatísticas do produto
        $totalSales = $product->saleItems()->sum('quantity');
        $totalRevenue = $product->saleItems()->sum('subtotal');
        $totalOrders = $product->saleItems()->distinct('sale_id')->count();

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
    public function edit(Product $product)
    {
        $categories = Category::where('active', true)->get();

        return view('admin.product.product-form', ['product' => $product, 'categories' => $categories, 'isEditing' => true]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        // Normalizar valores monetários (converter vírgula para ponto)
        $request->merge([
            'price' => $this->normalizeMonetaryValue($request->price),
            'cost_price' => $this->normalizeMonetaryValue($request->cost_price),
        ]);

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'active' => 'boolean',
        ]);

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
    public function destroy(Product $product)
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
    public function toggleStatus(Product $product)
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
    public function updateAvailability(Request $request, Product $product)
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
