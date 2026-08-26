<?php

namespace Modules\VCards\Enums;

enum VCardDesign: string
{
    case CLASSIC = 'classic';
    case FLAT = 'flat';
    case MODERN = 'modern';
    case SLEEK = 'sleek';
    case BLEND = 'blend';

    public function label(): string
    {
        return match ($this) {
            self::CLASSIC => 'Classic',
            self::FLAT => 'Flat',
            self::MODERN => 'Modern',
            self::SLEEK => 'Sleek',
            self::BLEND => 'Blend',
        };
    }
}
