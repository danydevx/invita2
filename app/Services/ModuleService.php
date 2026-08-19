<?php

namespace App\Services;

use App\Models\SystemModule;

class ModuleService
{
    private array $cache = [];

    private array $critical = [
        'users',
        'roles',
        'permissions',
        'settings',
    ];

    public function isEnabled(string $key): bool
    {
        if (in_array($key, $this->critical, true)) {
            return true;
        }

        $module = $this->resolve($key);
        if (! $module) {
            return true;
        }

        return (bool) $module->is_active;
    }

    public function list(): array
    {
        return SystemModule::query()
            ->orderBy('name')
            ->get()
            ->map(fn ($module) => [
                'id' => $module->id,
                'key' => $module->key,
                'name' => $module->name,
                'description' => $module->description,
                'is_active' => $module->is_active,
                'is_critical' => in_array($module->key, $this->critical, true),
            ])
            ->all();
    }

    public function forFrontend(): array
    {
        $modules = SystemModule::query()->get(['key', 'is_active']);
        $map = [];

        foreach ($modules as $module) {
            $map[$module->key] = $this->isEnabled($module->key);
        }

        foreach ($this->critical as $key) {
            $map[$key] = true;
        }

        return $map;
    }

    public function canDisable(string $key): bool
    {
        return ! in_array($key, $this->critical, true);
    }

    private function resolve(string $key): ?SystemModule
    {
        if (array_key_exists($key, $this->cache)) {
            return $this->cache[$key];
        }

        $module = SystemModule::query()->where('key', $key)->first();
        $this->cache[$key] = $module;

        return $module;
    }
}
