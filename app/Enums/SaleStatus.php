<?php

namespace App\Enums;

enum SaleStatus: string
{
    case PENDENTE = 'pendente';
    case EM_PREPARO = 'em_preparo';
    case PRONTO = 'pronto';
    case SAIU_ENTREGA = 'saiu_entrega';
    case ENTREGUE = 'entregue';
    case CANCELADO = 'cancelado';
    case FINALIZADO = 'finalizado';

    public function label(): string
    {
        return match($this) {
            self::PENDENTE => 'Pendente',
            self::EM_PREPARO => 'Em Preparo',
            self::PRONTO => 'Pronto',
            self::SAIU_ENTREGA => 'Saiu para Entrega',
            self::ENTREGUE => 'Entregue',
            self::CANCELADO => 'Cancelado',
            self::FINALIZADO => 'Finalizado',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::PENDENTE => '⏳',
            self::EM_PREPARO => '👨‍🍳',
            self::PRONTO => '✅',
            self::SAIU_ENTREGA => '🚴',
            self::ENTREGUE => '📦',
            self::CANCELADO => '❌',
            self::FINALIZADO => '💰',
        };
    }

    public function bgClass(): string
    {
        return match($this) {
            self::PENDENTE => 'bg-yellow-100',
            self::EM_PREPARO => 'bg-orange-100',
            self::PRONTO => 'bg-blue-100',
            self::SAIU_ENTREGA => 'bg-purple-100',
            self::ENTREGUE => 'bg-green-100',
            self::CANCELADO => 'bg-red-100',
            self::FINALIZADO => 'bg-green-100',
        };
    }

    public function textClass(): string
    {
        return match($this) {
            self::PENDENTE => 'text-yellow-800',
            self::EM_PREPARO => 'text-orange-800',
            self::PRONTO => 'text-blue-800',
            self::SAIU_ENTREGA => 'text-purple-800',
            self::ENTREGUE => 'text-green-800',
            self::CANCELADO => 'text-red-800',
            self::FINALIZADO => 'text-green-800',
        };
    }

    public function statusClasses(): string
    {
        return $this->bgClass() . ' ' . $this->textClass();
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
