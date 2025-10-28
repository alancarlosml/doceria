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
    ];

    protected $casts = [
        'delivery_date' => 'date',
        'delivery_time' => 'datetime:H:i',
        'delivery_fee' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'custom_costs' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
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

    public function customer()
    {
        return $this->belongsTo(Customer::class);
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
}
