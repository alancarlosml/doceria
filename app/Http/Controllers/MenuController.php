<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MenuController extends Controller
{
    /**
     * Show the menu management interface.
     */
    public function manage()
    {
        // Get all products that can be in menu
        $products = Product::with('category')
            ->where('active', true)
            ->orderBy('category_id')
            ->orderBy('name')
            ->get();

        // Get current day (for initial load)
        $currentDay = now()->dayOfWeek;

        // Map numeric day to Brazilian day names
        $dayNames = [
            0 => 'domingo',
            1 => 'segunda',
            2 => 'terca',
            3 => 'quarta',
            4 => 'quinta',
            5 => 'sexta',
            6 => 'sabado'
        ];

        $currentDayName = $dayNames[$currentDay];

        return view('admin.menu.menus', compact('products', 'currentDayName'));
    }

    /**
     * Show the menu management interface for a specific day.
     */
    public function manageDay($day)
    {
        // Validate day parameter
        $validDays = ['segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado', 'domingo'];
        if (!in_array($day, $validDays)) {
            abort(404, 'Dia da semana inválido');
        }

        // Get all products that can be in menu
        $products = Product::with('category')
            ->where('active', true)
            ->orderBy('category_id')
            ->orderBy('name')
            ->get();

        $currentDayName = $day;

        return view('admin.menu.menus', compact('products', 'currentDayName'));
    }

    /**
     * Get menu data for a specific day (web route, authenticated).
     */
    public function getMenuDataForDay($day)
    {
        // Validate day parameter
        $validDays = ['segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado', 'domingo'];
        if (!in_array($day, $validDays)) {
            return response()->json(['error' => 'Dia da semana inválido'], 400);
        }

        try {
            // Carregar menus com relacionamentos
            $menuItems = Menu::with(['product.category'])
                ->where('day_of_week', $day)
                ->where('available', true)
                ->whereHas('product', function($query) {
                    $query->where('active', true);
                })
                ->get();

            $formattedMenuItems = $menuItems->map(function ($item) {
                return [
                    'product_id' => $item->product_id,
                    'product' => [
                        'id' => $item->product->id,
                        'name' => $item->product->name,
                        'price' => $item->product->price,
                        'category' => [
                            'id' => $item->product->category->id ?? null,
                            'name' => $item->product->category->name ?? 'Sem categoria'
                        ]
                    ],
                    'available' => $item->available
                ];
            });

            return response()->json($formattedMenuItems);

        } catch (\Exception $e) {
            Log::error('Erro ao carregar cardápio: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json(['error' => 'Erro interno: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Toggle product availability for a specific day (AJAX).
     * Este método é chamado quando você clica no toggle no menus.blade.php
     * e afeta diretamente o que aparece no index.blade.php
     */
    public function toggleForDay(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'day_of_week' => 'required|in:segunda,terca,quarta,quinta,sexta,sabado,domingo',
            'available' => 'required|boolean',
        ]);

        try {
            // Find or create menu entry
            $menu = Menu::firstOrNew([
                'product_id' => $validated['product_id'],
                'day_of_week' => $validated['day_of_week'],
            ]);

            $menu->available = $validated['available'];
            $menu->save();

            // Carregar produto para retornar informações completas
            $product = Product::with('category')->find($validated['product_id']);

            Log::info('Menu atualizado', [
                'product_id' => $validated['product_id'],
                'product_name' => $product->name,
                'day_of_week' => $validated['day_of_week'],
                'available' => $validated['available'],
                'affected_page' => 'index.blade.php (página do cliente)'
            ]);

            return response()->json([
                'success' => true,
                'menu' => $menu,
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'category' => $product->category->name ?? 'Sem categoria'
                ],
                'message' => $menu->available 
                    ? "✅ {$product->name} adicionado ao cardápio de {$validated['day_of_week']}! Os clientes já podem ver este produto." 
                    : "❌ {$product->name} removido do cardápio de {$validated['day_of_week']}."
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar menu: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Erro ao atualizar cardápio: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display a listing of menu items.
     */
    public function index(Request $request)
    {
        $query = Menu::with('product.category');

        // Filter by day of week
        if ($request->filled('day_of_week')) {
            $query->where('day_of_week', $request->day_of_week);
        }

        // Filter by availability
        if ($request->filled('available')) {
            $query->where('available', $request->boolean('available'));
        }

        $menuItems = $query->orderBy('day_of_week')->orderBy('product_id')->paginate(20);

        return view('menu.index', compact('menuItems'));
    }

    /**
     * Show the form for creating a new menu item.
     */
    public function create()
    {
        $products = Product::where('active', true)->with('category')->orderBy('name')->get();
        return view('menu.create', compact('products'));
    }

    /**
     * Store a newly created menu item.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'day_of_week' => 'required|in:segunda,terca,quarta,quinta,sexta,sabado,domingo',
            'available' => 'required|boolean',
        ]);

        Menu::create($validated);

        return redirect()->route('menu.index')
            ->with('success', 'Item do cardápio criado com sucesso!');
    }

    /**
     * Display the specified menu item.
     */
    public function show(Menu $menu)
    {
        return view('menu.show', compact('menu'));
    }

    /**
     * Show the form for editing the specified menu item.
     */
    public function edit(Menu $menu)
    {
        return view('menu.edit', compact('menu'));
    }

    /**
     * Update the specified menu item.
     */
    public function update(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'day_of_week' => 'required|in:segunda,terca,quarta,quinta,sexta,sabado,domingo',
            'available' => 'required|boolean',
        ]);

        $menu->update($validated);

        return redirect()->route('menu.index')
            ->with('success', 'Item do cardápio atualizado com sucesso!');
    }

    /**
     * Remove the specified menu item.
     */
    public function destroy(Menu $menu)
    {
        $menu->delete();

        return redirect()->route('menu.index')
            ->with('success', 'Item do cardápio excluído com sucesso!');
    }

    /**
     * Toggle menu item availability.
     */
    public function toggleAvailability(Menu $menu)
    {
        $menu->update(['available' => !$menu->available]);

        return response()->json([
            'success' => true,
            'available' => $menu->available,
            'message' => $menu->available ? 'Produto disponível!' : 'Produto indisponível!'
        ]);
    }

    /**
     * Get menu for specific day (API).
     */
    public function getForDay($dayOfWeek)
    {
        $menuItems = Menu::where('day_of_week', $dayOfWeek)
            ->where('available', true)
            ->with('product.category')
            ->orderBy('product_id')
            ->get();

        return response()->json($menuItems);
    }

    /**
     * Get all available days for a product.
     */
    public function getProductDays(Product $product)
    {
        $availableDays = $product->getAvailableDays();

        return response()->json([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'available_days' => $availableDays,
        ]);
    }
}