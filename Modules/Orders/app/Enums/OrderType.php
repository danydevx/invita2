<?php

namespace Modules\Orders\Enums;

enum OrderType: string
{
    case DELIVERY = 'delivery';
    case PICKUP = 'pickup';

    public function label(): string
    {
        return match($this) {
            self::DELIVERY => 'Delivery',
            self::PICKUP => 'Recoger en local',
        };
    }
}
