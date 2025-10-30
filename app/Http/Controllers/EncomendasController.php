<?php

namespace App\Http\Controllers;

use App\Models\Encomenda;
use App\Models\Customer;
use App\Services\ThermalPrinterService;
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
        $request->validate([
            'status' => 'required|in:pendente,em_producao,pronto,entregue,cancelado'
        ]);

        try {
            // Find the encomenda (for now we'll simulate since the model isn't fully implemented)
            // $encomenda = Encomenda::findOrFail($id);
            // $encomenda->update(['status' => $request->status]);

            // For now, just simulate success
            // In production, this would actually update the encomenda in the database

            return response()->json([
                'success' => true,
                'message' => 'Status da encomenda atualizado com sucesso',
                'new_status' => $request->status
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar status: ' . $e->getMessage()
            ], 500);
        }
    }


    public function printEncomenda(Request $request, $id)
    {
        try {
            // For now, create simulated order data for printing
            // In production, this would fetch the actual encomenda from database

            $orderData = [
                'order_number' => $id,
                'date' => now()->format('d/m/Y H:i'),
                'order_type' => 'delivery',
                'customer_name' => 'Maria Silva',
                'customer_phone' => '(98) 98765-4321',
                'delivery_address' => 'Rua das Flores, 123 - Centro',
                'items' => [
                    [
                        'name' => 'Bolo de Chocolate Grande',
                        'quantity' => 1,
                        'price' => 45.00,
                        'subtotal' => 45.00,
                    ],
                    [
                        'name' => 'Brigadeiro Gourmet (25un)',
                        'quantity' => 1,
                        'price' => 35.00,
                        'subtotal' => 35.00,
                    ]
                ],
                'subtotal' => 80.00,
                'discount' => 0.00,
                'delivery_fee' => 5.00,
                'total' => 85.00,
                'payment_method' => 'pix',
            ];

            // Get printer configuration from settings or use defaults
            $printerConfig = [
                'file_path' => storage_path('app/prints/encomenda_' . $id . '_' . time() . '.txt')
            ];

            $printer = new ThermalPrinterService();
            $printer->connect($printerConfig);
            $printer->printHeader('Doce Doce Brigaderia');
            $printer->printOrder($orderData);
            $printer->printFooter('Encomenda preparada com carinho!');
            $printer->cut();
            $printer->finalize();

            return response()->json([
                'success' => true,
                'message' => 'Encomenda impressa com sucesso na POS'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao imprimir encomenda: ' . $e->getMessage()
            ], 500);
        }
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
