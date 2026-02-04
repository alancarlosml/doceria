<?php

namespace App\Enums;

enum SaleType: string
{
    case BALCAO = 'balcao';
    case DELIVERY = 'delivery';
    case ENCOMENDA = 'encomenda';

    public function label(): string
    {
        return match($this) {
            self::BALCAO => 'Balcão',
            self::DELIVERY => 'Delivery',
            self::ENCOMENDA => 'Encomenda',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return array_combine(
            array_column(self::cases(), 'value'),
            array_map(fn($case) => $case->label(), self::cases())
        );
    }
}
