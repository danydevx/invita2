<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeatureFlag;
use App\Models\Plan;
use App\Models\PlanFeatureFlag;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PlanFeatureFlagController extends Controller
{
    public function edit(Plan $plan)
    {
        $flags = FeatureFlag::query()
            ->orderBy('key')
            ->get()
            ->map(function ($flag) use ($plan) {
                $override = $plan->featureFlags()->where('feature_flag_id', $flag->id)->first();

                return [
                    'id' => $flag->id,
                    'key' => $flag->key,
                    'name' => $flag->name,
                    'type' => $flag->type,
                    'default_value' => $flag->default_value,
                    'value' => $override?->value,
                ];
            });

        return Inertia::render('Admin/Plans/Features', [
            'plan' => [
                'id' => $plan->id,
                'name' => $plan->name,
            ],
            'flags' => $flags,
        ]);
    }

    public function update(Request $request, Plan $plan, ActivityService $activity)
    {
        $data = $request->validate([
            'flags' => ['required', 'array'],
            'flags.*.id' => ['required', 'integer', 'exists:feature_flags,id'],
            'flags.*.value' => ['nullable', 'string'],
        ]);

        foreach ($data['flags'] as $item) {
            $value = $item['value'] ?? null;

            if ($value === '' || $value === null) {
                PlanFeatureFlag::query()
                    ->where('plan_id', $plan->id)
                    ->where('feature_flag_id', $item['id'])
                    ->delete();

                continue;
            }

            PlanFeatureFlag::updateOrCreate(
                [
                    'plan_id' => $plan->id,
                    'feature_flag_id' => $item['id'],
                ],
                [
                    'value' => $value,
                ]
            );
        }

        $activity->log('plan_feature_flags_updated', [
            'actor' => $request->user(),
            'subject' => $plan,
            'description' => 'Features del plan actualizadas',
            'request' => $request,
        ]);

        return back()->with('success', 'Features del plan actualizadas correctamente.');
    }
}
