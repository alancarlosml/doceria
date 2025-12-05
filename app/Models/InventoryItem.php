<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'current_quantity',
        'min_quantity',
        'unit',
        'last_updated_by',
        'notes',
        'active',
    ];

    protected $casts = [
        'current_quantity' => 'decimal:2',
        'min_quantity' => 'decimal:2',
        'active' => 'boolean',
    ];

    /**
     * Usuário que fez a última atualização
     */
    public function lastUpdatedBy()
    {
        return $this->belongsTo(User::class, 'last_updated_by');
    }

    /**
     * Verifica se o estoque está baixo
     */
    public function isLowStock()
    {
        return $this->current_quantity <= $this->min_quantity;
    }

    /**
     * Verifica se o estoque está crítico (menos de 50% da quantidade mínima)
     */
    public function isCriticalStock()
    {
        return $this->current_quantity < ($this->min_quantity * 0.5);
    }

    /**
     * Retorna a porcentagem do estoque em relação ao mínimo
     */
    public function getStockPercentage()
    {
        if ($this->min_quantity == 0) {
            return 100;
        }
        return min(100, ($this->current_quantity / $this->min_quantity) * 100);
    }
}
