<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::withCount('products')
            ->orderBy('order')
            ->orderBy('name')
            ->paginate(15);
        return view('admin.category.categories', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.category.category-form', ['isEditing' => false]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
            'emoji' => 'nullable|string|max:10',
            'active' => 'boolean',
            'order' => 'nullable|integer|min:0',
        ]);

        $validated['active'] = $request->has('active');
        
        // Se não foi informado um order, usar o próximo número disponível
        if (!isset($validated['order']) || $validated['order'] === null) {
            $maxOrder = Category::max('order') ?? 0;
            $validated['order'] = $maxOrder + 1;
        }

        Category::create($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Categoria criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        $productCount = $category->products()->count();
        $activeProductCount = $category->products()->where('active', true)->count();

        $recentProducts = $category->products()
            ->with('category')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.category.category-show', compact('category', 'productCount', 'activeProductCount', 'recentProducts'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return view('admin.category.category-form', ['category' => $category, 'isEditing' => true]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
            'emoji' => 'nullable|string|max:10',
            'active' => 'boolean',
            'order' => 'nullable|integer|min:0',
        ]);

        $validated['active'] = $request->has('active');
        
        // Se não foi informado um order, manter o valor atual ou usar 0
        if (!isset($validated['order']) || $validated['order'] === null) {
            $validated['order'] = $category->order ?? 0;
        }

        $category->update($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Categoria atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        // Check if category has products
        if ($category->products()->count() > 0) {
            return redirect()->route('categories.index')
                ->with('error', 'Não é possível excluir categoria que possui produtos!');
        }

        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Categoria excluída com sucesso!');
    }

    /**
     * Toggle category active status.
     */
    public function toggleStatus(Category $category)
    {
        $category->update(['active' => !$category->active]);

        return response()->json([
            'success' => true,
            'active' => $category->active,
            'message' => $category->active ? 'Categoria ativada!' : 'Categoria desativada!'
        ]);
    }

    /**
     * Get all active categories (API).
     */
    public function apiIndex()
    {
        $categories = Category::where('active', true)
            ->withCount('products')
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        return response()->json($categories);
    }
}
