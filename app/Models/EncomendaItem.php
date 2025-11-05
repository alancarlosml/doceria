<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EncomendaItem extends Model
{
    use HasFactory;

    protected $table = 'encomenda_items';

    protected $fillable = [
        'encomenda_id',
        'product_id',
        'product_name',
        'quantity',
        'unit_price',
        'subtotal',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    // Relacionamentos
    public function encomenda()
    {
        return $this->belongsTo(Encomenda::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Métodos helpers
    public function calculateSubtotal()
    {
        return $this->quantity * $this->unit_price;
    }

    public function getUnitPriceFormattedAttribute()
    {
        return number_format($this->unit_price, 2, ',', '.');
    }

    public function getSubtotalFormattedAttribute()
    {
        return number_format($this->subtotal, 2, ',', '.');
    }
}

