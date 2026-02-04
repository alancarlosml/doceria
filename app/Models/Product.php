<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    // Relationships
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    // Query Scopes
    public function scopeActive($query): void
    {
        $query->where('active', true);
    }

    public function scopeInactive($query): void
    {
        $query->where('active', false);
    }

    public function scopeByCategory($query, int $categoryId): void
    {
        $query->where('category_id', $categoryId);
    }

    public function scopeAvailableOnDay($query, string $dayOfWeek): void
    {
        $query->whereHas('menus', function ($q) use ($dayOfWeek) {
            $q->where('day_of_week', $dayOfWeek)
              ->where('available', true);
        });
    }

    public function scopeSearch($query, string $search): void
    {
        $query->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
    }

    // Business Logic Methods
    public function isAvailableOnDay(string $dayOfWeek): bool
    {
        return $this->menus()
            ->where('day_of_week', $dayOfWeek)
            ->where('available', true)
            ->exists();
    }

    public function getAvailableDays(): array
    {
        return $this->menus()
            ->where('available', true)
            ->pluck('day_of_week')
            ->toArray();
    }

    public function hasSales(): bool
    {
        return $this->saleItems()->exists();
    }

    public function getTotalSalesQuantity(): int
    {
        return $this->saleItems()->sum('quantity');
    }

    public function getTotalRevenue(): float
    {
        return $this->saleItems()->sum('subtotal');
    }
}