<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Listings\Models\Listing;
use Modules\ListingReviews\Models\ListingReview;

class ReviewController extends Controller
{
    public function index(Request $request, Listing $business)
    {
        $this->authorize('viewAny', [ListingReview::class, $business]);

        $perPage = min((int) $request->get('per_page', 10), 100);
        $search = $request->get('search', '');
        $sort = $request->get('sort', 'sort_order');
        $direction = $request->get('direction', 'asc');

        $allowedSorts = ['client_name', 'rating', 'is_active', 'created_at', 'sort_order'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'sort_order';
        }
        $direction = strtolower($direction) === 'asc' ? 'asc' : 'desc';

        $query = $business->reviews()
            ->with('location')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('client_name', 'like', "%{$search}%")
                      ->orWhere('company', 'like', "%{$search}%")
                      ->orWhere('comment', 'like', "%{$search}%");
                });
            })
            ->orderBy($sort, $direction);

        $reviews = $query->paginate($perPage);

        $dataTable = [
            'data' => collect($reviews->items())->map(function ($review) {
                return [
                    'id' => $review->id,
                    'sort_order' => $review->sort_order,
                    'client_name' => $review->client_name,
                    'company' => $review->company,
                    'comment' => $review->comment,
                    'rating' => $review->rating,
                    'google_link' => $review->google_link,
                    'is_active' => $review->is_active,
                    'location' => $review->location ? [
                        'id' => $review->location->id,
                        'name' => $review->location->name,
                    ] : null,
                ];
            })->toArray(),
            'current_page' => $reviews->currentPage(),
            'last_page' => $reviews->lastPage(),
            'per_page' => $reviews->perPage(),
            'total' => $reviews->total(),
            'from' => $reviews->firstItem(),
            'to' => $reviews->lastItem(),
        ];

        return Inertia::render('Member/Reviews/Index', [
            'listing' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'reviews' => $reviews,
            'dataTable' => $dataTable,
        ]);
    }

    public function create(Request $request, Listing $business)
    {
        $this->authorize('create', [ListingReview::class, $business]);

        return Inertia::render('Member/Reviews/Create', [
            'listing' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
        ]);
    }

    public function store(Request $request, Listing $business, ActivityService $activity)
    {
        $this->authorize('create', [ListingReview::class, $business]);

        $data = $request->validate([
            'client_name' => ['required', 'string', 'max:150'],
            'company' => ['nullable', 'string', 'max:150'],
            'comment' => ['nullable', 'string'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'google_link' => ['nullable', 'url', 'max:500'],
            'business_location_id' => ['nullable', 'exists:listing_locations,id'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['listing_id'] = $business->id;

        $review = $business->reviews()->create($data);

        $activity->log('review_created', [
            'actor' => $request->user(),
            'subject' => $review,
            'description' => 'Resena creada',
            'request' => $request,
        ]);

        return redirect()->route('member.listings.reviews.index', $business->id)
            ->with('success', 'Resena creada correctamente.');
    }

    public function edit(Request $request, Listing $business, ListingReview $review)
    {
        $this->authorize('update', [ListingReview::class, $review]);

        return Inertia::render('Member/Reviews/Edit', [
            'listing' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'review' => [
                'id' => $review->id,
                'client_name' => $review->client_name,
                'company' => $review->company,
                'comment' => $review->comment,
                'rating' => $review->rating,
                'google_link' => $review->google_link,
                'business_location_id' => $review->business_location_id,
                'is_active' => $review->is_active,
                'sort_order' => $review->sort_order,
            ],
        ]);
    }

    public function update(Request $request, Listing $business, ListingReview $review, ActivityService $activity)
    {
        $this->authorize('update', [ListingReview::class, $review]);

        $data = $request->validate([
            'client_name' => ['required', 'string', 'max:150'],
            'company' => ['nullable', 'string', 'max:150'],
            'comment' => ['nullable', 'string'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'google_link' => ['nullable', 'url', 'max:500'],
            'business_location_id' => ['nullable', 'exists:listing_locations,id'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $review->update($data);

        $activity->log('review_updated', [
            'actor' => $request->user(),
            'subject' => $review,
            'description' => 'Resena actualizada',
            'request' => $request,
        ]);

        return redirect()->route('member.listings.reviews.index', $business->id)
            ->with('success', 'Resena actualizada correctamente.');
    }

    public function destroy(Request $request, Listing $business, ListingReview $review, ActivityService $activity)
    {
        $this->authorize('delete', [ListingReview::class, $review]);

        $activity->log('review_deleted', [
            'actor' => $request->user(),
            'subject' => $review,
            'description' => 'Resena eliminada',
        ]);

        return redirect()->route('member.listings.reviews.index', $business->id)
            ->with('success', 'Resena eliminada correctamente.');
    }

    public function reorder(Request $request, Listing $business)
    {
        $user = $request->user();

        if ($user->hasAnyRole(['superadmin', 'admin'])) {
        } else {
            abort_unless($business->user_id === $user->id, 403);
        }

        $data = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', \Illuminate\Validation\Rule::exists('listing_reviews', 'id')->where('listing_id', $business->id)],
            'page' => ['nullable', 'integer', 'min:1'],
            'perPage' => ['nullable', 'integer', 'min:1'],
        ]);

        $page = $data['page'] ?? 1;
        $perPage = $data['perPage'] ?? count($data['ids']);
        $start = (($page - 1) * $perPage) + 1;

        \DB::transaction(function () use ($data, $business, $start) {
            foreach ($data['ids'] as $index => $id) {
                \Modules\ListingReviews\Models\ListingReview::where('id', $id)
                    ->where('listing_id', $business->id)
                    ->update(['sort_order' => $start + $index]);
            }
        });

        return back(303);
    }

    public function bulkDelete(Request $request, Listing $business)
    {
        $user = $request->user();

        if ($user->hasAnyRole(['superadmin', 'admin'])) {
        } else {
            abort_unless($business->user_id === $user->id, 403);
        }

        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
        ]);

        $count = count($data['ids']);

        $business->reviews()
            ->whereIn('id', $data['ids'])
            ->delete();

        $message = $count === 1
            ? "1 resena eliminada correctamente."
            : "{$count} resenas eliminadas correctamente.";

        return redirect()->back()
            ->with('success', $message);
    }

    public function clone(Request $request, Listing $business, ListingReview $review, ActivityService $activity)
    {
        $this->authorize('create', [ListingReview::class, $business]);

        $maxOrder = $business->reviews()->max('sort_order') ?? 0;

        $cloned = $business->reviews()->create([
            'client_name' => $review->client_name . ' (copia)',
            'company' => $review->company,
            'comment' => $review->comment,
            'rating' => $review->rating,
            'google_link' => $review->google_link,
            'business_location_id' => $review->business_location_id,
            'is_active' => false,
            'sort_order' => $maxOrder + 1,
        ]);

        $activity->log('review_cloned', [
            'actor' => $request->user(),
            'subject' => $cloned,
            'description' => 'Resena clonada',
            'request' => $request,
        ]);

        return redirect()->route('member.listings.reviews.edit', [$business->id, $cloned->id]);
    }
}
