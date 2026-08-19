<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Businesses\Models\Business;
use Modules\BusinessModules\Models\BusinessModule;

class BusinessModulesController extends Controller
{
    public function show(Request $request, Business $business)
    {
        $user = $request->user();
        abort_unless($user->id === $business->user_id || $user->hasAnyRole(['superadmin', 'admin']), 403);

        $bizId = $business->id;

        $industry = $business->industry;

        $industryModuleDefs = $industry
            ? $industry->moduleDefinitions()->where('is_active', true)->get()
            : collect();

        $businessModules = $business->modules()->pluck('is_enabled', 'module_key')->toArray();

        $moduleSummary = $industryModuleDefs->map(function ($def) use ($bizId, $businessModules) {
            $moduleKey = $def->key;
            $isEnabled = $businessModules[$moduleKey] ?? true;

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

        return Inertia::render('Member/BusinessModules', [
            'business' => [
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
            'client_fidelity' => "/member/businesses/{$businessId}/fidelity-cards",
            default => "/member/businesses/{$businessId}/" . $moduleKey,
        };
    }
}
