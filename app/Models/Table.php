<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Table extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'number',
        'capacity',
        'status',
        'active',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'active' => 'boolean',
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function isDisponivel()
    {
        return $this->status === 'disponivel';
    }

    public function isOcupada()
    {
        return $this->status === 'ocupada';
    }

    public function isReservada()
    {
        return $this->status === 'reservada';
    }
}