<?php

namespace App\Services;

use App\Models\Setting;

class SettingService
{
    public function get(string $key, $default = null)
    {
        $value = Setting::query()->where('key', $key)->value('value');

        return $value !== null ? $value : $default;
    }

    public function getMany(array $keys): array
    {
        return Setting::query()
            ->whereIn('key', $keys)
            ->pluck('value', 'key')
            ->toArray();
    }

    public function set(string $key, ?string $value): void
    {
        Setting::updateOrCreate([
            'key' => $key,
        ], [
            'value' => $value ?? '',
        ]);
    }
}
