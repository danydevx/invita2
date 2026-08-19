<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserNotificationPreference;

class NotificationPreferenceService
{
    private array $categories = [
        'security' => [
            'label' => 'Seguridad',
            'description' => 'Alertas de acceso y cambios sensibles.',
        ],
        'billing' => [
            'label' => 'Facturacion',
            'description' => 'Pagos, comprobantes y cambios en tu suscripcion.',
        ],
        'support' => [
            'label' => 'Soporte',
            'description' => 'Tickets y respuestas del equipo de soporte.',
        ],
        'product' => [
            'label' => 'Producto',
            'description' => 'Novedades, mejoras y cambios generales del servicio.',
        ],
        'marketing' => [
            'label' => 'Marketing',
            'description' => 'Campanas, promociones y contenido comercial.',
        ],
    ];

    private array $defaults = [
        'security' => ['in_app' => true, 'email' => true],
        'billing' => ['in_app' => true, 'email' => true],
        'support' => ['in_app' => true, 'email' => true],
        'product' => ['in_app' => true, 'email' => false],
        'marketing' => ['in_app' => false, 'email' => false],
    ];

    private array $requiredChannels = [
        'security' => ['in_app', 'email'],
    ];

    public function categories(): array
    {
        return $this->categories;
    }

    public function allowedCategories(): array
    {
        return array_keys($this->categories);
    }

    public function defaultsFor(string $category): array
    {
        return $this->defaults[$category] ?? ['in_app' => true, 'email' => true];
    }

    public function isRequiredChannel(string $category, string $channel): bool
    {
        return in_array($channel, $this->requiredChannels[$category] ?? [], true);
    }

    public function forUser(User $user): array
    {
        $records = UserNotificationPreference::query()
            ->where('user_id', $user->id)
            ->get()
            ->keyBy('category');

        $legacy = app(UserPreferenceService::class)->forUser($user);
        $legacyEmail = (bool) ($legacy['email_notifications'] ?? true);
        $legacyInApp = (bool) ($legacy['system_notifications'] ?? true);

        $data = [];

        foreach ($this->categories as $key => $meta) {
            $defaults = $this->defaultsFor($key);
            $record = $records->get($key);

            $inApp = $record?->in_app_enabled ?? $defaults['in_app'];
            $email = $record?->email_enabled ?? $defaults['email'];

            if (! $legacyInApp && ! $this->isRequiredChannel($key, 'in_app')) {
                $inApp = false;
            }
            if (! $legacyEmail && ! $this->isRequiredChannel($key, 'email')) {
                $email = false;
            }

            if ($this->isRequiredChannel($key, 'in_app')) {
                $inApp = true;
            }
            if ($this->isRequiredChannel($key, 'email')) {
                $email = true;
            }

            $data[] = [
                'category' => $key,
                'label' => $meta['label'],
                'description' => $meta['description'],
                'in_app_enabled' => (bool) $inApp,
                'email_enabled' => (bool) $email,
                'required_in_app' => $this->isRequiredChannel($key, 'in_app'),
                'required_email' => $this->isRequiredChannel($key, 'email'),
            ];
        }

        return $data;
    }

    public function allows(User $user, string $category, string $channel): bool
    {
        if (! in_array($channel, ['in_app', 'email'], true)) {
            return false;
        }

        if (! in_array($category, $this->allowedCategories(), true)) {
            return true;
        }

        if ($this->isRequiredChannel($category, $channel)) {
            return true;
        }

        $record = UserNotificationPreference::query()
            ->where('user_id', $user->id)
            ->where('category', $category)
            ->first();

        $defaults = $this->defaultsFor($category);
        $value = $channel === 'email'
            ? ($record?->email_enabled ?? $defaults['email'])
            : ($record?->in_app_enabled ?? $defaults['in_app']);

        $legacy = app(UserPreferenceService::class)->forUser($user);
        if ($channel === 'email' && ! ($legacy['email_notifications'] ?? true)) {
            return false;
        }
        if ($channel === 'in_app' && ! ($legacy['system_notifications'] ?? true)) {
            return false;
        }

        return (bool) $value;
    }
}
