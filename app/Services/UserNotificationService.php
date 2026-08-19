<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserNotification;

class UserNotificationService
{
    public function create(
        User $user,
        string $type,
        string $title,
        ?string $message = null,
        ?string $url = null,
        ?array $metadata = null
    ): ?UserNotification {
        // Evita crear notificaciones si el modulo esta desactivado.
        if (! app(ModuleService::class)->isEnabled('notifications')) {
            return null;
        }

        $preferences = app(NotificationPreferenceService::class);
        if (! $preferences->allows($user, $type, 'in_app')) {
            return null;
        }

        return UserNotification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'url' => $url,
            'metadata' => $metadata,
        ]);
    }
}
