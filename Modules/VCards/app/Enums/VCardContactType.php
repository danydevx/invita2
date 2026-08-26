<?php

namespace Modules\VCards\Enums;

enum VCardContactType: string
{
    case PHONE = 'phone';
    case EMAIL = 'email';
    case WHATSAPP = 'whatsapp';

    public function label(): string
    {
        return match ($this) {
            self::PHONE => 'Teléfono',
            self::EMAIL => 'Email',
            self::WHATSAPP => 'WhatsApp',
        };
    }
}
