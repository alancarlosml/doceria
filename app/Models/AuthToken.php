<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AuthToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'token',
        'name',
        'abilities',
        'last_used_at',
        'expires_at',
    ];

    protected $casts = [
        'abilities' => 'array',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Generate a new token.
     */
    public static function generateToken($length = 64)
    {
        return Str::random($length);
    }

    /**
     * Find token by token string.
     */
    public static function findByToken($token)
    {
        return static::where('token', $token)->first();
    }

    /**
     * Update last used timestamp.
     */
    public function updateLastUsed()
    {
        $this->update(['last_used_at' => now()]);
    }

    /**
     * Check if token is expired.
     */
    public function isExpired()
    {
        if (!$this->expires_at) {
            return false;
        }

        return $this->expires_at->isPast();
    }

    /**
     * Get associated user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Revoke (delete) this token.
     */
    public function revoke()
    {
        return $this->delete();
    }
}
