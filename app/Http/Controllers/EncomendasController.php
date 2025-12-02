<?php

namespace App\Http\Controllers;

use App\Models\Encomenda;
use App\Models\EncomendaItem;
use App\Models\Customer;
use App\Models\Product;
use App\Models\CashRegister;
use App\Services\ThermalPrinterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EncomendasController extends Controller
{
    /**
     * Listar encomendas (página principal)
     */
    public function index()
    {
        return view('admin.sale.encomendas');
    }

    /**
     * API: Buscar encomendas com filtros
     */
    public function apiIndex(Request $request)
    {
        $query = Encomenda::with(['customer', 'items.product', 'user'])
            ->orderBy('delivery_date', 'desc')
            ->orderBy('delivery_time', 'desc');

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('delivery_date')) {
            $query->whereDate('delivery_date', $request->delivery_date);
        }

        if ($request->filled('period')) {
            switch ($request->period) {
                case 'hoje':
                    $query->whereDate('delivery_date', today());
                    break;
                case 'amanha':
                    $query->whereDate('delivery_date', today()->addDay());
                    break;
                case 'semana':
                    $query->whereBetween('delivery_date', [today(), today()->addWeek()]);
                    break;
                case 'mes':
                    $query->whereMonth('delivery_date', now()->month)
                          ->whereYear('delivery_date', now()->year);
                    break;
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        $encomendas = $query->get();

        // Agrupar por data
        $grouped = $encomendas->groupBy(function($encomenda) {
            return $encomenda->delivery_date->format('Y-m-d');
        })->map(function($group, $date) {
            $first = $group->first();
            return [
                'date' => $date,
                'date_formatted' => $first->delivery_date->locale('pt_BR')->translatedFormat('l, d \d\e F \d\e Y'),
                'encomendas' => $group->map(function($encomenda) {
                    return $this->formatEncomendaForApi($encomenda);
                })->values()
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => $grouped
        ]);
    }

    /**
     * API: Estatísticas
     */
    public function stats()
    {
        $pendentes = Encomenda::where('status', 'pendente')->count();
        $emProducao = Encomenda::where('status', 'em_producao')->count();
        $hoje = Encomenda::whereDate('delivery_date', today())
            ->whereIn('status', ['pendente', 'em_producao', 'pronto'])
            ->count();
        $valorTotal = Encomenda::whereIn('status', ['pendente', 'em_producao', 'pronto'])
            ->sum('total');

        return response()->json([
            'success' => true,
            'pendentes' => $pendentes,
            'em_producao' => $emProducao,
            'hoje' => $hoje,
            'valor_total' => number_format($valorTotal, 2, ',', '.'),
        ]);
    }

    /**
     * Criar nova encomenda
     */
    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $products = Product::where('active', true)->orderBy('name')->get();
        return view('admin.encomendas.create', compact('customers', 'products'));
    }

    /**
     * Salvar nova encomenda
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'delivery_date' => 'required|date',
            'delivery_time' => 'nullable|string',
            'delivery_address' => 'nullable|string',
            'delivery_fee' => 'nullable|numeric|min:0',
            'custom_costs' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'items' => 'nullable|array',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.product_name' => 'required_with:items|string|max:255',
            'items.*.quantity' => 'required_with:items|integer|min:1',
            'items.*.unit_price' => 'required_with:items|numeric|min:0',
            'items.*.notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Se não houver itens, criar um item padrão baseado no subtotal
            if (empty($validated['items']) || count($validated['items']) === 0) {
                $validated['items'] = [[
                    'product_id' => null,
                    'product_name' => $validated['title'],
                    'quantity' => 1,
                    'unit_price' => $validated['subtotal'],
                    'notes' => $validated['description'] ?? null,
                ]];
            }

            // Calcular subtotal dos itens
            $subtotal = collect($validated['items'])->sum(function($item) {
                return $item['quantity'] * $item['unit_price'];
            });

            // Calcular total
            $total = $subtotal 
                + ($validated['custom_costs'] ?? 0)
                + ($validated['delivery_fee'] ?? 0)
                - ($validated['discount'] ?? 0);

            // Criar encomenda
            $encomenda = Encomenda::create([
                'user_id' => Auth::id(),
                'customer_id' => $validated['customer_id'] ?? null,
                'code' => Encomenda::generateCode(),
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'status' => 'pendente',
                'delivery_date' => $validated['delivery_date'],
                'delivery_time' => $validated['delivery_time'] ?? null,
                'delivery_address' => $validated['delivery_address'] ?? null,
                'delivery_fee' => $validated['delivery_fee'] ?? 0,
                'custom_costs' => $validated['custom_costs'] ?? 0,
                'discount' => $validated['discount'] ?? 0,
                'subtotal' => $subtotal,
                'total' => $total,
            ]);

            // Criar itens
            foreach ($validated['items'] as $itemData) {
                $product = $itemData['product_id'] ? Product::find($itemData['product_id']) : null;
                
                EncomendaItem::create([
                    'encomenda_id' => $encomenda->id,
                    'product_id' => $itemData['product_id'] ?? null,
                    'product_name' => $itemData['product_name'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'subtotal' => $itemData['quantity'] * $itemData['unit_price'],
                    'notes' => $itemData['notes'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('encomendas.index')
                ->with('success', 'Encomenda criada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withErrors(['error' => 'Erro ao criar encomenda: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Exibir encomenda
     */
    public function show(Encomenda $encomenda)
    {
        $encomenda->load(['customer', 'items.product', 'user']);
        return view('admin.encomendas.show', compact('encomenda'));
    }

    /**
     * Editar encomenda
     */
    public function edit(Encomenda $encomenda)
    {
        $encomenda->load(['customer', 'items.product', 'user']);
        $customers = Customer::orderBy('name')->get();
        $products = Product::where('active', true)->orderBy('name')->get();
        return view('admin.encomendas.edit', compact('encomenda', 'customers', 'products'));
    }

    /**
     * Atualizar encomenda
     */
    public function update(Request $request, Encomenda $encomenda)
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'delivery_date' => 'required|date',
            'delivery_time' => 'nullable|string',
            'delivery_address' => 'nullable|string',
            'delivery_fee' => 'nullable|numeric|min:0',
            'custom_costs' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.product_name' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Calcular subtotal dos itens
            $subtotal = collect($validated['items'])->sum(function($item) {
                return $item['quantity'] * $item['unit_price'];
            });

            // Calcular total
            $total = $subtotal 
                + ($validated['custom_costs'] ?? 0)
                + ($validated['delivery_fee'] ?? 0)
                - ($validated['discount'] ?? 0);

            // Atualizar encomenda
            $encomenda->update([
                'customer_id' => $validated['customer_id'] ?? null,
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'delivery_date' => $validated['delivery_date'],
                'delivery_time' => $validated['delivery_time'] ?? null,
                'delivery_address' => $validated['delivery_address'] ?? null,
                'delivery_fee' => $validated['delivery_fee'] ?? 0,
                'custom_costs' => $validated['custom_costs'] ?? 0,
                'discount' => $validated['discount'] ?? 0,
                'subtotal' => $subtotal,
                'total' => $total,
            ]);

            // Deletar itens antigos
            $encomenda->items()->delete();

            // Criar novos itens
            foreach ($validated['items'] as $itemData) {
                EncomendaItem::create([
                    'encomenda_id' => $encomenda->id,
                    'product_id' => $itemData['product_id'] ?? null,
                    'product_name' => $itemData['product_name'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'subtotal' => $itemData['quantity'] * $itemData['unit_price'],
                    'notes' => $itemData['notes'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('encomendas.index')
                ->with('success', 'Encomenda atualizada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withErrors(['error' => 'Erro ao atualizar encomenda: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Deletar encomenda
     */
    public function destroy(Encomenda $encomenda)
    {
        try {
            $encomenda->delete();
            return redirect()->route('encomendas.index')
                ->with('success', 'Encomenda excluída com sucesso!');
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Erro ao excluir encomenda: ' . $e->getMessage()]);
        }
    }

    /**
     * Atualizar status da encomenda
     */
    public function updateStatus(Request $request, Encomenda $encomenda)
    {
        $request->validate([
            'status' => 'required|in:pendente,em_producao,pronto,entregue,cancelado'
        ]);

        try {
            // Se a encomenda está sendo marcada como entregue e não tem caixa vinculado,
            // vincular ao caixa aberto atual
            if ($request->status === 'entregue' && !$encomenda->cash_register_id) {
                $openCashRegister = CashRegister::where('status', 'aberto')->first();
                if ($openCashRegister) {
                    $encomenda->cash_register_id = $openCashRegister->id;
                    $encomenda->save(); // Salvar o cash_register_id antes de atualizar o status
                }
            }

            $encomenda->updateStatus($request->status);

            return response()->json([
                'success' => true,
                'message' => 'Status da encomenda atualizado com sucesso',
                'new_status' => $request->status,
                'encomenda' => $this->formatEncomendaForApi($encomenda->fresh(['customer', 'items.product']))
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Finalizar encomenda com dados de pagamento
     */
    public function finalizarComPagamento(Request $request, Encomenda $encomenda)
    {
        $validated = $request->validate([
            'payment_method' => 'required|in:dinheiro,pix,cartao_debito,cartao_credito,split',
            'payment_methods_split' => 'nullable|array',
            'payment_methods_split.*.method' => 'required_with:payment_methods_split|in:dinheiro,pix,cartao_debito,cartao_credito',
            'payment_methods_split.*.value' => 'required_with:payment_methods_split|numeric|min:0',
            'payment_methods_split.*.amount_received' => 'nullable|numeric|min:0',
            'payment_methods_split.*.change_amount' => 'nullable|numeric|min:0',
            'amount_received' => 'nullable|numeric|min:0',
            'change_amount' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Determinar método de pagamento principal
            $paymentMethod = $validated['payment_method'];
            $paymentMethodsSplit = null;
            $amountReceived = null;
            $changeAmount = null;
            
            // Se for pagamento dividido
            if ($paymentMethod === 'split' && !empty($validated['payment_methods_split'])) {
                $paymentMethodsSplit = $validated['payment_methods_split'];
                // Usar o primeiro método como principal para compatibilidade
                $paymentMethod = $paymentMethodsSplit[0]['method'] ?? 'dinheiro';
                // Calcular troco total para pagamentos em dinheiro
                $totalChange = 0;
                foreach ($paymentMethodsSplit as $split) {
                    if ($split['method'] === 'dinheiro' && isset($split['change_amount'])) {
                        $totalChange += $split['change_amount'];
                    }
                }
                $changeAmount = $totalChange > 0 ? $totalChange : ($validated['change_amount'] ?? null);
            } else {
                // Pagamento simples
                $amountReceived = $validated['amount_received'] ?? null;
                $changeAmount = $validated['change_amount'] ?? null;
            }

            // Vincular ao caixa aberto atual
            $openCashRegister = CashRegister::where('status', 'aberto')->first();
            if ($openCashRegister) {
                $encomenda->cash_register_id = $openCashRegister->id;
            }

            // Atualizar dados de pagamento e status
            $encomenda->update([
                'status' => 'entregue',
                'payment_method' => $paymentMethod,
                'payment_methods_split' => $paymentMethodsSplit,
                'amount_received' => $amountReceived,
                'change_amount' => $changeAmount,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Encomenda finalizada com sucesso!',
                'encomenda' => $this->formatEncomendaForApi($encomenda->fresh(['customer', 'items.product']))
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao finalizar encomenda: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Imprimir encomenda
     */
    public function printEncomenda(Request $request, Encomenda $encomenda)
    {
        try {
            $encomenda->load(['customer', 'items.product']);

            // Preparar dados para impressão
            $orderData = [
                'order_number' => $encomenda->code,
                'date' => $encomenda->created_at->format('d/m/Y H:i'),
                'order_type' => 'encomenda',
                'customer_name' => $encomenda->customer ? $encomenda->customer->name : 'Cliente não identificado',
                'customer_phone' => $encomenda->customer ? $encomenda->customer->phone : null,
                'delivery_address' => $encomenda->delivery_address,
                'items' => $encomenda->items->map(function($item) {
                    return [
                        'name' => $item->product_name,
                        'quantity' => $item->quantity,
                        'price' => $item->unit_price,
                        'subtotal' => $item->subtotal,
                    ];
                })->toArray(),
                'subtotal' => $encomenda->subtotal,
                'discount' => $encomenda->discount,
                'delivery_fee' => $encomenda->delivery_fee,
                'total' => $encomenda->total,
                'payment_method' => $encomenda->payment_method,
                'payment_methods_split' => $encomenda->payment_methods_split,
                'amount_received' => $encomenda->amount_received,
                'change_amount' => $encomenda->change_amount,
            ];

            // Obter configurações da impressora
            $printerConfig = ThermalPrinterService::getConfigFromSettings();

            // Imprimir
            $printer = new ThermalPrinterService();
            $printer->connect($printerConfig);
            $printer->printHeader('Doce Doce Brigaderia');
            $printer->printOrder($orderData);
            $printer->printFooter('Encomenda preparada com carinho!');
            $printer->cut();
            $printer->finalize();

            return response()->json([
                'success' => true,
                'message' => 'Encomenda impressa com sucesso!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao imprimir encomenda: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Formatar encomenda para API
     */
    private function formatEncomendaForApi(Encomenda $encomenda)
    {
        $items = $encomenda->items->map(function($item) {
            return [
                'id' => $item->id,
                'product_name' => $item->product_name,
                'quantity' => $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'unit_price_formatted' => $item->unit_price_formatted,
                'subtotal' => (float) $item->subtotal,
                'subtotal_formatted' => $item->subtotal_formatted,
                'notes' => $item->notes,
            ];
        });

        return [
            'id' => $encomenda->id,
            'code' => $encomenda->code,
            'status' => $encomenda->status,
            'customer_name' => $encomenda->customer ? $encomenda->customer->name : null,
            'customer_phone' => $encomenda->customer ? $encomenda->customer->phone : null,
            'delivery_time' => $encomenda->delivery_time ?: null,
            'delivery_address' => $encomenda->delivery_address,
            'items_summary' => $items->map(function($item) {
                return $item['quantity'] . 'x ' . $item['product_name'];
            })->join(', '),
            'total' => (float) $encomenda->total,
            'total_formatted' => number_format($encomenda->total, 2, ',', '.'),
            'notes' => $encomenda->notes,
            'items' => $items->toArray(),
            'subtotal' => (float) $encomenda->subtotal,
            'subtotal_formatted' => number_format($encomenda->subtotal, 2, ',', '.'),
            'discount' => (float) $encomenda->discount,
            'discount_formatted' => number_format($encomenda->discount, 2, ',', '.'),
            'delivery_date_formatted' => $encomenda->delivery_date->format('d/m/Y'),
            // Campos de pagamento
            'payment_method' => $encomenda->payment_method,
            'payment_methods_split' => $encomenda->payment_methods_split,
            'amount_received' => (float) ($encomenda->amount_received ?? 0),
            'change_amount' => (float) ($encomenda->change_amount ?? 0),
            'payment_summary' => $encomenda->payment_summary ?? null,
        ];
    }
}
