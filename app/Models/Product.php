<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'cost_price',
        'image',
        'active',
        'disponivel_encomenda',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'active' => 'boolean'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function menus()
    {
        return $this->hasMany(Menu::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function isAvailableOnDay($dayOfWeek)
    {
        return $this->menus()
            ->where('day_of_week', $dayOfWeek)
            ->where('available', true)
            ->exists();
    }

    public function getAvailableDays()
    {
        return $this->menus()
            ->where('available', true)
            ->pluck('day_of_week')
            ->toArray();
    }
}