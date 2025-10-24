<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
    ];

    protected $casts = [
        'value' => 'string',
    ];

    /**
     * Get a setting value
     */
    public static function get(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();

        if (!$setting) {
            return $default;
        }

        return $setting->getParsedValue();
    }

    /**
     * Set a setting value
     */
    public static function set(string $key, $value, string $type = 'string')
    {
        if ($type === 'boolean') {
            $value = $value ? '1' : '0';
        } elseif ($type === 'integer') {
            $value = (string) (int) $value;
        } elseif ($type === 'json') {
            $value = json_encode($value);
        } else {
            $value = (string) $value;
        }

        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
            ]
        );
    }

    /**
     * Get parsed value based on type
     */
    public function getParsedValue()
    {
        switch ($this->type) {
            case 'boolean':
                return $this->value === '1';
            case 'integer':
                return (int) $this->value;
            case 'json':
                return json_decode($this->value, true);
            default:
                return $this->value;
        }
    }

    /**
     * Get banner message if active
     */
    public static function getBannerMessage(): ?string
    {
        $setting = static::where('key', 'banner_message')->first();

        if (!$setting || empty($setting->value)) {
            return null;
        }

        $isActive = static::get('banner_active', false);
        return $isActive ? $setting->getParsedValue() : null;
    }

    /**
     * Check if store is open based on manual status setting
     */
    public static function isStoreOpen(): bool
    {
        $status = static::get('store_status', 'closed');
        return $status === 'open';
    }

    /**
     * Check if there is an open cash register (for internal use)
     */
    public static function hasOpenCashRegister(): bool
    {
        return \App\Models\CashRegister::where('status', 'aberto')->exists();
    }

    /**
     * Get current store status with context
     */
    public static function getStoreStatus(): array
    {
        $isOpen = static::isStoreOpen();
        $hasOpenCashRegister = static::hasOpenCashRegister();

        return [
            'is_open' => $isOpen,
            'has_open_register' => $hasOpenCashRegister,
            'status_message' => $isOpen ? 'Loja Aberta' : 'Loja Fechada',
            'context_message' => $isOpen ? 'Estamos atendendo' : 'indisponíveis no momento'
        ];
    }
}
