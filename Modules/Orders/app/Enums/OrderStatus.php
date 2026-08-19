<?php

namespace Modules\Orders\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case PREPARING = 'preparing';
    case READY = 'ready';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pendiente',
            self::CONFIRMED => 'Confirmado',
            self::PREPARING => 'En preparación',
            self::READY => 'Listo',
            self::COMPLETED => 'Completado',
            self::CANCELLED => 'Cancelado',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'bg-warning text-dark',
            self::CONFIRMED => 'bg-info',
            self::PREPARING => 'bg-primary',
            self::READY => 'bg-success',
            self::COMPLETED => 'bg-secondary',
            self::CANCELLED => 'bg-danger',
        };
    }
}
