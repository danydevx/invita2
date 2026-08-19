<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PricingController extends Controller
{
    public function index()
    {
        $plans = Plan::query()
            ->where('is_active', true)
            ->orderByRaw('sort_order is null, sort_order asc')
            ->orderBy('price')
            ->get()
            ->map(fn ($plan) => [
                'id' => $plan->id,
                'name' => $plan->name,
                'description' => $plan->description,
                'price' => $plan->price,
                'billing_period' => $plan->billing_period,
                'features' => $plan->features ?? [],
                'limits' => $plan->limits ?? [],
                'is_checkout_available' => ! empty($plan->stripe_price_id),
            ]);

        $recommendedPlanId = $plans->first()['id'] ?? null;

        return Inertia::render('Public/Pricing/Index', [
            'plans' => $plans,
            'recommendedPlanId' => $recommendedPlanId,
        ]);
    }

    public function select(Request $request, Plan $plan)
    {
        if (! $plan->is_active) {
            return back()->with('error', 'Este plan no esta disponible.');
        }

        if (empty($plan->stripe_price_id)) {
            return back()->with('error', 'Este plan aun no tiene checkout disponible.');
        }

        $request->session()->put('selected_plan_id', $plan->id);

        $user = $request->user();
        if (! $user) {
            return redirect('/register')->with('success', 'Plan seleccionado. Crea tu cuenta para continuar.');
        }

        if ($user->hasAnyRole(['admin', 'superadmin'])) {
            return redirect('/pricing')->with('error', 'Este flujo esta disponible solo para miembros.');
        }

        if ($user->hasRole('member')) {
            return redirect('/member/plan-selection');
        }

        return redirect('/pricing');
    }
}
