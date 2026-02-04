<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case DINHEIRO = 'dinheiro';
    case PIX = 'pix';
    case CARTAO_DEBITO = 'cartao_debito';
    case CARTAO_CREDITO = 'cartao_credito';
    case TRANSFERENCIA = 'transferencia';
    case SPLIT = 'split';

    public function label(): string
    {
        return match($this) {
            self::DINHEIRO => 'Dinheiro',
            self::PIX => 'PIX',
            self::CARTAO_DEBITO => 'Cartão Débito',
            self::CARTAO_CREDITO => 'Cartão Crédito',
            self::TRANSFERENCIA => 'Transferência',
            self::SPLIT => 'Dividido',
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
