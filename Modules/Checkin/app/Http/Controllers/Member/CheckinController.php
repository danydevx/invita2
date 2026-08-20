<?php

namespace Modules\Checkin\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Checkin\Models\BusinessCheckin;
use Modules\Guests\Models\BusinessGuest;

class CheckinController extends Controller
{
    public function index(Request $request, $listing)
    {
        $listing = \Modules\Listings\Models\Listing::where('id', $listing)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $checkins = BusinessCheckin::where('listing_id', $listing->id)
            ->with('guest')
            ->orderBy('checkin_time', 'desc')
            ->paginate(50);

        $totalGuests = BusinessGuest::where('listing_id', $listing->id)->count();
        $checkedIn = BusinessCheckin::where('listing_id', $listing->id)->count();
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
            'guest_id' => ['required', 'exists:business_guests,id'],
            'plus_ones_checked_in' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $guest = BusinessGuest::where('id', $validated['guest_id'])
            ->where('listing_id', $listing->id)
            ->firstOrFail();

        $checkin = BusinessCheckin::updateOrCreate(
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

        $checkin = BusinessCheckin::where('listing_id', $listing->id)
            ->where('id', $id)
            ->firstOrFail();

        $checkin->delete();

        return redirect()->back()->with('success', 'Check-in eliminado.');
    }
}
