<?php

namespace App\Services;

use App\Models\Industry;
use App\Models\PlanBusinessModule;
use Illuminate\Support\Facades\Cache;

class ModuleVisibilityService
{
    public function getVisibleModuleKeys(Industry $industry, array $planModuleKeys): array
    {
        $industryModuleKeys = $industry->moduleDefinitions->pluck('key')->toArray();

        return array_values(array_intersect($planModuleKeys, $industryModuleKeys));
    }

    public function getIndustryModuleKeys(Industry $industry): array
    {
        return $industry->moduleDefinitions->pluck('key')->toArray();
    }

    public function getPlanModuleKeysForUser($user): array
    {
        if (!$user) {
            return [];
        }

        $subscription = $user->subscriptions()
            ->where('status', 'active')
            ->where(function ($query) {
                $query->where('ends_at', '>', now())
                    ->orWhereNull('ends_at');
            })
            ->latest()
            ->first();

        if (!$subscription) {
            return [];
        }

        return PlanBusinessModule::where('plan_id', $subscription->plan_id)
            ->where('is_enabled', true)
            ->whereHas('moduleDefinition', fn ($q) => $q->where('is_active', true))
            ->get()
            ->pluck('module_key')
            ->toArray();
    }

    public function getDefaultModuleKeys(): array
    {
        $plan = \App\Models\Plan::where('slug', 'free')->first();

        if (!$plan) {
            return [];
        }

        return PlanBusinessModule::where('plan_id', $plan->id)
            ->where('is_enabled', true)
            ->whereHas('moduleDefinition', fn ($q) => $q->where('is_active', true))
            ->get()
            ->pluck('module_key')
            ->toArray();
    }
}
