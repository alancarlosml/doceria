<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'cpf',
        'address',
        'neighborhood',
        'city',
        'state',
        'zipcode',
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->address,
            $this->neighborhood,
            $this->city,
            $this->state,
            $this->zipcode,
        ]);

        return implode(', ', $parts);
    }
}