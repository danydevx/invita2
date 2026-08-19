<?php

namespace Modules\Leads\Enums;

enum LeadSource: string
{
    case MANUAL = 'manual';
    case WEBSITE = 'website';
    case PHONE = 'phone';
    case WALK_IN = 'walk_in';
    case REFERRAL = 'referral';
    case SOCIAL_MEDIA = 'social_media';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::MANUAL => 'Manual',
            self::WEBSITE => 'Website',
            self::PHONE => 'Phone',
            self::WALK_IN => 'Walk-in',
            self::REFERRAL => 'Referral',
            self::SOCIAL_MEDIA => 'Social Media',
            self::OTHER => 'Other',
        };
    }
}
