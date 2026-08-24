<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Modules\Listings\Models\Listing;
use Modules\ListingPromotions\Models\ListingPromotion;

class PromotionController extends Controller
{
    public function index(Request $request, Listing $business)
    {
        $this->authorize('viewAny', [ListingPromotion::class, $business]);

        $perPage = min((int) $request->get('per_page', 10), 100);
        $search = $request->get('search', '');
        $sort = $request->get('sort', 'sort_order');
        $direction = $request->get('direction', 'asc');

        $allowedSorts = ['name', 'regular_price', 'promotion_price', 'is_active', 'sort_order', 'created_at'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'sort_order';
        }
        $direction = strtolower($direction) === 'desc' ? 'desc' : 'asc';

        $query = $business->promotions()
            ->with('location')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('coupon_code', 'like', "%{$search}%");
                });
            })
            ->orderBy($sort, $direction)
            ->orderByDesc('created_at');

        $promotions = $query->paginate($perPage);

        $dataTable = [
            'data' => collect($promotions->items())->map(function ($promo) {
                return [
                    'id' => $promo->id,
                    'name' => $promo->name,
                    'description' => $promo->description,
                    'image' => $promo->image,
                    'regular_price' => $promo->regular_price,
                    'promotion_price' => $promo->promotion_price,
                    'coupon_code' => $promo->coupon_code,
                    'qr_code_path' => $promo->qr_code_path,
                    'starts_at' => $promo->starts_at,
                    'expires_at' => $promo->expires_at,
                    'is_active' => $promo->is_active,
                    'sort_order' => $promo->sort_order,
                    'location' => $promo->location ? [
                        'id' => $promo->location->id,
                        'name' => $promo->location->name,
                    ] : null,
                ];
            })->toArray(),
            'current_page' => $promotions->currentPage(),
            'last_page' => $promotions->lastPage(),
            'per_page' => $promotions->perPage(),
            'total' => $promotions->total(),
            'from' => $promotions->firstItem(),
            'to' => $promotions->lastItem(),
        ];

        return Inertia::render('Member/Promotions/Index', [
            'listing' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'promotions' => $promotions,
            'dataTable' => $dataTable,
        ]);
    }

    public function create(Request $request, Listing $business)
    {
        $this->authorize('create', [ListingPromotion::class, $business]);

        $locations = $business->locations()->where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return Inertia::render('Member/Promotions/Create', [
            'listing' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'locations' => $locations,
        ]);
    }

    public function store(Request $request, Listing $business, ActivityService $activity)
    {
        $this->authorize('create', [ListingPromotion::class, $business]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
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

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('promotions', 'public');
            $data['image'] = $path;
        }

        $promotion = $business->promotions()->create($data);

        $activity->log('promotion_created', [
            'actor' => $request->user(),
            'subject' => $promotion,
            'description' => 'Promocion creada',
            'request' => $request,
        ]);

        return redirect()->route('member.listings.promotions.index', $business->id)
            ->with('success', 'Promocion creada correctamente.');
    }

    public function edit(Request $request, Listing $business, ListingPromotion $promotion)
    {
        $this->authorize('update', [ListingPromotion::class, $promotion]);

        $locations = $business->locations()->where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return Inertia::render('Member/Promotions/Edit', [
            'listing' => [
                'id' => $business->id,
                'name' => $business->name,
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
                'qr_code_path' => $promotion->qr_code_path,
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
        $this->authorize('update', [ListingPromotion::class, $promotion]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'remove_image' => ['nullable', 'boolean'],
            'regular_price' => ['nullable', 'numeric', 'min:0', 'max:99999999'],
            'promotion_price' => ['nullable', 'numeric', 'min:0', 'max:99999999'],
            'coupon_code' => ['nullable', 'string', 'max:50'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'business_location_id' => ['nullable', 'exists:listing_locations,id'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        if ($request->boolean('remove_image')) {
            if ($promotion->image) {
                Storage::disk('public')->delete($promotion->image);
            }
            $data['image'] = null;
        }

        if ($request->hasFile('image')) {
            if ($promotion->image) {
                Storage::disk('public')->delete($promotion->image);
            }
            $path = $request->file('image')->store('promotions', 'public');
            $data['image'] = $path;
        } else {
            unset($data['image']);
        }

        $promotion->update($data);

        $activity->log('promotion_updated', [
            'actor' => $request->user(),
            'subject' => $promotion,
            'description' => 'Promocion actualizada',
            'request' => $request,
        ]);

        return redirect()->route('member.listings.promotions.index', $business->id)
            ->with('success', 'Promocion actualizada correctamente.');
    }

    public function destroy(Request $request, Listing $business, ListingPromotion $promotion, ActivityService $activity)
    {
        $this->authorize('delete', [ListingPromotion::class, $promotion]);

        $this->deletePromotionImage($promotion);

        $activity->log('promotion_deleted', [
            'actor' => $request->user(),
            'subject' => $promotion,
            'description' => 'Promocion eliminada',
        ]);

        $promotion->delete();

        return redirect()->route('member.listings.promotions.index', $business->id)
            ->with('success', 'Promocion eliminada correctamente.');
    }

    private function deletePromotionImage(ListingPromotion $promotion): void
    {
        if ($promotion->image) {
            $path = $promotion->image;
            if (str_starts_with($path, '/storage/')) {
                $path = substr($path, 9);
            }
            Storage::disk('public')->delete($path);
        }

        if ($promotion->qr_code_path) {
            $path = $promotion->qr_code_path;
            if (str_starts_with($path, '/storage/')) {
                $path = substr($path, 9);
            }
            Storage::disk('public')->delete($path);
        }

        foreach ($promotion->images as $image) {
            Storage::disk('public')->delete($image->path);
        }
        $promotion->images()->delete();
    }

    public function reorder(Request $request, Listing $business)
    {
        $user = $request->user();

        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            // allowed
        } else {
            abort_unless($business->user_id === $user->id, 403);
        }

        $data = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', \Illuminate\Validation\Rule::exists('listing_promotions', 'id')->where('listing_id', $business->id)],
            'page' => ['nullable', 'integer', 'min:1'],
            'perPage' => ['nullable', 'integer', 'min:1'],
        ]);

        $page = $data['page'] ?? 1;
        $perPage = $data['perPage'] ?? count($data['ids']);
        $start = (($page - 1) * $perPage) + 1;

        \DB::transaction(function () use ($data, $business, $start) {
            foreach ($data['ids'] as $index => $id) {
                \Modules\ListingPromotions\Models\ListingPromotion::where('id', $id)
                    ->where('listing_id', $business->id)
                    ->update(['sort_order' => $start + $index]);
            }
        });

        return back(303);
    }

    public function bulkDelete(Request $request, Listing $business)
    {
        $this->authorize('deleteAny', [ListingPromotion::class, $business]);

        $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', \Illuminate\Validation\Rule::exists('listing_promotions', 'id')->where('listing_id', $business->id)],
        ]);

        $promotions = \Modules\ListingPromotions\Models\ListingPromotion::where('listing_id', $business->id)
            ->whereIn('id', $request->ids)
            ->get();

        foreach ($promotions as $promotion) {
            foreach ($promotion->images as $image) {
                Storage::disk('public')->delete($image->path);
            }
            $promotion->images()->delete();
            $promotion->delete();
        }

        return redirect()->back()->with('success', count($promotions) . ' promocion(es) eliminada(s).');
    }

    public function regenerateQrCode(Request $request, Listing $business, ListingPromotion $promotion)
    {
        $this->authorize('update', [ListingPromotion::class, $promotion]);

        if (!$promotion->coupon_code) {
            return redirect()->route('member.listings.promotions.edit', [$business->id, $promotion->id])
                ->with('error', 'La promoción no tiene código de cupón.');
        }

        $promotion->regenerateQrCode();

        return redirect()->route('member.listings.promotions.edit', [$business->id, $promotion->id])
            ->with('success', 'Código QR regenerado correctamente.');
    }

    public function clone(Request $request, Listing $business, ListingPromotion $promotion, ActivityService $activity)
    {
        $this->authorize('create', [ListingPromotion::class, $business]);

        $maxOrder = $business->promotions()->max('sort_order') ?? 0;

        $cloned = $business->promotions()->create([
            'name' => $promotion->name . ' (copia)',
            'slug' => \Illuminate\Support\Str::slug($promotion->name) . '-copia-' . time(),
            'description' => $promotion->description,
            'image' => $promotion->getAttributes()['image'] ?? null,
            'regular_price' => $promotion->regular_price,
            'promotion_price' => $promotion->promotion_price,
            'coupon_code' => $promotion->coupon_code ? $promotion->coupon_code . '-COPY' : null,
            'starts_at' => $promotion->starts_at,
            'expires_at' => $promotion->expires_at,
            'business_location_id' => $promotion->business_location_id,
            'is_active' => false,
            'sort_order' => $maxOrder + 1,
        ]);

        $activity->log('promotion_cloned', [
            'actor' => $request->user(),
            'subject' => $cloned,
            'description' => 'Promocion clonada',
            'request' => $request,
        ]);

        return redirect()->route('member.listings.promotions.edit', [$business->id, $cloned->id]);
    }
}
