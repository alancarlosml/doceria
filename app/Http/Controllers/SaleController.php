<?php

namespace App\Http\Controllers;

use App\Enums\SaleStatus;
use App\Enums\SaleType;
use App\Http\Requests\StoreSaleRequest;
use App\Http\Requests\UpdateSaleRequest;
use App\Http\Requests\UpdateSaleStatusRequest;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\Table;
use App\Models\Motoboy;
use App\Models\CashRegister;
use App\Models\Product;
use App\Models\SaleItem;
use App\Models\Category;
use App\Services\SaleService;
use App\Services\ThermalPrinterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class SaleController extends Controller
{
    public function __construct(
        private readonly SaleService $saleService
    ) {}
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
    public function update(UpdateSaleRequest $request, Sale $sale): JsonResponse
    {
        $validated = $request->validated();

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

        // Validar se venda pode ser editada
        if (!$sale->canBeEdited()) {
            return response()->json([
                'success' => false,
                'message' => 'Não é possível editar uma venda que já foi finalizada, cancelada ou entregue!'
            ], 422);
        }

        // Validar mesa se table_id foi alterado ou fornecido
        $oldTableId = $sale->table_id;
        $newTableId = $validated['table_id'] ?? null;
        
        if ($newTableId && $newTableId != $oldTableId) {
            try {
                $this->saleService->validateTableAvailability($newTableId, $sale->id);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }
        }

        try {
            DB::beginTransaction();

            // Calcular valores usando service
            $subtotal = $this->saleService->calculateSubtotal($validated['items']);
            $deliveryFee = $validated['delivery_fee'] ?? 0;
            $discount = $validated['discount'] ?? 0;
            $total = $this->saleService->calculateTotal($subtotal, $deliveryFee, $discount);

            // Determinar status da venda usando service
            $finalize = $validated['finalize'] ?? false;
            $closeAccount = $request->input('close_account', false);
            $type = SaleType::from($validated['type']);
            $currentStatus = $sale->status instanceof SaleStatus ? $sale->status : SaleStatus::from($sale->status);
            
            $status = $this->saleService->determineStatus(
                $type,
                $finalize,
                $closeAccount,
                $currentStatus
            );

            // Processar método de pagamento usando service
            $paymentData = $this->saleService->processPaymentMethod(
                \App\Enums\PaymentMethod::from($validated['payment_method']),
                $validated['payment_methods_split'] ?? null
            );

            // Atualizar venda
            $sale->update([
                'customer_id' => $validated['customer_id'] ?? null,
                'table_id' => $validated['table_id'] ?? null,
                'motoboy_id' => $validated['motoboy_id'] ?? null,
                'type' => $type,
                'status' => $status,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'delivery_fee' => $deliveryFee,
                'total' => $total,
                'payment_method' => $paymentData['payment_method'],
                'payment_methods_split' => $paymentData['payment_methods_split'],
                'amount_received' => $validated['amount_received'] ?? $paymentData['amount_received'],
                'change_amount' => $validated['change_amount'] ?? $paymentData['change_amount'],
                'delivery_address' => $validated['delivery_address'] ?? null,
            ]);

            // Atualizar itens usando service
            $this->saleService->updateSaleItems($sale, $validated['items']);

            // Gerenciar status das mesas usando service
            $this->saleService->manageTableStatus(
                $sale,
                $oldTableId,
                $newTableId,
                $status
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Venda atualizada com sucesso!',
                'sale' => $sale
            ]);

        } catch (\RuntimeException $e) {
            DB::rollBack();
            Log::warning('Erro de validação ao atualizar venda', [
                'sale_id' => $sale->id ?? null,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar venda', [
                'sale_id' => $sale->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar a solicitação. Tente novamente.'
            ], 500);
        }
    }

    /**
     * Store a new sale
     */
    public function store(StoreSaleRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $openCashRegister = CashRegister::where('status', 'aberto')->first();
        if (!$openCashRegister) {
            return response()->json([
                'success' => false,
                'message' => 'Caixa não está aberto!'
            ], 400);
        }

        // Validar mesa ocupada se table_id foi fornecido
        if (!empty($validated['table_id'])) {
            try {
                $this->saleService->validateTableAvailability($validated['table_id']);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }
        }

        try {
            DB::beginTransaction();

            // Calcular valores usando service
            $subtotal = $this->saleService->calculateSubtotal($validated['items']);
            $deliveryFee = $validated['delivery_fee'] ?? 0;
            $discount = $validated['discount'] ?? 0;
            $total = $this->saleService->calculateTotal($subtotal, $deliveryFee, $discount);

            // Determinar status inicial usando service
            $finalize = $validated['finalize'] ?? false;
            $type = SaleType::from($validated['type']);
            $status = $this->saleService->determineStatus($type, $finalize);

            // Processar método de pagamento usando service
            $paymentData = $this->saleService->processPaymentMethod(
                \App\Enums\PaymentMethod::from($validated['payment_method']),
                $validated['payment_methods_split'] ?? null
            );

            // Criar venda
            $sale = Sale::create([
                'cash_register_id' => $openCashRegister->id,
                'user_id' => Auth::id(),
                'customer_id' => $validated['customer_id'] ?? null,
                'table_id' => $validated['table_id'] ?? null,
                'motoboy_id' => $validated['motoboy_id'] ?? null,
                'type' => $type,
                'status' => $status,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'delivery_fee' => $deliveryFee,
                'total' => $total,
                'payment_method' => $paymentData['payment_method'],
                'payment_methods_split' => $paymentData['payment_methods_split'],
                'amount_received' => $validated['amount_received'] ?? $paymentData['amount_received'],
                'change_amount' => $validated['change_amount'] ?? $paymentData['change_amount'],
                'delivery_address' => $validated['delivery_address'] ?? null,
            ]);

            // Criar itens usando service
            $this->saleService->createSaleItems($sale, $validated['items']);

            // Atualizar mesa se necessário usando service
            if ($sale->table_id && $status !== SaleStatus::FINALIZADO) {
                $this->saleService->manageTableStatus($sale, null, $sale->table_id, $status);
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
    public function updateStatus(UpdateSaleStatusRequest $request, Sale $sale): JsonResponse
    {
        $validated = $request->validated();
        $status = SaleStatus::from($validated['status']);

        $oldStatus = $sale->status instanceof SaleStatus ? $sale->status : SaleStatus::from($sale->status);
        $sale->update(['status' => $status]);

        // Recarregar o objeto do banco para garantir dados atualizados
        $sale->refresh();

        // Liberar mesa se finalizado/cancelado/entregue usando service
        if (in_array($status, [SaleStatus::FINALIZADO, SaleStatus::ENTREGUE, SaleStatus::CANCELADO]) && $sale->table_id) {
            $this->saleService->manageTableStatus($sale, $sale->table_id, null, $status);
        }

        // Log para debug
        Log::info('Status da venda atualizado', [
            'sale_id' => $sale->id,
            'old_status' => $oldStatus->value,
            'new_status' => $status->value,
            'type' => $sale->type instanceof SaleType ? $sale->type->value : $sale->type
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
                'payment_method' => $sale->payment_method instanceof \App\Enums\PaymentMethod ? $sale->payment_method->value : $sale->payment_method,
                'order_type' => $sale->type instanceof SaleType ? $sale->type->value : $sale->type,
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
