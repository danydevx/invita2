<?php

namespace App\Services;

use App\Models\FeatureFlag;
use App\Models\Plan;
use App\Models\User;

class FeatureService
{
    public function enabled(?User $user, string $key, bool $default = false): bool
    {
        $value = $this->value($user, $key, $default ? 'true' : 'false');

        return $this->toBool($value);
    }

    public function value(?User $user, string $key, $default = null)
    {
        $flag = FeatureFlag::query()->where('key', $key)->first();
        if (! $flag || ! $flag->is_active) {
            // Si no existe el flag, busca un fallback en settings para evitar inconsistencias.
            $fallback = $this->fallbackSettingValue($key);

            return $fallback !== null ? $fallback : $default;
        }

        $userValue = $this->userOverrideValue($user, $flag->id);
        if ($userValue !== null) {
            return $this->castValue($flag->type, $userValue);
        }

        $planValue = $this->planValue($user, $flag->id);
        if ($planValue !== null) {
            return $this->castValue($flag->type, $planValue);
        }

        if ($flag->default_value !== null) {
            return $this->castValue($flag->type, $flag->default_value);
        }

        return $default;
    }

    private function fallbackSettingValue(string $key): mixed
    {
        // Solo aplica a flags globales guardados en settings con prefijo features.
        if (! str_starts_with($key, 'features.')) {
            return null;
        }

        $value = app(SettingService::class)->get($key, null);
        if ($value === null || $value === '') {
            return null;
        }

        return $this->toBool($value);
    }

    public function enabledForCurrentUser(string $key, bool $default = false): bool
    {
        return $this->enabled(auth()->user(), $key, $default);
    }

    public function allForUser(?User $user): array
    {
        $flags = FeatureFlag::query()->where('is_active', true)->get();
        $resolved = [];

        foreach ($flags as $flag) {
            $resolved[$flag->key] = $this->value($user, $flag->key, null);
        }

        return $resolved;
    }

    private function userOverrideValue(?User $user, int $flagId): ?string
    {
        if (! $user) {
            return null;
        }

        $override = $user->featureFlags()->where('feature_flag_id', $flagId)->first();

        return $override?->value;
    }

    private function planValue(?User $user, int $flagId): ?string
    {
        if (! $user) {
            return null;
        }

        $plan = $this->currentPlan($user);
        if (! $plan) {
            return null;
        }

        $override = $plan->featureFlags()->where('feature_flag_id', $flagId)->first();

        return $override?->value;
    }

    private function currentPlan(User $user): ?Plan
    {
        return $user->currentSubscription?->plan;
    }

    private function castValue(string $type, $value)
    {
        return match ($type) {
            'integer' => is_numeric($value) ? (int) $value : null,
            'boolean' => $this->toBool($value),
            default => $value,
        };
    }

    private function toBool($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        $normalized = strtolower((string) $value);

        return in_array($normalized, ['1', 'true', 'yes', 'on'], true);
    }
}
