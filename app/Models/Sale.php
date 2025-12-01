<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'total' => 'decimal:2',
        'amount_received' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'payment_methods_split' => 'array', // JSON para array
        'delivery_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($sale) {
            if (empty($sale->code)) {
                $sale->code = 'VEN-' . strtoupper(uniqid());
            }
        });
    }

    public function cashRegister()
    {
        return $this->belongsTo(CashRegister::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function motoboy()
    {
        return $this->belongsTo(Motoboy::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function calculateTotal()    
    {
        $this->subtotal = $this->items->sum('subtotal');
        $this->total = $this->subtotal - $this->discount + $this->delivery_fee;
        $this->save();

        return $this->total;
    }

    public function isBalcao()
    {
        return $this->type === 'balcao';
    }

    public function isDelivery()
    {
        return $this->type === 'delivery';
    }

    public function isEncomenda()
    {
        return $this->type === 'encomenda';
    }

    public function isPendente()
    {
        return $this->status === 'pendente';
    }

    public function isCancelado()
    {
        return $this->status === 'cancelado';
    }

    public function isFinalizado()
    {
        return $this->status === 'finalizado';
    }

    public static function getStatusConfig($status)
    {
        $configs = [
            'pendente' => [
                'label' => 'Pendente',
                'icon' => '⏳',
                'bg' => 'bg-yellow-100'
            ],
            'em_preparo' => [
                'label' => 'Em Preparo',
                'icon' => '👨‍🍳',
                'bg' => 'bg-orange-100'
            ],
            'pronto' => [
                'label' => 'Pronto',
                'icon' => '✅',
                'bg' => 'bg-blue-100'
            ],
            'saiu_entrega' => [
                'label' => 'Saiu para Entrega',
                'icon' => '🚴',
                'bg' => 'bg-purple-100'
            ],
            'entregue' => [
                'label' => 'Entregue',
                'icon' => '📦',
                'bg' => 'bg-green-100'
            ],
            'cancelado' => [
                'label' => 'Cancelado',
                'icon' => '❌',
                'bg' => 'bg-red-100'
            ],
            'finalizado' => [
                'label' => 'Finalizado',
                'icon' => '💰',
                'bg' => 'bg-green-100'
            ],
        ];

        return $configs[$status] ?? $configs['pendente'];
    }

    public static function getAvailableStatuses()
    {
        return [
            'pendente' => 'Pendente',
            'em_preparo' => 'Em Preparo',
            'pronto' => 'Pronto',
            'saiu_entrega' => 'Saiu para Entrega',
            'entregue' => 'Entregue',
            'cancelado' => 'Cancelado',
            'finalizado' => 'Finalizado'
        ];
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
        
        return self::getPaymentMethodName($this->payment_method);
    }

    /**
     * Verifica se tem troco
     */
    public function hasChange(): bool
    {
        return $this->change_amount && $this->change_amount > 0;
    }
}
