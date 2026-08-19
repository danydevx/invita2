<?php

namespace Modules\Businesses\Enums;

enum BusinessType: string
{
    case BARBER_SHOP = 'barber_shop';
    case BEAUTY_SALON = 'beauty_salon';
    case DENTIST = 'dentist';
    case MEDICAL_CLINIC = 'medical_clinic';
    case DOCTOR = 'doctor';
    case SPA = 'spa';
    case VETERINARIAN = 'veterinarian';
    case PHYSIOTHERAPIST = 'physiotherapist';
    case PSYCHOLOGIST = 'psychologist';
    case NUTRITIONIST = 'nutritionist';
    case TATTOO_STUDIO = 'tattoo_studio';
    case GENERIC = 'generic';

    public function label(): string
    {
        return match ($this) {
            self::BARBER_SHOP => 'Barber Shop',
            self::BEAUTY_SALON => 'Beauty Salon',
            self::DENTIST => 'Dentist',
            self::MEDICAL_CLINIC => 'Medical Clinic',
            self::DOCTOR => 'Doctor',
            self::SPA => 'Spa',
            self::VETERINARIAN => 'Veterinarian',
            self::PHYSIOTHERAPIST => 'Physiotherapist',
            self::PSYCHOLOGIST => 'Psychologist',
            self::NUTRITIONIST => 'Nutritionist',
            self::TATTOO_STUDIO => 'Tattoo Studio',
            self::GENERIC => 'Generic',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::BARBER_SHOP => 'bi-scissors',
            self::BEAUTY_SALON => 'bi-heart',
            self::DENTIST => 'bi-bandaid',
            self::MEDICAL_CLINIC => 'bi-hospital',
            self::DOCTOR => 'bi-activity',
            self::SPA => 'bi-flower1',
            self::VETERINARIAN => 'bi-bug',
            self::PHYSIOTHERAPIST => 'bi-person',
            self::PSYCHOLOGIST => 'bi-chat-quote',
            self::NUTRITIONIST => 'bi-egg-fried',
            self::TATTOO_STUDIO => 'bi-brush',
            self::GENERIC => 'bi-grid',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::BARBER_SHOP => '#6F42C1',
            self::BEAUTY_SALON => '#E83E8C',
            self::DENTIST => '#0DCAF0',
            self::MEDICAL_CLINIC => '#DC3545',
            self::DOCTOR => '#FD7E14',
            self::SPA => '#20C997',
            self::VETERINARIAN => '#198754',
            self::PHYSIOTHERAPIST => '#20C997',
            self::PSYCHOLOGIST => '#6F42C1',
            self::NUTRITIONIST => '#FD7E14',
            self::TATTOO_STUDIO => '#DC3545',
            self::GENERIC => '#6C757D',
        };
    }
}
