<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Listings\Models\Listing;
use Modules\ListingPromotions\Models\ListingPromotion;

class ListingPromotionController extends Controller
{
    public function index(Request $request, Listing $business)
    {
        $promotions = $business->promotions()
            ->with('location')
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->paginate(20);

        return Inertia::render('Admin/BusinessContent/PromotionsIndex', [
            'listing' => [
                'id' => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
            ],
            'promotions' => $promotions,
        ]);
    }

    public function create(Request $request, Listing $business)
    {
        $locations = $business->locations()->where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return Inertia::render('Admin/BusinessContent/PromotionsCreate', [
            'listing' => [
                'id' => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
            ],
            'locations' => $locations,
        ]);
    }

    public function store(Request $request, Listing $business, ActivityService $activity)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'string'],
            'regular_price' => ['nullable', 'numeric', 'min:0', 'max:99999999'],
            'promotion_price' => ['nullable', 'numeric', 'min:0', 'max:99999999'],
            'coupon_code' => ['nullable', 'string', 'max:50'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'business_location_id' => ['nullable', 'exists:listing_locations,id'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['listing_id'] = $business->id;
        $data['slug'] = \Illuminate\Support\Str::slug($data['name']);

        $promotion = $business->promotions()->create($data);

        $activity->log('admin_promotion_created', [
            'actor' => $request->user(),
            'subject' => $promotion,
            'description' => 'Admin: Promocion creada',
            'request' => $request,
        ]);

        return redirect()->route('admin.business.promotions.index', $business->id)
            ->with('success', 'Promocion creada correctamente.');
    }

    public function edit(Request $request, Listing $business, ListingPromotion $promotion)
    {
        $locations = $business->locations()->where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return Inertia::render('Admin/BusinessContent/PromotionsEdit', [
            'listing' => [
                'id' => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
            ],
            'promotion' => [
                'id' => $promotion->id,
                'name' => $promotion->name,
                'slug' => $promotion->slug,
                'description' => $promotion->description,
                'image' => $promotion->image,
                'regular_price' => $promotion->regular_price,
                'promotion_price' => $promotion->promotion_price,
                'coupon_code' => $promotion->coupon_code,
                'starts_at' => $promotion->starts_at?->format('Y-m-d\TH:i'),
                'expires_at' => $promotion->expires_at?->format('Y-m-d\TH:i'),
                'business_location_id' => $promotion->business_location_id,
                'is_active' => $promotion->is_active,
                'sort_order' => $promotion->sort_order,
            ],
            'locations' => $locations,
        ]);
    }

    public function update(Request $request, Listing $business, ListingPromotion $promotion, ActivityService $activity)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'string'],
            'regular_price' => ['nullable', 'numeric', 'min:0', 'max:99999999'],
            'promotion_price' => ['nullable', 'numeric', 'min:0', 'max:99999999'],
            'coupon_code' => ['nullable', 'string', 'max:50'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'business_location_id' => ['nullable', 'exists:listing_locations,id'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $promotion->update($data);

        $activity->log('admin_promotion_updated', [
            'actor' => $request->user(),
            'subject' => $promotion,
            'description' => 'Admin: Promocion actualizada',
            'request' => $request,
        ]);

        return redirect()->route('admin.business.promotions.index', $business->id)
            ->with('success', 'Promocion actualizada correctamente.');
    }

    public function destroy(Request $request, Listing $business, ListingPromotion $promotion, ActivityService $activity)
    {
        $activity->log('admin_promotion_deleted', [
            'actor' => $request->user(),
            'subject' => $promotion,
            'description' => 'Admin: Promocion eliminada',
        ]);

        $promotion->delete();

        return redirect()->route('admin.business.promotions.index', $business->id)
            ->with('success', 'Promocion eliminada correctamente.');
    }
}
