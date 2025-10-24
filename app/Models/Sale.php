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
}
