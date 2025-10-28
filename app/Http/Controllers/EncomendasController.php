<?php

namespace App\Http\Controllers;

use App\Models\Encomenda;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EncomendasController extends Controller
{
    public function index()
    {
        // Usar dados mock por enquanto até implementar completamente
        $groupedEncomendas = [];
        $filters = ['status' => '', 'period' => 'todos', 'search' => ''];
        $stats = [
            'pendentes' => 0,
            'em_producao' => 0,
            'hoje' => 0,
            'valor_total' => '0,00'
        ];
        $encomendas = collect();

        return view('admin.sale.encomendas', compact('groupedEncomendas', 'filters', 'stats', 'encomendas'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        return view('admin.encomendas.create', compact('customers'));
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.sale.encomendas')->with('info', 'Sistema de encomendas em desenvolvimento');
    }

    public function show($id)
    {
        return view('admin.encomendas.show', ['encomenda' => null]);
    }

    public function edit($id)
    {
        return view('admin.encomendas.edit', ['encomenda' => null]);
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('admin.sale.encomendas')->with('info', 'Sistema de encomendas em desenvolvimento');
    }

    public function destroy($id)
    {
        return redirect()->route('admin.sale.encomendas')->with('info', 'Sistema de encomendas em desenvolvimento');
    }

    public function updateStatus(Request $request, $id)
    {
        return response()->json(['success' => false, 'message' => 'Sistema em desenvolvimento']);
    }

    public function stats()
    {
        return response()->json([
            'pendentes' => 0,
            'em_producao' => 0,
            'pronto' => 0,
            'entregue' => 0,
            'hoje' => 0,
            'valor_total' => 0,
            'lucro_estimado' => 0,
        ]);
    }
}
