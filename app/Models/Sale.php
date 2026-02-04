<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Enums\SaleStatus;
use App\Enums\SaleType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'cash_register_id',
        'user_id',
        'customer_id',
        'table_id',
        'motoboy_id',
        'code',
        'type',
        'status',
        'subtotal',
        'discount',
        'delivery_fee',
        'total',
        'payment_method',
        'payment_methods_split', // JSON com múltiplas formas de pagamento
        'amount_received', // Valor recebido do cliente (dinheiro)
        'change_amount', // Troco
        'delivery_date',
        'delivery_time',
        'notes',
        'delivery_address',
    ];

    protected $casts = [
        'type' => SaleType::class,
        'status' => SaleStatus::class,
        'payment_method' => PaymentMethod::class,
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'total' => 'decimal:2',
        'amount_received' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'payment_methods_split' => 'array',
        'delivery_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (Sale $sale): void {
            if (empty($sale->code)) {
                $sale->code = 'VEN-' . strtoupper(uniqid());
            }
        });
    }

    // Relationships
    public function cashRegister(): BelongsTo
    {
        return $this->belongsTo(CashRegister::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    public function motoboy(): BelongsTo
    {
        return $this->belongsTo(Motoboy::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    // Query Scopes
    public function scopeByStatus($query, SaleStatus $status): void
    {
        $query->where('status', $status);
    }

    public function scopeByType($query, SaleType $type): void
    {
        $query->where('type', $type);
    }

    public function scopeActive($query): void
    {
        $query->whereNotIn('status', [
            SaleStatus::FINALIZADO->value,
            SaleStatus::CANCELADO->value,
            SaleStatus::ENTREGUE->value,
        ]);
    }

    public function scopePending($query): void
    {
        $query->whereIn('status', [
            SaleStatus::PENDENTE->value,
            SaleStatus::EM_PREPARO->value,
            SaleStatus::PRONTO->value,
        ]);
    }

    public function scopeDeliveries($query): void
    {
        $query->where('type', SaleType::DELIVERY->value);
    }

    public function scopeBalcao($query): void
    {
        $query->where('type', SaleType::BALCAO->value);
    }

    // Business Logic Methods
    public function calculateTotal(): float
    {
        $this->subtotal = $this->items->sum('subtotal');
        $this->total = round($this->subtotal - ($this->discount ?? 0) + ($this->delivery_fee ?? 0), 2);
        $this->save();

        return (float) $this->total;
    }

    public function isBalcao(): bool
    {
        return $this->type === SaleType::BALCAO;
    }

    public function isDelivery(): bool
    {
        return $this->type === SaleType::DELIVERY;
    }

    public function isEncomenda(): bool
    {
        return $this->type === SaleType::ENCOMENDA;
    }

    public function isPendente(): bool
    {
        return $this->status === SaleStatus::PENDENTE;
    }

    public function isCancelado(): bool
    {
        return $this->status === SaleStatus::CANCELADO;
    }

    public function isFinalizado(): bool
    {
        return $this->status === SaleStatus::FINALIZADO;
    }

    public function canBeEdited(): bool
    {
        return !in_array($this->status, [
            SaleStatus::FINALIZADO,
            SaleStatus::CANCELADO,
            SaleStatus::ENTREGUE,
        ], true);
    }

    public static function getStatusConfig(SaleStatus|string $status): array
    {
        $statusEnum = $status instanceof SaleStatus ? $status : SaleStatus::tryFrom($status) ?? SaleStatus::PENDENTE;
        
        return [
            'label' => $statusEnum->label(),
            'icon' => $statusEnum->icon(),
            'bg' => $statusEnum->bgClass(),
        ];
    }

    public static function getAvailableStatuses(): array
    {
        return SaleStatus::options();
    }

    /**
     * Verifica se é pagamento dividido
     */
    public function isSplitPayment(): bool
    {
        return !empty($this->payment_methods_split) && count($this->payment_methods_split) > 0;
    }

    /**
     * Obter nome legível do método de pagamento
     */
    public static function getPaymentMethodName(PaymentMethod|string $method): string
    {
        if ($method instanceof PaymentMethod) {
            return $method->label();
        }

        $paymentMethod = PaymentMethod::tryFrom($method);
        return $paymentMethod?->label() ?? $method;
    }

    /**
     * Obter resumo dos métodos de pagamento formatado
     */
    public function getPaymentSummaryAttribute(): string
    {
        if ($this->isSplitPayment()) {
            $parts = [];
            foreach ($this->payment_methods_split as $payment) {
                $method = PaymentMethod::tryFrom($payment['method'] ?? '');
                $methodName = $method?->label() ?? $payment['method'] ?? '';
                $value = number_format($payment['value'] ?? 0, 2, ',', '.');
                $parts[] = "{$methodName}: R$ {$value}";
            }
            return implode(' + ', $parts);
        }
        
        return $this->payment_method instanceof PaymentMethod 
            ? $this->payment_method->label() 
            : self::getPaymentMethodName($this->payment_method);
    }

    /**
     * Verifica se tem troco
     */
    public function hasChange(): bool
    {
        return $this->change_amount && $this->change_amount > 0;
    }
}
