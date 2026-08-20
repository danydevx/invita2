<?php

namespace App\Services;

use App\Models\ModuleDefinition;

class ModuleVisibilityService
{
    public function getPlanModuleKeysForUser($user): array
    {
        return $this->getAllActiveModuleKeys();
    }

    public function getDefaultModuleKeys(): array
    {
        return $this->getAllActiveModuleKeys();
    }

    public function getAllActiveModuleKeys(): array
    {
        return ModuleDefinition::where('is_active', true)
            ->pluck('key')
            ->toArray();
    }
}
