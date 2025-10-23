<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashRegister extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'opening_balance',
        'closing_balance',
        'opened_at',
        'closed_at',
        'status',
        'opening_notes',
        'closing_notes',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'closing_balance' => 'decimal:2',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function isOpen()
    {
        return $this->status === 'aberto';
    }

    public function isClosed()
    {
        return $this->status === 'fechado';
    }

    public function getTotalSales()
    {
        return $this->sales()
            ->whereNotIn('status', ['cancelado'])
            ->sum('total');
    }

    public function getTotalExpenses()
    {
        return $this->expenses()
            ->where('type', 'saida')
            ->sum('amount');
    }

    public function getTotalRevenues()
    {
        return $this->expenses()
            ->where('type', 'entrada')
            ->sum('amount');
    }

    public function getExpectedBalance()
    {
        return $this->opening_balance + 
               $this->getTotalSales() + 
               $this->getTotalRevenues() - 
               $this->getTotalExpenses();
    }
}
