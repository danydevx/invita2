<?php

namespace Modules\VCards\Enums;

enum VCardPronouns: string
{
    case HE = 'he';
    case SHE = 'she';
    case THEY = 'they';

    public function label(): string
    {
        return match ($this) {
            self::HE => 'Él',
            self::SHE => 'Ella',
            self::THEY => 'Ellos / Ellas',
        };
    }
}
