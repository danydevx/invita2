<?php

namespace App\Services;

use App\Models\User;

class UserPreferenceService
{
    public function defaults(): array
    {
        return [
            'locale' => 'es',
            'timezone' => 'America/Mexico_City',
            'email_notifications' => true,
            'system_notifications' => true,
            'dashboard_welcome_dismissed' => false,
        ];
    }

    public function forUser(User $user): array
    {
        $profile = $user->profile;
        $stored = $profile?->preferences ?? [];

        return array_merge($this->defaults(), $stored);
    }
}
