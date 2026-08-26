<?php

namespace Modules\VCards\Enums;

enum VCardContactSubtype: string
{
    case PERSONAL = 'personal';
    case WORK = 'work';
    case HOME = 'home';

    public function label(): string
    {
        return match ($this) {
            self::PERSONAL => 'Personal',
            self::WORK => 'Trabajo',
            self::HOME => 'Casa',
        };
    }
}
