<?php

namespace Modules\ListingCheckin\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\ListingCheckin\Models\ListingCheckin;
use Modules\ListingGuests\Models\ListingGuest;

class CheckinController extends Controller
{
    public function index(Request $request, $listing)
    {
        $listing = \Modules\Listings\Models\Listing::where('id', $listing)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $checkins = ListingCheckin::where('listing_id', $listing->id)
            ->with('guest')
            ->orderBy('checkin_time', 'desc')
            ->paginate(50);

        $totalGuests = ListingGuest::where('listing_id', $listing->id)->count();
        $checkedIn = ListingCheckin::where('listing_id', $listing->id)->count();
        $pending = $totalGuests - $checkedIn;

        return inertia('Member/Checkin/Index', [
            'listing' => $listing,
            'checkins' => $checkins,
            'stats' => [
                'total' => $totalGuests,
                'checked_in' => $checkedIn,
                'pending' => $pending,
            ],
        ]);
    }

    public function store(Request $request, $listing)
    {
        $listing = \Modules\Listings\Models\Listing::where('id', $listing)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $validated = $request->validate([
            'guest_id' => ['required', 'exists:listing_guests,id'],
            'plus_ones_checked_in' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $guest = ListingGuest::where('id', $validated['guest_id'])
            ->where('listing_id', $listing->id)
            ->firstOrFail();

        $checkin = ListingCheckin::updateOrCreate(
            [
                'listing_id' => $listing->id,
                'guest_id' => $guest->id,
            ],
            [
                'checkin_time' => now(),
                'plus_ones_checked_in' => $validated['plus_ones_checked_in'] ?? 0,
                'notes' => $validated['notes'] ?? null,
            ]
        );

        return redirect()->back()->with('success', 'Check-in realizado correctamente.');
    }

    public function destroy(Request $request, $listing, $id)
    {
        $listing = \Modules\Listings\Models\Listing::where('id', $listing)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $checkin = ListingCheckin::where('listing_id', $listing->id)
            ->where('id', $id)
            ->firstOrFail();

        $checkin->delete();

        return redirect()->back()->with('success', 'Check-in eliminado.');
    }
}