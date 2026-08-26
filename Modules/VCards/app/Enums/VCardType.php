<?php

namespace Modules\VCards\Enums;

enum VCardType: string
{
    case SINGLE = 'single';
    case TEAM = 'team';

    public function label(): string
    {
        return match ($this) {
            self::SINGLE => 'Individual',
            self::TEAM => 'Equipo',
        };
    }
}
