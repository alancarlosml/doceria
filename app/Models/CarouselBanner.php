<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CarouselBanner extends Model
{
    protected $fillable = [
        'image',
        'title',
        'description',
        'link',
        'order',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Scope para banners ativos ordenados
     */
    public function scopeActive($query)
    {
        return $query->where('active', true)->orderBy('order');
    }

    /**
     * Get all active banners for the carousel
     */
    public static function getActiveBanners()
    {
        return static::active()->get();
    }

    /**
     * Get the full URL for the banner image
     */
    public function getImageUrlAttribute()
    {
        if ($this->image && Storage::disk('public')->exists($this->image)) {
            return asset('storage/' . $this->image);
        }
        return null;
    }

    /**
     * Delete the banner image from storage
     */
    public function deleteImage()
    {
        if ($this->image && Storage::disk('public')->exists($this->image)) {
            Storage::disk('public')->delete($this->image);
        }
    }
}

