<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'day_of_week',
        'available',
    ];

    protected $casts = [
        'available' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}