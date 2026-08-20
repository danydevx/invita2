<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\PlanBusinessModule;
use App\Services\PlanLimits;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Listings\Models\Listing;

class BusinessModuleController extends Controller
{
    public function index(Request $request, PlanLimits $planLimits)
    {
        $user = $request->user();

        $businesses = Listing::where('user_id', $user->id)
            ->with(['modules.moduleDefinition' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $businesses->getCollection()->transform(function ($business) {
            $business->modules = $business->modules
                ->filter(fn ($m) => $m->moduleDefinition?->is_active)
                ->map(fn ($m) => [
                    'module_key' => $m->module_key,
                    'module_name' => $m->moduleDefinition?->name ?? $m->module_key,
                    'is_enabled' => $m->is_enabled,
                ]);

            return $business;
        });

        $businessCount = $businesses->total();
        $maxBusinesses = $planLimits->max($user, 'max_businesses');

        return Inertia::render('Member/BusinessModules/Index', [
            'businesses' => $businesses,
            'canCreate' => $maxBusinesses === null || $businessCount < $maxBusinesses,
            'businessCount' => $businessCount,
            'maxBusinesses' => $maxBusinesses,
            'planName' => $planLimits->currentPlan($user)?->name ?? 'Sin plan',
        ]);
    }

    public function edit(Request $request, Listing $business)
    {
        $user = $request->user();

        if ($business->user_id !== $user->id) {
            abort(403, 'No tienes permiso para gestionar este negocio.');
        }

        $business->load(['modules.moduleDefinition' => fn ($q) => $q->where('is_active', true)]);

        $planModules = $this->getPlanModulesForUser($user);

        return Inertia::render('Member/BusinessModules/Edit', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
                'modules' => $business->modules
                    ->filter(fn ($m) => $m->moduleDefinition?->is_active)
                    ->map(fn ($m) => [
                        'id' => $m->id,
                        'module_key' => $m->module_key,
                        'module_name' => $m->moduleDefinition?->name ?? $m->module_key,
                        'module_description' => $m->moduleDefinition?->description,
                        'module_image' => $m->moduleDefinition?->image,
                        'is_enabled' => $m->is_enabled,
                    ])
                    ->values()
                    ->toArray(),
            ],
            'planModules' => array_keys($planModules),
            'planName' => $this->getUserPlanName($user),
        ]);
    }

    public function update(Request $request, Listing $business)
    {
        $user = $request->user();

        if ($business->user_id !== $user->id) {
            abort(403, 'No tienes permiso para gestionar este negocio.');
        }

        $planModules = $this->getPlanModulesForUser($user);

        $data = $request->validate([
            'modules' => ['required', 'array'],
            'modules.*.id' => ['required', 'exists:business_modules,id'],
            'modules.*.is_enabled' => ['required', 'boolean'],
        ]);

        foreach ($data['modules'] as $moduleData) {
            $module = $business->modules()->find($moduleData['id']);

            if (! $module) {
                continue;
            }

            if ($moduleData['is_enabled'] && ! ($planModules[$module->moduleDefinition?->key] ?? false)) {
                continue;
            }

            $business->modules()
                ->where('id', $moduleData['id'])
                ->update(['is_enabled' => $moduleData['is_enabled']]);
        }

        return redirect()->back()->with('success', 'Modulo actualizado correctamente.');
    }

    private function getPlanModulesForUser($user): array
    {
        if (! $user) {
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

        if (! $subscription) {
            return [];
        }

        return PlanBusinessModule::where('plan_id', $subscription->plan_id)
            ->where('is_enabled', true)
            ->whereHas('moduleDefinition', fn ($q) => $q->where('is_active', true))
            ->with('moduleDefinition')
            ->get()
            ->pluck('moduleDefinition.key')
            ->flip()
            ->map(fn () => true)
            ->toArray();
    }

    private function getUserPlanName($user): string
    {
        if (! $user) {
            return 'Sin plan';
        }

        $subscription = $user->subscriptions()
            ->where('status', 'active')
            ->where(function ($query) {
                $query->where('ends_at', '>', now())
                    ->orWhereNull('ends_at');
            })
            ->latest()
            ->first();

        if (! $subscription || ! $subscription->plan) {
            return 'Sin plan';
        }

        return $subscription->plan->name;
    }
}
