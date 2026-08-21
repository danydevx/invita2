<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\ModuleDefinition;
use App\Services\PlanLimits;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Listings\Models\Listing;

class ListingModuleController extends Controller
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

        return Inertia::render('Member/ListingModules/Index', [
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

        if ($user->hasRole('member') && ! $user->hasAnyRole(['superadmin', 'admin'])) {
            abort(403, 'No tienes permiso para acceder a esta pagina.');
        }

        if ($business->user_id !== $user->id) {
            abort(403, 'No tienes permiso para gestionar este negocio.');
        }

        $business->load(['modules.moduleDefinition' => fn ($q) => $q->where('is_active', true)]);

        return Inertia::render('Member/ListingModules/Index', [
            'listing' => [
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
            'planName' => $this->getUserPlanName($user),
        ]);
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
