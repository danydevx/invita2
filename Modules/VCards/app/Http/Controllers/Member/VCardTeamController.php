<?php

namespace Modules\VCards\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Modules\Listings\Models\Listing;
use Modules\VCards\Models\VCardTeam;

class VCardTeamController extends Controller
{
    public function index(Request $request, Listing $listing)
    {
        abort_unless($listing->user_id === Auth::id(), 403);

        $teams = VCardTeam::where('listing_id', $listing->id)
            ->withCount('vcards')
            ->orderBy('sort_order')
            ->get();

        return response()->json(['teams' => $teams]);
    }

    public function teamsIndex(Request $request, Listing $listing)
    {
        abort_unless($listing->user_id === Auth::id(), 403);

        $teams = VCardTeam::where('listing_id', $listing->id)
            ->withCount('vcards')
            ->orderBy('sort_order')
            ->get();

        return Inertia::render('Member/VCards/Teams/Index', [
            'listing' => $listing,
            'teams' => $teams,
        ]);
    }

    public function data(Request $request, Listing $listing)
    {
        abort_unless($listing->user_id === Auth::id(), 403);

        $teams = VCardTeam::where('listing_id', $listing->id)
            ->withCount('vcards')
            ->orderBy('sort_order')
            ->get();

        return response()->json(['teams' => $teams]);
    }

    public function store(Request $request, Listing $listing)
    {
        abort_unless($listing->user_id === Auth::id(), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'active' => ['boolean'],
        ]);

        $slug = Str::slug($validated['name']);
        $originalSlug = $slug;
        $counter = 1;

        while (VCardTeam::where('listing_id', $listing->id)->where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $maxOrder = VCardTeam::where('listing_id', $listing->id)->max('sort_order') ?? 0;

        $team = VCardTeam::create([
            'listing_id' => $listing->id,
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'active' => $validated['active'] ?? true,
            'sort_order' => $maxOrder + 1,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['team' => $team, 'message' => 'Equipo creado correctamente.']);
        }

        return redirect()->route('member.listings.vcards.teams.index', [$listing->id]);
    }

    public function update(Request $request, Listing $listing, VCardTeam $team)
    {
        abort_unless($listing->user_id === Auth::id(), 403);
        abort_unless($team->listing_id === $listing->id, 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'active' => ['boolean'],
        ]);

        if (isset($validated['name']) && $validated['name'] !== $team->name) {
            $slug = Str::slug($validated['name']);
            $originalSlug = $slug;
            $counter = 1;

            while (VCardTeam::where('listing_id', $listing->id)->where('slug', $slug)->where('id', '!=', $team->id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
            $validated['slug'] = $slug;
        }

        $team->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['team' => $team, 'message' => 'Equipo actualizado correctamente.']);
        }

        return redirect()->route('member.listings.vcards.teams.index', [$listing->id]);
    }

    public function destroy(Request $request, Listing $listing, VCardTeam $team)
    {
        abort_unless($listing->user_id === Auth::id(), 403);
        abort_unless($team->listing_id === $listing->id, 403);

        $team->vcards()->update(['vcard_team_id' => null]);
        $team->delete();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Equipo eliminado correctamente.']);
        }

        return redirect()->route('member.listings.vcards.teams.index', [$listing->id]);
    }

    public function reorder(Request $request, Listing $listing)
    {
        abort_unless($listing->user_id === Auth::id(), 403);

        $validated = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['exists:vcard_teams,id'],
        ]);

        foreach ($validated['order'] as $index => $id) {
            VCardTeam::where('id', $id)->update(['sort_order' => $index]);
        }

        return redirect()->back();
    }
}
