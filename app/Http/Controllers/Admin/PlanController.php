<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class PlanController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));

        $plans = Plan::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->orderByRaw('sort_order is null, sort_order asc')
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString()
            ->through(fn ($plan) => [
                'id' => $plan->id,
                'name' => $plan->name,
                'slug' => $plan->slug,
                'price' => $plan->price,
                'billing_period' => $plan->billing_period,
                'is_active' => (bool) $plan->is_active,
                'sort_order' => $plan->sort_order,
            ]);

        return Inertia::render('Admin/Plans/Index', [
            'plans' => $plans,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Plans/Create');
    }

    public function store(Request $request, ActivityService $activity)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['required', 'string', 'max:150', 'unique:plans,slug'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'billing_period' => ['nullable', 'string', 'max:50'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'stripe_product_id' => ['nullable', 'string', 'max:150'],
            'stripe_price_id' => ['nullable', 'string', 'max:150'],
            'limits.max_items' => ['nullable', 'integer', 'min:0'],
            'limits.max_businesses' => ['nullable', 'integer', 'min:0'],
            'limits.max_requests_per_day' => ['nullable', 'integer', 'min:0'],
            'limits.can_use_ai' => ['boolean'],
            'limits.can_export' => ['boolean'],
            'limits.can_upload_files' => ['boolean'],
        ]);

        $plan = Plan::create([
            'name' => trim($data['name']),
            'slug' => trim($data['slug']),
            'description' => $data['description'] ?? null,
            'price' => $data['price'] ?? null,
            'billing_period' => $data['billing_period'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? false),
            'sort_order' => $data['sort_order'] ?? null,
            'limits' => $data['limits'] ?? null,
            'stripe_product_id' => $data['stripe_product_id'] ?? null,
            'stripe_price_id' => $data['stripe_price_id'] ?? null,
        ]);

        $activity->log('plan_created', [
            'actor' => $request->user(),
            'subject' => $plan,
            'description' => 'Plan creado',
            'request' => $request,
        ]);

        return redirect()->route('admin.plans.index');
    }

    public function edit(Plan $plan)
    {
        return Inertia::render('Admin/Plans/Edit', [
            'plan' => [
                'id' => $plan->id,
                'name' => $plan->name,
                'slug' => $plan->slug,
                'description' => $plan->description,
                'price' => $plan->price,
                'billing_period' => $plan->billing_period,
                'is_active' => (bool) $plan->is_active,
                'sort_order' => $plan->sort_order,
                'limits' => $plan->limits ?? [],
                'stripe_product_id' => $plan->stripe_product_id,
                'stripe_price_id' => $plan->stripe_price_id,
            ],
            'modules' => [],
        ]);
    }

    public function update(Request $request, Plan $plan, ActivityService $activity)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['required', 'string', 'max:150', Rule::unique('plans', 'slug')->ignore($plan->id)],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'billing_period' => ['nullable', 'string', 'max:50'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'stripe_product_id' => ['nullable', 'string', 'max:150'],
            'stripe_price_id' => ['nullable', 'string', 'max:150'],
            'limits.max_items' => ['nullable', 'integer', 'min:0'],
            'limits.max_businesses' => ['nullable', 'integer', 'min:0'],
            'limits.max_requests_per_day' => ['nullable', 'integer', 'min:0'],
            'limits.can_use_ai' => ['boolean'],
            'limits.can_export' => ['boolean'],
            'limits.can_upload_files' => ['boolean'],
        ]);

        $plan->update([
            'name' => trim($data['name']),
            'slug' => trim($data['slug']),
            'description' => $data['description'] ?? null,
            'price' => $data['price'] ?? null,
            'billing_period' => $data['billing_period'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? false),
            'sort_order' => $data['sort_order'] ?? null,
            'limits' => $data['limits'] ?? null,
            'stripe_product_id' => $data['stripe_product_id'] ?? null,
            'stripe_price_id' => $data['stripe_price_id'] ?? null,
        ]);

        $activity->log('plan_updated', [
            'actor' => $request->user(),
            'subject' => $plan,
            'description' => 'Plan actualizado',
            'request' => $request,
        ]);

        return redirect()->route('admin.plans.index');
    }

    public function destroy(Plan $plan)
    {
        if ($plan->subscriptions()->exists()) {
            return back()->withErrors([
                'delete' => 'No se puede eliminar un plan con suscripciones asociadas.',
            ]);
        }

        $plan->delete();

        return redirect()->route('admin.plans.index');
    }
}
