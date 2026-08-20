<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\ListingAppointments\Models\ListingAppointment;
use Modules\Listings\Models\Listing;
use Modules\ListingLeads\Models\ListingLead;
use Modules\ListingPromotions\Models\ListingPromotion;
use Modules\ListingReviews\Models\ListingReview;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $listings = $user->listings()
            ->select('id', 'name', 'description')
            ->orderBy('name')
            ->get()
            ->map(fn ($biz) => [
                'id' => $biz->id,
                'name' => $biz->name,
                'description' => $biz->description,
            ]);

        $businessIds = $listings->pluck('id');
        $businessCount = $listings->count();

        if ($businessCount === 0) {
            $counts = [
                'leads' => 0,
                'appointments' => 0,
                'promotions' => 0,
                'reviews' => 0,
            ];
        } else {
            $counts = [
                'leads' => ListingLead::whereIn('listing_id', $businessIds)->count(),
                'appointments' => ListingAppointment::whereIn('listing_id', $businessIds)->count(),
                'promotions' => ListingPromotion::whereIn('listing_id', $businessIds)->count(),
                'reviews' => ListingReview::whereIn('listing_id', $businessIds)->count(),
            ];
        }

        return Inertia::render('Member/Dashboard', [
            'businessCount' => $businessCount,
            'stats' => $counts,
            'listings' => $listings,
        ]);
    }
}
