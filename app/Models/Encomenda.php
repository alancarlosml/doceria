<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Encomenda extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'cash_register_id',
        'customer_id',
        'code',
        'title',
        'description',
        'notes',
        'status',
        'delivery_date',
        'delivery_time',
        'delivery_address',
        'delivery_fee',
        'subtotal',
        'custom_costs',
        'discount',
        'total',
        'payment_method', // Método de pagamento principal
        'payment_methods_split', // JSON com múltiplas formas de pagamento
        'amount_received', // Valor recebido do cliente (dinheiro)
        'change_amount', // Troco
    ];

    protected $casts = [
        'delivery_date' => 'date',
        'delivery_time' => 'string',
        'delivery_fee' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'custom_costs' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'amount_received' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'payment_methods_split' => 'array', // JSON para array
    ];

    protected $attributes = [
        'delivery_fee' => 0,
        'custom_costs' => 0,
        'discount' => 0,
        'status' => 'pendente',
    ];

    // Relacionamentos
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cashRegister()
    {
        return $this->belongsTo(CashRegister::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(EncomendaItem::class);
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeForDeliveryDate($query, $date)
    {
        return $query->whereDate('delivery_date', $date);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pendente');
    }

    public function scopeInProduction($query)
    {
        return $query->where('status', 'em_producao');
    }

    public function scopeReady($query)
    {
        return $query->where('status', 'pronto');
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'entregue');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelado');
    }

    // Métodos helpers
    public static function generateCode()
    {
        do {
            $code = 'ENC-' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
        } while (self::where('code', $code)->exists());

        return $code;
    }

    public function calculateTotal()
    {
        return $this->subtotal + $this->custom_costs + $this->delivery_fee - $this->discount;
    }

    public function updateStatus($newStatus)
    {
        $validStatuses = ['pendente', 'em_producao', 'pronto', 'entregue', 'cancelado'];

        if (!in_array($newStatus, $validStatuses)) {
            throw new \InvalidArgumentException('Status inválido');
        }

        $this->update(['status' => $newStatus]);

        return $this;
    }

    // Accessors
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pendente' => 'Pendente',
            'em_producao' => 'Em Produção',
            'pronto' => 'Pronto',
            'entregue' => 'Entregue',
            'cancelado' => 'Cancelado',
            default => 'Desconhecido'
        };
    }

    public function getTotalFormattedAttribute()
    {
        return 'R$ ' . number_format($this->total, 2, ',', '.');
    }

    public function getSubtotalFormattedAttribute()
    {
        return 'R$ ' . number_format($this->subtotal, 2, ',', '.');
    }

    public function getDeliveryFeeFormattedAttribute()
    {
        return 'R$ ' . number_format($this->delivery_fee, 2, ',', '.');
    }

    public function getCustomCostsFormattedAttribute()
    {
        return 'R$ ' . number_format($this->custom_costs, 2, ',', '.');
    }

    public function getDiscountFormattedAttribute()
    {
        return 'R$ ' . number_format($this->discount, 2, ',', '.');
    }

    public function getDeliveryDateFormattedAttribute()
    {
        return $this->delivery_date ? $this->delivery_date->format('d/m/Y') : 'Não especificada';
    }

    public function getIsOverdueAttribute()
    {
        if ($this->delivery_date && in_array($this->status, ['pendente', 'em_producao'])) {
            return $this->delivery_date->isPast();
        }
        return false;
    }

    public function getIsDueTodayAttribute()
    {
        if ($this->delivery_date) {
            return $this->delivery_date->isToday();
        }
        return false;
    }

    public function getDaysUntilDeliveryAttribute()
    {
        if ($this->delivery_date) {
            return now()->diffInDays($this->delivery_date, false);
        }
        return null;
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
    public static function getPaymentMethodName($method): string
    {
        $methods = [
            'dinheiro' => 'Dinheiro',
            'cartao_credito' => 'Cartão Crédito',
            'cartao_debito' => 'Cartão Débito',
            'pix' => 'PIX',
            'transferencia' => 'Transferência',
        ];

        return $methods[$method] ?? $method;
    }

    /**
     * Obter resumo dos métodos de pagamento formatado
     */
    public function getPaymentSummaryAttribute(): string
    {
        if ($this->isSplitPayment()) {
            $parts = [];
            foreach ($this->payment_methods_split as $payment) {
                $methodName = self::getPaymentMethodName($payment['method']);
                $value = number_format($payment['value'], 2, ',', '.');
                $parts[] = "{$methodName}: R$ {$value}";
            }
            return implode(' + ', $parts);
        }
        
        if ($this->payment_method) {
            return self::getPaymentMethodName($this->payment_method);
        }
        
        return 'Não informado';
    }

    /**
     * Verifica se tem troco
     */
    public function hasChange(): bool
    {
        return $this->change_amount && $this->change_amount > 0;
    }

    /**
     * Obter valor do troco formatado
     */
    public function getChangeFormattedAttribute(): string
    {
        return 'R$ ' . number_format($this->change_amount ?? 0, 2, ',', '.');
    }

    /**
     * Obter valor recebido formatado
     */
    public function getAmountReceivedFormattedAttribute(): string
    {
        return 'R$ ' . number_format($this->amount_received ?? 0, 2, ',', '.');
    }
}
