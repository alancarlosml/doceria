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
        $categories = Category::withCount('products')->paginate(15);
        return view('admin.categories', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.category-form', ['isEditing' => false]);
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
        ]);

        $validated['active'] = $request->has('active');

        Category::create($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Categoria criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        $category->load('products');
        return view('categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return view('admin.category-form', ['category' => $category, 'isEditing' => true]);
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
        ]);

        $validated['active'] = $request->has('active');

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
            ->orderBy('name')
            ->get();

        return response()->json($categories);
    }
}