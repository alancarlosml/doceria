<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers.
     */
    public function index(Request $request)
    {
        $query = Customer::query();

        // Search by name, phone, email or CPF
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('cpf', 'like', "%{$search}%");
            });
        }

        $customers = $query->orderBy('name')->paginate(15);

        return view('admin.customer.customers', compact('customers'));
    }

    /**
     * Show the form for creating a new customer.
     */
    public function create()
    {
        return view('admin.customer.customer-form', ['customer' => null, 'isEditing' => false]);
    }

    /**
     * Store a newly created customer.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:customers',
            'cpf' => 'nullable|string|max:14|unique:customers',
            'address' => 'nullable|string',
            'neighborhood' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:2',
            'zipcode' => 'nullable|string|max:10',
        ]);

        Customer::create($validated);

        return redirect()->route('customers.index')
            ->with('success', 'Cliente criado com sucesso!');
    }

    /**
     * Display the specified customer.
     */
    public function show(Customer $customer)
    {
        // Estatísticas do cliente
        $totalSales = $customer->sales()->count();
        $totalOrders = $customer->sales()->sum('total');
        $totalProducts = $customer->sales()
            ->with('items')
            ->get()
            ->pluck('items')
            ->flatten()
            ->sum('quantity');

        // Vendas recentes com produtos
        $recentSales = $customer->sales()
            ->with(['items.product', 'items.product.category'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Produto mais comprado
        $mostPurchasedProduct = $customer->sales()
            ->with('items.product')
            ->get()
            ->pluck('items')
            ->flatten()
            ->groupBy('product_id')
            ->map(function ($items) {
                return $items->sum('quantity');
            })
            ->sortDesc()
            ->keys()
            ->first();

        $favoriteProduct = null;
        if ($mostPurchasedProduct) {
            $favoriteProduct = \App\Models\Product::find($mostPurchasedProduct);
        }

        return view('admin.customer.customer-show', compact(
            'customer',
            'totalSales',
            'totalOrders',
            'totalProducts',
            'recentSales',
            'favoriteProduct'
        ));
    }

    /**
     * Show the form for editing the specified customer.
     */
    public function edit(Customer $customer)
    {
        return view('admin.customer.customer-form', ['customer' => $customer, 'isEditing' => true]);
    }

    /**
     * Update the specified customer.
     */
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:customers,email,' . $customer->id,
            'cpf' => 'nullable|string|max:14|unique:customers,cpf,' . $customer->id,
            'address' => 'nullable|string',
            'neighborhood' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:2',
            'zipcode' => 'nullable|string|max:10',
        ]);

        $customer->update($validated);

        return redirect()->route('customers.index')
            ->with('success', 'Cliente atualizado com sucesso!');
    }

    /**
     * Remove the specified customer.
     */
    public function destroy(Customer $customer)
    {
        // Check if customer has sales
        if ($customer->sales()->count() > 0) {
            return redirect()->route('customers.index')
                ->with('error', 'Não é possível excluir cliente que possui vendas!');
        }

        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Cliente excluído com sucesso!');
    }

    /**
     * Get customers for API (autocomplete).
     */
    public function search(Request $request)
    {
        $search = $request->get('q');
        $customers = Customer::where('name', 'like', "%{$search}%")
            ->orWhere('phone', 'like', "%{$search}%")
            ->limit(10)
            ->get(['id', 'name', 'phone', 'email']);

        return response()->json($customers);
    }
}
