<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Customer;
use App\Models\Table;
use App\Models\Motoboy;
use App\Models\CashRegister;
use App\Models\Product;
use App\Models\SaleItem;
use App\Models\Category;
use App\Services\ThermalPrinterService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    /**
     * Display the POS interface
     */
    public function pos()
    {
        // Verificar caixa aberto
        $openCashRegister = CashRegister::where('status', 'aberto')->first();
        
        if (!$openCashRegister) {
            return redirect()->route('cash-registers.index')
                ->with('error', 'Abra o caixa antes de realizar vendas!');
        }

        // Produtos do dia
        $currentDay = now()->locale('pt_BR')->dayName;
        $dayMapping = [
            'Monday' => 'segunda',
            'Tuesday' => 'terca',
            'Wednesday' => 'quarta',
            'Thursday' => 'quinta',
            'Friday' => 'sexta',
            'Saturday' => 'sabado',
            'Sunday' => 'domingo',
        ];
        $currentDayPt = $dayMapping[$currentDay] ?? 'segunda';

        $products = Product::where('active', true)
            ->whereHas('menus', function($query) use ($currentDayPt) {
                $query->where('day_of_week', $currentDayPt)
                      ->where('available', true);
            })
            ->with('category')
            ->orderBy('category_id')
            ->orderBy('name')
            ->get();

        $categories = Category::where('active', true)
            ->whereHas('products', function($query) use ($currentDayPt) {
                $query->where('active', true)
                      ->whereHas('menus', function($q) use ($currentDayPt) {
                          $q->where('day_of_week', $currentDayPt)
                            ->where('available', true);
                      });
            })
            ->withCount(['products' => function($query) use ($currentDayPt) {
                $query->where('active', true)
                      ->whereHas('menus', function($q) use ($currentDayPt) {
                          $q->where('day_of_week', $currentDayPt)
                            ->where('available', true);
                      });
            }])
            ->orderBy('name')
            ->get();

        // Mesas disponíveis
        $tables = Table::where('active', true)
            ->where('status', 'disponivel')
            ->orderBy('number')
            ->get();

        // Mesas ocupadas (para mostrar em vendas já criadas)
        $occupiedTables = Table::where('active', true)
            ->where('status', 'ocupada')
            ->orderBy('number')
            ->get();

        // Mapa de mesas ocupadas com IDs de vendas
        $occupiedTablesWithSales = $occupiedTables->mapWithKeys(function ($table) {
            $sale = $table->sales()->where('status', '!=', 'finalizado')->where('status', '!=', 'cancelado')->first();
            return [$table->id => $sale ? $sale->id : null];
        })->toArray();

        // Clientes recentes
        $recentCustomers = Customer::orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        // Motoboys
        $motoboys = Motoboy::where('active', true)
            ->orderBy('name')
            ->get();

        // Vendas pendentes (balcão e delivery aguardando preparo)
        $pendingSales = Sale::where('cash_register_id', $openCashRegister->id)
            ->whereIn('status', ['pendente', 'em_preparo', 'pronto'])
            ->with(['customer', 'table', 'items.product', 'motoboy', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Vendas em entrega (deliveries que saíram)
        $inDeliverySales = Sale::where('cash_register_id', $openCashRegister->id)
            ->where('status', 'saiu_entrega')
            ->where('type', 'delivery')
            ->with(['customer', 'items.product', 'motoboy', 'user'])
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('admin.sale.pos', compact(
            'openCashRegister',
            'products',
            'categories',
            'tables',
            'occupiedTables',
            'occupiedTablesWithSales',
            'recentCustomers',
            'motoboys',
            'pendingSales',
            'inDeliverySales'
        ));
    }

    /**
     * Update an existing sale
     */
    public function update(Request $request, Sale $sale)
    {
        $validated = $request->validate([
            'type' => 'required|in:balcao,delivery',
            'table_id' => 'nullable|exists:tables,id',
            'customer_id' => 'nullable|exists:customers,id',
            'motoboy_id' => 'nullable|exists:motoboys,id',
            'delivery_address' => 'nullable|string',
            'delivery_fee' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:dinheiro,pix,cartao_debito,cartao_credito,split',
            'payment_methods_split' => 'nullable|array', // Array de pagamentos divididos
            'payment_methods_split.*.method' => 'required_with:payment_methods_split|in:dinheiro,pix,cartao_debito,cartao_credito',
            'payment_methods_split.*.value' => 'required_with:payment_methods_split|numeric|min:0',
            'payment_methods_split.*.amount_received' => 'nullable|numeric|min:0',
            'payment_methods_split.*.change_amount' => 'nullable|numeric|min:0',
            'amount_received' => 'nullable|numeric|min:0', // Valor recebido (dinheiro)
            'change_amount' => 'nullable|numeric|min:0', // Troco
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'finalize' => 'nullable|boolean',
        ]);

        // Validações específicas
        if ($validated['type'] === 'delivery') {
            if (empty($validated['motoboy_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selecione um motoboy!'
                ], 422);
            }
        }

        $openCashRegister = CashRegister::where('status', 'aberto')->first();
        if (!$openCashRegister) {
            return response()->json([
                'success' => false,
                'message' => 'Caixa não está aberto!'
            ], 400);
        }

        if ($sale->cash_register_id !== $openCashRegister->id) {
            return response()->json([
                'success' => false,
                'message' => 'Esta venda pertence a outro caixa!'
            ], 403);
        }

        // Validar mesa se table_id foi alterado ou fornecido
        $oldTableId = $sale->table_id;
        $newTableId = $validated['table_id'] ?? null;
        
        if ($newTableId && $newTableId != $oldTableId) {
            // Mesa foi alterada, validar nova mesa
            $newTable = Table::find($newTableId);
            
            if (!$newTable) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mesa não encontrada!'
                ], 404);
            }

            // Verificar se nova mesa está disponível
            if ($newTable->status !== 'disponivel') {
                return response()->json([
                    'success' => false,
                    'message' => "A mesa {$newTable->number} não está disponível! Status atual: " . ucfirst($newTable->status)
                ], 422);
            }

            // Verificar se nova mesa não tem venda ativa (exceto a atual)
            $activeSale = $newTable->sales()
                ->where('status', '!=', 'finalizado')
                ->where('status', '!=', 'cancelado')
                ->where('id', '!=', $sale->id)
                ->first();

            if ($activeSale) {
                return response()->json([
                    'success' => false,
                    'message' => "A mesa {$newTable->number} já possui uma venda ativa (Pedido #{$activeSale->id})!"
                ], 422);
            }
        }

        try {
            DB::beginTransaction();

            // Recalcular subtotal
            $subtotal = 0;
            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);
                $subtotal += $product->price * $item['quantity'];
            }

            // Calcular total
            $deliveryFee = $validated['delivery_fee'] ?? 0;
            $discount = $validated['discount'] ?? 0;
            $total = $subtotal + $deliveryFee - $discount;

            // Determinar status da venda
            $finalize = $validated['finalize'] ?? false;
            $closeAccount = $request->input('close_account', false); // Indica se quer fechar a conta da mesa
            $currentStatus = $sale->status;
            
            if ($finalize) {
                // Se está finalizando, determinar status apropriado
                if ($validated['type'] === 'delivery') {
                    $status = 'saiu_entrega'; // Delivery vai direto para entrega
                } else {
                    // Balcão
                    if (empty($validated['table_id'])) {
                        $status = 'finalizado'; // Venda rápida sem mesa
                    } else {
                        // Com mesa: verificar se quer fechar a conta ou apenas atualizar o pedido
                        // close_account = true → finaliza (fechar conta/imprimir recibo)
                        // close_account = false → permanece pendente (apenas atualizar itens)
                        $status = $closeAccount ? 'finalizado' : 'pendente';
                    }
                }
            } else {
                // Não está finalizando, manter status atual se a venda ainda não foi finalizada/cancelada
                // Caso contrário, permitir apenas atualizações em vendas pendentes
                if (in_array($currentStatus, ['finalizado', 'cancelado', 'entregue'])) {
                    // Não permitir alterar venda já finalizada/cancelada/entregue
                    return response()->json([
                        'success' => false,
                        'message' => 'Não é possível editar uma venda que já foi finalizada, cancelada ou entregue!'
                    ], 422);
                }
                // Manter o status atual
                $status = $currentStatus;
            }

            // Limpar itens antigos e recriar
            $sale->items()->delete();

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

            // Atualizar venda
            $sale->update([
                'customer_id' => $validated['customer_id'] ?? null,
                'table_id' => $validated['table_id'] ?? null,
                'motoboy_id' => $validated['motoboy_id'] ?? null,
                'type' => $validated['type'],
                'status' => $status,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'delivery_fee' => $deliveryFee,
                'total' => $total,
                'payment_method' => $paymentMethod,
                'payment_methods_split' => $paymentMethodsSplit,
                'amount_received' => $amountReceived,
                'change_amount' => $changeAmount,
                'delivery_address' => $validated['delivery_address'] ?? null,
            ]);

            // Criar itens novamente
            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);
                $itemSubtotal = $product->price * $item['quantity'];

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                    'subtotal' => $itemSubtotal,
                ]);
            }

            // Gerenciar status das mesas
            // Se a venda está sendo finalizada
            if ($sale->status === 'finalizado') {
                // Liberar mesa atual se houver
                if ($sale->table_id) {
                    $sale->table->update(['status' => 'disponivel']);
                }
            } else {
                // Venda ainda está ativa, gerenciar mudança de mesa
                if ($oldTableId != $newTableId) {
                    // Mesa foi alterada
                    if ($oldTableId) {
                        // Liberar mesa antiga
                        $oldTable = Table::find($oldTableId);
                        if ($oldTable) {
                            // Verificar se não há outras vendas ativas na mesa antiga
                            $otherActiveSales = Sale::where('table_id', $oldTableId)
                                ->where('status', '!=', 'finalizado')
                                ->where('status', '!=', 'cancelado')
                                ->where('id', '!=', $sale->id)
                                ->count();
                            
                            if ($otherActiveSales == 0) {
                                $oldTable->update(['status' => 'disponivel']);
                            }
                        }
                    }
                    
                    // Ocupar nova mesa
                    if ($newTableId) {
                        $newTable = Table::find($newTableId);
                        if ($newTable) {
                            $newTable->update(['status' => 'ocupada']);
                        }
                    }
                } else if ($newTableId && !$oldTableId) {
                    // Nova mesa foi atribuída (sem ter mesa antes)
                    $newTable = Table::find($newTableId);
                    if ($newTable) {
                        $newTable->update(['status' => 'ocupada']);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Venda atualizada com sucesso!',
                'sale' => $sale
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Erro: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a new sale
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:balcao,delivery',
            'table_id' => 'nullable|exists:tables,id',
            'customer_id' => 'nullable|exists:customers,id',
            'motoboy_id' => 'nullable|exists:motoboys,id',
            'delivery_address' => 'nullable|string',
            'delivery_fee' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:dinheiro,pix,cartao_debito,cartao_credito,split',
            'payment_methods_split' => 'nullable|array', // Array de pagamentos divididos
            'payment_methods_split.*.method' => 'required_with:payment_methods_split|in:dinheiro,pix,cartao_debito,cartao_credito',
            'payment_methods_split.*.value' => 'required_with:payment_methods_split|numeric|min:0',
            'payment_methods_split.*.amount_received' => 'nullable|numeric|min:0',
            'payment_methods_split.*.change_amount' => 'nullable|numeric|min:0',
            'amount_received' => 'nullable|numeric|min:0', // Valor recebido (dinheiro)
            'change_amount' => 'nullable|numeric|min:0', // Troco
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'finalize' => 'nullable|boolean',
        ]);

        // Validações específicas
        if ($validated['type'] === 'delivery') {
            if (empty($validated['motoboy_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selecione um motoboy!'
                ], 422);
            }
        }

        $openCashRegister = CashRegister::where('status', 'aberto')->first();
        if (!$openCashRegister) {
            return response()->json([
                'success' => false,
                'message' => 'Caixa não está aberto!'
            ], 400);
        }

        // Validar mesa ocupada se table_id foi fornecido
        if (!empty($validated['table_id'])) {
            $table = Table::find($validated['table_id']);
            
            if (!$table) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mesa não encontrada!'
                ], 404);
            }

            // Verificar se mesa está disponível
            if ($table->status !== 'disponivel') {
                return response()->json([
                    'success' => false,
                    'message' => "A mesa {$table->number} não está disponível! Status atual: " . ucfirst($table->status)
                ], 422);
            }

            // Verificar se mesa não tem venda ativa
            $activeSale = $table->sales()
                ->where('status', '!=', 'finalizado')
                ->where('status', '!=', 'cancelado')
                ->first();

            if ($activeSale) {
                return response()->json([
                    'success' => false,
                    'message' => "A mesa {$table->number} já possui uma venda ativa (Pedido #{$activeSale->id})!"
                ], 422);
            }
        }

        try {
            DB::beginTransaction();

            // Calcular subtotal
            $subtotal = 0;
            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);
                $subtotal += $product->price * $item['quantity'];
            }

            // Calcular total
            $deliveryFee = $validated['delivery_fee'] ?? 0;
            $discount = $validated['discount'] ?? 0;
            $total = $subtotal + $deliveryFee - $discount;

            // Determinar status inicial
            $finalize = $validated['finalize'] ?? false;
            
            if ($finalize) {
                // Se está finalizando
                if ($validated['type'] === 'delivery') {
                    $status = 'saiu_entrega'; // Delivery vai direto para entrega
                } else {
                    // Balcão
                    if (empty($validated['table_id'])) {
                        $status = 'finalizado'; // Venda rápida sem mesa
                    } else {
                        $status = 'pendente'; // Com mesa, fica pendente
                    }
                }
            } else {
                $status = 'pendente'; // Salvando sem finalizar
            }

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

            // Criar venda
            $sale = Sale::create([
                'cash_register_id' => $openCashRegister->id,
                'user_id' => Auth::id(),
                'customer_id' => $validated['customer_id'] ?? null,
                'table_id' => $validated['table_id'] ?? null,
                'motoboy_id' => $validated['motoboy_id'] ?? null,
                'type' => $validated['type'],
                'status' => $status,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'delivery_fee' => $deliveryFee,
                'total' => $total,
                'payment_method' => $paymentMethod,
                'payment_methods_split' => $paymentMethodsSplit,
                'amount_received' => $amountReceived,
                'change_amount' => $changeAmount,
                'delivery_address' => $validated['delivery_address'] ?? null,
            ]);

            // Criar itens
            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);
                $itemSubtotal = $product->price * $item['quantity'];

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                    'subtotal' => $itemSubtotal,
                ]);
            }

            // Atualizar mesa se necessário
            if ($sale->table_id && $status !== 'finalizado') {
                $sale->table->update(['status' => 'ocupada']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Venda criada com sucesso!',
                'sale' => $sale
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Erro: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get sale data for POS
     */
    public function getPosData(Sale $sale)
    {
        $sale->load(['customer', 'table', 'motoboy', 'items.product.category']);

        $items = $sale->items->map(function ($item) {
            return [
                'product_id' => $item->product_id,
                'name' => $item->product->name,
                'price' => (float) $item->unit_price,
                'category' => $item->product->category?->name ?? 'Sem categoria',
                'quantity' => (int) $item->quantity,
                'subtotal' => (float) $item->subtotal,
            ];
        })->toArray();

        return response()->json([
            'success' => true,
            'sale' => [
                'id' => $sale->id,
                'type' => $sale->type,
                'status' => $sale->status,
                'customer_id' => $sale->customer_id,
                'table_id' => $sale->table_id,
                'motoboy_id' => $sale->motoboy_id,
                'payment_method' => $sale->payment_method,
                'delivery_address' => $sale->delivery_address,
                'delivery_fee' => (float) ($sale->delivery_fee ?? 0),
                'discount' => (float) ($sale->discount ?? 0),
                'subtotal' => (float) $sale->subtotal,
                'total' => (float) $sale->total,
                'items' => $items
            ]
        ]);
    }

    /**
     * Update sale status
     */
    public function updateStatus(Request $request, Sale $sale)
    {
        $validated = $request->validate([
            'status' => 'required|in:pendente,em_preparo,pronto,saiu_entrega,entregue,cancelado,finalizado',
        ]);

        $oldStatus = $sale->status;
        $sale->update(['status' => $validated['status']]);

        // Recarregar o objeto do banco para garantir dados atualizados
        $sale->refresh();

        // Liberar mesa se finalizado/cancelado/entregue
        if (in_array($validated['status'], ['finalizado', 'entregue', 'cancelado']) && $sale->table_id) {
            $sale->table->update(['status' => 'disponivel']);
        }

        // Log para debug
        \Log::info('Status da venda atualizado', [
            'sale_id' => $sale->id,
            'old_status' => $oldStatus,
            'new_status' => $sale->status,
            'type' => $sale->type
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status atualizado!',
            'sale' => $sale->load(['customer', 'motoboy', 'table', 'items.product'])
        ]);
    }

    /**
     * Print receipt for sale
     */
    public function printReceipt(Request $request, Sale $sale)
    {
        try {
            // Carregar relacionamento de itens se não estiver carregado
            if (!$sale->relationLoaded('items')) {
                $sale->load(['customer', 'table', 'motoboy', 'user', 'items.product']);
            }

            // Obter configurações da impressora do banco de dados ou usar padrões
            $printerConfig = ThermalPrinterService::getConfigFromSettings();

            // Tentar imprimir
            ThermalPrinterService::print($sale, $printerConfig);

            return response()->json([
                'success' => true,
                'message' => 'Recibo impresso com sucesso!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao imprimir recibo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display sale details
     */
    public function show(Request $request, Sale $sale)
    {
        // Se for AJAX, retorna JSON
        if ($request->expectsJson() || $request->ajax()) {
            $sale->load(['customer', 'table', 'motoboy', 'user', 'items.product.category']);

            return response()->json([
                'success' => true,
                'sale' => $sale
            ]);
        }

        // Senão retorna view
        $sale->load(['customer', 'table', 'motoboy', 'user', 'items.product.category']);
        return view('sales.show', compact('sale'));
    }

    /**
     * Get receipt data for QZ Tray printing
     * Returns formatted data for ESC/POS printing via browser
     */
    public function getReceiptData(Sale $sale)
    {
        try {
            // Carregar relacionamentos necessários
            $sale->load(['customer', 'table', 'motoboy', 'user', 'items.product']);

            // Formatar dados para o QZ Tray
            $receiptData = [
                'business_name' => 'Doce Doce Brigaderia',
                'order_number' => $sale->id,
                'date' => $sale->created_at->format('d/m/Y H:i'),
                'items' => $sale->items->map(function ($item) {
                    return [
                        'name' => $item->product->name ?? 'Produto',
                        'quantity' => $item->quantity,
                        'price' => $item->unit_price,
                        'subtotal' => $item->subtotal
                    ];
                })->toArray(),
                'subtotal' => $sale->subtotal,
                'discount' => $sale->discount ?? 0,
                'delivery_fee' => $sale->delivery_fee ?? 0,
                'total' => $sale->total,
                'payment_method' => $sale->payment_method,
                'order_type' => $sale->type,
                'footer' => 'Obrigado pela preferencia!'
            ];

            // Adicionar dados do cliente se existir
            if ($sale->customer) {
                $receiptData['customer_name'] = $sale->customer->name;
                $receiptData['customer_phone'] = $sale->customer->phone;
            }

            // Adicionar endereço de entrega se for delivery
            if ($sale->delivery_address) {
                $receiptData['delivery_address'] = $sale->delivery_address;
            }

            // Adicionar mesa se for balcão
            if ($sale->table) {
                $receiptData['table_number'] = $sale->table->number;
            }

            return response()->json([
                'success' => true,
                'receipt' => $receiptData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao obter dados do recibo: ' . $e->getMessage()
            ], 500);
        }
    }
}
