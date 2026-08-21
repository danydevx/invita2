<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Listings\Models\Listing;
use Modules\ListingModules\Models\ListingModule;

class ListingModulesController extends Controller
{
    public function show(Request $request, Listing $business)
    {
        $user = $request->user();

        if ($user->hasRole('member') && ! $user->hasAnyRole(['superadmin', 'admin'])) {
            abort(403, 'No tienes permiso para acceder a esta pagina.');
        }

        abort_unless($user->id === $business->user_id || $user->hasAnyRole(['superadmin', 'admin']), 403);

        $bizId = $business->id;

        $industry = $business->industry;

        $industryModuleDefs = $industry
            ? $industry->moduleDefinitions()->where('is_active', true)->get()
            : collect();

        $listingModules = $business->modules()->pluck('is_enabled', 'module_key')->toArray();

        $moduleSummary = $industryModuleDefs->map(function ($def) use ($bizId, $listingModules) {
            $moduleKey = $def->key;
            $isEnabled = $listingModules[$moduleKey] ?? true;

            return [
                'key' => $moduleKey,
                'name' => $def->name,
                'description' => $def->description ?? '',
                'icon' => $def->icon ?? 'bi bi-grid',
                'count' => 0,
                'url' => $this->getModuleUrl($bizId, $moduleKey),
                'is_enabled' => $isEnabled,
                'is_premium' => $def->is_premium,
            ];
        })->values()->toArray();

        return Inertia::render('Member/ListingModules/Index', [
            'listing' => [
                'id' => $business->id,
                'name' => $business->name,
                'industry_name' => $industry?->name,
            ],
            'moduleSummary' => $moduleSummary,
        ]);
    }

    private function getModuleUrl(int $businessId, string $moduleKey): string
    {
        return match ($moduleKey) {
            'leads' => "/member/leads?business={$businessId}",
            'appointments' => "/member/appointments?business={$businessId}",
            'client_fidelity' => "/member/listings/{$businessId}/fidelity-cards",
            default => "/member/listings/{$businessId}/" . $moduleKey,
        };
    }
}
