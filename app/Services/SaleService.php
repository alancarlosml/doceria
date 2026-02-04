<?php

namespace App\Services;

use App\Enums\PaymentMethod;
use App\Enums\SaleStatus;
use App\Enums\SaleType;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SaleService
{
    /**
     * Calculate subtotal from items array
     */
    public function calculateSubtotal(array $items): float
    {
        $subtotal = 0;
        
        foreach ($items as $item) {
            $product = Product::findOrFail($item['product_id']);
            $subtotal += $product->price * $item['quantity'];
        }
        
        return round($subtotal, 2);
    }

    /**
     * Calculate total from subtotal, delivery fee and discount
     */
    public function calculateTotal(float $subtotal, float $deliveryFee = 0, float $discount = 0): float
    {
        return round($subtotal + $deliveryFee - $discount, 2);
    }

    /**
     * Determine sale status based on type and finalization
     */
    public function determineStatus(
        SaleType $type,
        bool $finalize,
        ?bool $closeAccount = null,
        ?SaleStatus $currentStatus = null
    ): SaleStatus {
        if (!$finalize) {
            // Se não está finalizando, manter status atual se existir, senão pendente
            return $currentStatus ?? SaleStatus::PENDENTE;
        }

        // Se está finalizando
        if ($type === SaleType::DELIVERY) {
            return SaleStatus::SAIU_ENTREGA;
        }

        // Balcão
        if ($closeAccount) {
            return SaleStatus::FINALIZADO;
        }

        return SaleStatus::PENDENTE;
    }

    /**
     * Process payment method and split payments
     */
    public function processPaymentMethod(
        PaymentMethod $paymentMethod,
        ?array $paymentMethodsSplit = null
    ): array {
        $result = [
            'payment_method' => $paymentMethod,
            'payment_methods_split' => null,
            'amount_received' => null,
            'change_amount' => null,
        ];

        if ($paymentMethod === PaymentMethod::SPLIT && !empty($paymentMethodsSplit)) {
            $result['payment_methods_split'] = $paymentMethodsSplit;
            // Usar o primeiro método como principal para compatibilidade
            $result['payment_method'] = PaymentMethod::from($paymentMethodsSplit[0]['method'] ?? PaymentMethod::DINHEIRO->value);
            
            // Calcular troco total para pagamentos em dinheiro
            $totalChange = 0;
            foreach ($paymentMethodsSplit as $split) {
                if ($split['method'] === PaymentMethod::DINHEIRO->value && isset($split['change_amount'])) {
                    $totalChange += $split['change_amount'];
                }
            }
            $result['change_amount'] = $totalChange > 0 ? round($totalChange, 2) : null;
        }

        return $result;
    }

    /**
     * Create sale items from array
     */
    public function createSaleItems(Sale $sale, array $items): void
    {
        if (empty($items)) {
            return;
        }

        // Otimização: buscar todos os produtos de uma vez para evitar N+1 queries
        $productIds = array_unique(array_column($items, 'product_id'));
        $products = Product::whereIn('id', $productIds)
            ->pluck('price', 'id')
            ->toArray();

        $saleItems = [];
        foreach ($items as $item) {
            $productId = $item['product_id'];
            if (!isset($products[$productId])) {
                throw new \RuntimeException("Produto ID {$productId} não encontrado.");
            }
            
            $unitPrice = $products[$productId];
            $quantity = (int) $item['quantity'];
            if ($quantity <= 0) {
                throw new \RuntimeException("Quantidade inválida para produto ID {$productId}.");
            }
            $itemSubtotal = $unitPrice * $quantity;

            $saleItems[] = [
                'sale_id' => $sale->id,
                'product_id' => $productId,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'subtotal' => round($itemSubtotal, 2),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Inserir todos os itens de uma vez (mais eficiente)
        SaleItem::insert($saleItems);
    }

    /**
     * Update sale items (delete old and create new)
     */
    public function updateSaleItems(Sale $sale, array $items): void
    {
        // Limpar itens antigos
        $sale->items()->delete();
        
        // Criar novos itens
        $this->createSaleItems($sale, $items);
    }

    /**
     * Validate and manage table status
     */
    public function manageTableStatus(
        Sale $sale,
        ?int $oldTableId,
        ?int $newTableId,
        SaleStatus $status
    ): void {
        // Se a venda está sendo finalizada, cancelada ou entregue
        if (in_array($status, [SaleStatus::FINALIZADO, SaleStatus::CANCELADO, SaleStatus::ENTREGUE])) {
            // Liberar mesa atual se houver
            if ($sale->table_id) {
                $table = Table::find($sale->table_id);
                if ($table) {
                    $table->update(['status' => 'disponivel']);
                }
            }
            return;
        }

        // Venda ainda está ativa, gerenciar mudança de mesa
        if ($oldTableId != $newTableId) {
            // Mesa foi alterada
            if ($oldTableId) {
                // Liberar mesa antiga
                $oldTable = Table::find($oldTableId);
                if ($oldTable) {
                    // Verificar se não há outras vendas ativas na mesa antiga
                    $otherActiveSales = Sale::where('table_id', $oldTableId)
                        ->where('status', '!=', SaleStatus::FINALIZADO->value)
                        ->where('status', '!=', SaleStatus::CANCELADO->value)
                        ->where('id', '!=', $sale->id)
                        ->count();
                    
                    if ($otherActiveSales == 0) {
                        $oldTable->update(['status' => 'disponivel']);
                    }
                }
            }
            
            // Ocupar nova mesa
            if ($newTableId) {
                /** @var Table $newTable */
                $newTable = Table::findOrFail($newTableId);
                $newTable->update(['status' => 'ocupada']);
            }
        } elseif ($newTableId && !$oldTableId) {
            // Nova mesa foi atribuída (sem ter mesa antes)
            /** @var Table $newTable */
            $newTable = Table::findOrFail($newTableId);
            $newTable->update(['status' => 'ocupada']);
        }
    }

    /**
     * Validate table availability
     */
    public function validateTableAvailability(?int $tableId, ?int $excludeSaleId = null): void
    {
        if (empty($tableId)) {
            return;
        }

        $table = Table::findOrFail($tableId);

        // Verificar se mesa está disponível
        if ($table->status !== 'disponivel') {
            throw new \Exception("A mesa {$table->number} não está disponível! Status atual: " . ucfirst($table->status));
        }

        // Verificar se mesa não tem venda ativa
        $query = $table->sales()
            ->where('status', '!=', SaleStatus::FINALIZADO->value)
            ->where('status', '!=', SaleStatus::CANCELADO->value);

        if ($excludeSaleId) {
            $query->where('id', '!=', $excludeSaleId);
        }

        $activeSale = $query->first();

        if ($activeSale) {
            throw new \Exception("A mesa {$table->number} já possui uma venda ativa (Pedido #{$activeSale->id})!");
        }
    }
}
