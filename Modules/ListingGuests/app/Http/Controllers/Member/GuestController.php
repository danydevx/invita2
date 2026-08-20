<?php

namespace Modules\ListingGuests\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\ListingGuests\Models\ListingGuest;
use Illuminate\Support\Str;

class GuestController extends Controller
{
    public function index(Request $request, $listing)
    {
        $listing = \Modules\Listings\Models\Listing::where('id', $listing)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $guests = ListingGuest::where('listing_id', $listing->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return inertia('Member/Guests/Index', [
            'listing' => $listing,
            'guests' => $guests,
        ]);
    }

    public function store(Request $request, $listing)
    {
        $listing = \Modules\Listings\Models\Listing::where('id', $listing)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'plus_ones' => ['nullable', 'integer', 'min:0', 'max:20'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $guest = ListingGuest::create([
            ...$validated,
            'listing_id' => $listing->id,
            'confirmation_token' => ListingGuest::generateToken(),
        ]);

        return redirect()->back()->with('success', 'Invitado agregado correctamente.');
    }

    public function update(Request $request, $listing, $id)
    {
        $listing = \Modules\Listings\Models\Listing::where('id', $listing)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $guest = ListingGuest::where('listing_id', $listing->id)
            ->where('id', $id)
            ->firstOrFail();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'plus_ones' => ['nullable', 'integer', 'min:0', 'max:20'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'rsvp_status' => ['nullable', 'in:pending,confirmed,declined,maybe'],
        ]);

        $guest->update($validated);

        return redirect()->back()->with('success', 'Invitado actualizado correctamente.');
    }

    public function destroy(Request $request, $listing, $id)
    {
        $listing = \Modules\Listings\Models\Listing::where('id', $listing)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $guest = ListingGuest::where('listing_id', $listing->id)
            ->where('id', $id)
            ->firstOrFail();

        $guest->delete();

        return redirect()->back()->with('success', 'Invitado eliminado correctamente.');
    }

    public function import(Request $request, $listing)
    {
        $listing = \Modules\Listings\Models\Listing::where('id', $listing)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $validated = $request->validate([
            'guests' => ['required', 'array', 'min:1'],
            'guests.*.name' => ['required', 'string', 'max:255'],
            'guests.*.email' => ['nullable', 'email', 'max:255'],
            'guests.*.phone' => ['nullable', 'string', 'max:50'],
        ]);

        $count = 0;
        foreach ($validated['guests'] as $guestData) {
            ListingGuest::create([
                'listing_id' => $listing->id,
                'name' => $guestData['name'],
                'email' => $guestData['email'] ?? null,
                'phone' => $guestData['phone'] ?? null,
                'confirmation_token' => ListingGuest::generateToken(),
            ]);
            $count++;
        }

        return redirect()->back()->with('success', "{$count} invitados importados correctamente.");
    }
}