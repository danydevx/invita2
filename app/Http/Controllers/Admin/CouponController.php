<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $status = $request->input('status', '');

        $coupons = Coupon::query()
            ->when($search !== '', function ($query) use ($search) {
                $needle = mb_strtolower($search);
                $query->where(function ($q) use ($needle) {
                    $q->whereRaw('LOWER(code) like ?', ['%'.$needle.'%'])
                        ->orWhereRaw('LOWER(name) like ?', ['%'.$needle.'%']);
                });
            })
            ->when($status !== '', function ($query) use ($status) {
                $query->where('is_active', $status === 'active');
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString()
            ->through(fn ($coupon) => [
                'id' => $coupon->id,
                'code' => $coupon->code,
                'name' => $coupon->name,
                'type' => $coupon->type,
                'value' => $coupon->value,
                'is_active' => (bool) $coupon->is_active,
                'starts_at' => $coupon->starts_at?->toDateString(),
                'ends_at' => $coupon->ends_at?->toDateString(),
                'usage_limit' => $coupon->usage_limit,
                'used_count' => $coupon->used_count,
            ]);

        return Inertia::render('Admin/Coupons/Index', [
            'coupons' => $coupons,
            'filters' => [
                'search' => $search,
                'status' => $status,
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Coupons/Create', [
            'plans' => $this->planOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $coupon = Coupon::create([
            'code' => strtoupper(trim($data['code'])),
            'name' => trim($data['name']),
            'description' => $data['description'] ?? null,
            'type' => $data['type'],
            'value' => $data['value'],
            'is_active' => (bool) ($data['is_active'] ?? false),
            'starts_at' => $data['starts_at'] ?? null,
            'ends_at' => $data['ends_at'] ?? null,
            'usage_limit' => $data['usage_limit'] ?? null,
            'applies_to_all_plans' => (bool) ($data['applies_to_all_plans'] ?? true),
            'stripe_coupon_id' => $data['stripe_coupon_id'] ?? null,
            'stripe_promotion_code_id' => $data['stripe_promotion_code_id'] ?? null,
        ]);

        $plans = $data['plans'] ?? [];
        if (! $coupon->applies_to_all_plans && ! empty($plans)) {
            $coupon->plans()->sync($plans);
        }

        return redirect()->route('admin.coupons.index');
    }

    public function edit(Coupon $coupon)
    {
        return Inertia::render('Admin/Coupons/Edit', [
            'coupon' => [
                'id' => $coupon->id,
                'code' => $coupon->code,
                'name' => $coupon->name,
                'description' => $coupon->description,
                'type' => $coupon->type,
                'value' => $coupon->value,
                'is_active' => (bool) $coupon->is_active,
                'starts_at' => $coupon->starts_at?->toDateString(),
                'ends_at' => $coupon->ends_at?->toDateString(),
                'usage_limit' => $coupon->usage_limit,
                'used_count' => $coupon->used_count,
                'applies_to_all_plans' => (bool) $coupon->applies_to_all_plans,
                'stripe_coupon_id' => $coupon->stripe_coupon_id,
                'stripe_promotion_code_id' => $coupon->stripe_promotion_code_id,
            ],
            'plans' => $this->planOptions(),
            'selectedPlans' => $coupon->plans()->pluck('plans.id')->all(),
        ]);
    }

    public function update(Request $request, Coupon $coupon)
    {
        $data = $this->validated($request, $coupon->id);

        $coupon->update([
            'code' => strtoupper(trim($data['code'])),
            'name' => trim($data['name']),
            'description' => $data['description'] ?? null,
            'type' => $data['type'],
            'value' => $data['value'],
            'is_active' => (bool) ($data['is_active'] ?? false),
            'starts_at' => $data['starts_at'] ?? null,
            'ends_at' => $data['ends_at'] ?? null,
            'usage_limit' => $data['usage_limit'] ?? null,
            'applies_to_all_plans' => (bool) ($data['applies_to_all_plans'] ?? true),
            'stripe_coupon_id' => $data['stripe_coupon_id'] ?? null,
            'stripe_promotion_code_id' => $data['stripe_promotion_code_id'] ?? null,
        ]);

        $plans = $data['plans'] ?? [];
        if ($coupon->applies_to_all_plans) {
            $coupon->plans()->detach();
        } else {
            $coupon->plans()->sync($plans);
        }

        return redirect()->route('admin.coupons.edit', $coupon)->with('success', 'Cupon actualizado correctamente.');
    }

    public function destroy(Coupon $coupon)
    {
        if ($coupon->used_count > 0) {
            return back()->withErrors([
                'delete' => 'No se puede eliminar un cupon con usos registrados. Desactivalo si es necesario.',
            ]);
        }

        $coupon->delete();

        return redirect()->route('admin.coupons.index');
    }

    private function validated(Request $request, ?int $couponId = null): array
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('coupons', 'code')->ignore($couponId)],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:500'],
            'type' => ['required', 'string', Rule::in(['fixed', 'percent'])],
            'value' => ['required', 'numeric', 'min:0'],
            'is_active' => ['boolean'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'applies_to_all_plans' => ['boolean'],
            'plans' => ['array'],
            'plans.*' => ['integer', Rule::exists('plans', 'id')],
            'stripe_coupon_id' => ['nullable', 'string', 'max:150'],
            'stripe_promotion_code_id' => ['nullable', 'string', 'max:150'],
        ]);

        if ($data['type'] === 'percent' && (float) $data['value'] > 100) {
            $request->validate([
                'value' => ['numeric', 'max:100'],
            ]);
        }

        if (! ($data['applies_to_all_plans'] ?? true) && empty($data['plans'])) {
            $request->validate([
                'plans' => ['required'],
            ]);
        }

        return $data;
    }

    private function planOptions()
    {
        return Plan::query()
            ->orderByRaw('sort_order is null, sort_order asc')
            ->orderBy('id')
            ->get(['id', 'name'])
            ->map(fn ($plan) => [
                'id' => $plan->id,
                'label' => $plan->name,
            ]);
    }
}
