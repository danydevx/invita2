<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Listings\Models\Listing;
use Modules\ListingReviews\Models\ListingReview;

class ListingReviewController extends Controller
{
    public function index(Request $request, Listing $business)
    {
        $reviews = $business->reviews()
            ->with('location')
            ->orderByDesc('created_at')
            ->paginate(20);

        return Inertia::render('Admin/BusinessContent/ReviewsIndex', [
            'listing' => [
                'id' => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
            ],
            'reviews' => $reviews,
        ]);
    }

    public function create(Request $request, Listing $business)
    {
        return Inertia::render('Admin/BusinessContent/ReviewsCreate', [
            'listing' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
        ]);
    }

    public function store(Request $request, Listing $business, ActivityService $activity)
    {
        $data = $request->validate([
            'client_name' => ['required', 'string', 'max:150'],
            'company' => ['nullable', 'string', 'max:150'],
            'comment' => ['required', 'string'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'google_link' => ['nullable', 'url', 'max:500'],
            'business_location_id' => ['nullable', 'exists:listing_locations,id'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['listing_id'] = $business->id;

        $review = $business->reviews()->create($data);

        $activity->log('admin_review_created', [
            'actor' => $request->user(),
            'subject' => $review,
            'description' => 'Admin: Review created',
            'request' => $request,
        ]);

        return redirect()->route('admin.business.reviews.index', $business->id)
            ->with('success', 'Review created successfully.');
    }

    public function edit(Request $request, Listing $business, ListingReview $review)
    {
        return Inertia::render('Admin/BusinessContent/ReviewsEdit', [
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
        $data = $request->validate([
            'client_name' => ['required', 'string', 'max:150'],
            'company' => ['nullable', 'string', 'max:150'],
            'comment' => ['required', 'string'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'google_link' => ['nullable', 'url', 'max:500'],
            'business_location_id' => ['nullable', 'exists:listing_locations,id'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $review->update($data);

        $activity->log('admin_review_updated', [
            'actor' => $request->user(),
            'subject' => $review,
            'description' => 'Admin: Review updated',
            'request' => $request,
        ]);

        return redirect()->route('admin.business.reviews.index', $business->id)
            ->with('success', 'Review updated successfully.');
    }

    public function destroy(Request $request, Listing $business, ListingReview $review, ActivityService $activity)
    {
        $activity->log('admin_review_deleted', [
            'actor' => $request->user(),
            'subject' => $review,
            'description' => 'Admin: Review deleted',
        ]);

        $review->delete();

        return redirect()->route('admin.business.reviews.index', $business->id)
            ->with('success', 'Review deleted successfully.');
    }
}
