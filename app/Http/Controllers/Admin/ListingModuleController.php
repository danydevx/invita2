<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Modules\Listings\Models\Listing;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ListingModuleController extends Controller
{
    public function edit(Request $request, Listing $business)
    {
        $business->load('modules.moduleDefinition');
        $business->load('user.subscriptions.plan');

        return Inertia::render('Admin/BusinessModules/Edit', [
            'listing' => [
                'id' => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
                'user' => $business->user ? [
                    'id' => $business->user->id,
                    'name' => $business->user->name,
                    'email' => $business->user->email,
                    'plan_name' => $this->getUserPlanName($business->user),
                ] : null,
                'modules' => $business->modules->map(fn ($m) => [
                    'id' => $m->id,
                    'module_key' => $m->module_key,
                    'module_name' => $m->moduleDefinition?->name ?? $m->module_key,
                    'module_icon' => $m->moduleDefinition?->icon,
                    'is_enabled' => $m->is_enabled,
                    'is_active_globally' => $m->moduleDefinition?->is_active ?? false,
                    'has_settings' => $m->moduleDefinition?->has_settings ?? false,
                    'settings_url' => $m->moduleDefinition?->settings_url,
                    'settings' => $m->settings,
                ]),
            ],
        ]);
    }

    public function update(Request $request, Listing $business, ActivityService $activity)
    {
        $data = $request->validate([
            'modules' => ['required', 'array'],
            'modules.*.id' => ['required', 'exists:listing_modules,id'],
            'modules.*.is_enabled' => ['required', 'boolean'],
        ]);

        foreach ($data['modules'] as $moduleData) {
            $business->modules()
                ->where('id', $moduleData['id'])
                ->update(['is_enabled' => $moduleData['is_enabled']]);
        }

        $activity->log('listing_modules_updated', [
            'actor' => $request->user(),
            'subject' => $business,
            'description' => 'Modulos de negocio actualizados',
            'request' => $request,
        ]);

        return redirect()->back()->with('success', 'Modulos actualizados correctamente.');
    }

    private function getUserPlanName($user): string
    {
        if (!$user) {
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

        if (!$subscription || !$subscription->plan) {
            return 'Sin plan';
        }

        return $subscription->plan->name;
    }
}
