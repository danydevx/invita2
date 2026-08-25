<?php

namespace Modules\ClientFidelity\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Modules\Listings\Models\Listing;
use Modules\ClientFidelity\Models\FidelityReward;

class FidelityRewardController extends Controller
{
    public function index(Request $request, Listing $listing)
    {
        abort_unless($listing->user_id === Auth::id(), 403);

        $perPage = min((int) $request->get('per_page', 10), 100);
        $search = $request->get('search', '');

        $query = FidelityReward::where('listing_id', $listing->id)
            ->when($search, function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            })
            ->sorted();

        $rewards = $query->paginate($perPage);

        return Inertia::render('Member/ClientFidelity/Rewards/Index', [
            'listing' => $listing,
            'rewards' => $rewards,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function create(Listing $listing)
    {
        abort_unless($listing->user_id === Auth::id(), 403);

        return Inertia::render('Member/ClientFidelity/Rewards/Create', [
            'listing' => $listing,
        ]);
    }

    public function store(Request $request, Listing $listing)
    {
        abort_unless($listing->user_id === Auth::id(), 403);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'max_visits' => ['required', 'integer', 'min:2', 'max:100'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        if (isset($data['image'])) {
            $path = $data['image']->store('fidelity-rewards', 'public');
            $data['image'] = $path;
        }

        $data['listing_id'] = $listing->id;

        if (!isset($data['sort_order'])) {
            $data['sort_order'] = FidelityReward::where('listing_id', $listing->id)->max('sort_order') + 1;
        }

        $reward = FidelityReward::create($data);

        return redirect()->route('member.listings.fidelity-rewards.index', $listing->id)
            ->with('success', 'Recompensa creada correctamente.');
    }

    public function edit(Listing $listing, FidelityReward $reward)
    {
        abort_unless($listing->user_id === Auth::id(), 403);
        abort_unless($reward->listing_id === $listing->id, 403);

        return Inertia::render('Member/ClientFidelity/Rewards/Edit', [
            'listing' => $listing,
            'reward' => $reward,
        ]);
    }

    public function update(Request $request, Listing $listing, FidelityReward $reward)
    {
        abort_unless($listing->user_id === Auth::id(), 403);
        abort_unless($reward->listing_id === $listing->id, 403);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'max_visits' => ['required', 'integer', 'min:2', 'max:100'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer'],
            'remove_image' => ['boolean'],
        ]);

        if (isset($data['remove_image']) && $data['remove_image']) {
            if ($reward->image) {
                Storage::disk('public')->delete($reward->image);
            }
            $data['image'] = null;
            unset($data['remove_image']);
        }

        if (isset($data['image'])) {
            if ($reward->image) {
                Storage::disk('public')->delete($reward->image);
            }
            $path = $data['image']->store('fidelity-rewards', 'public');
            $data['image'] = $path;
        }

        $reward->update($data);

        return redirect()->back()->with('success', 'Recompensa actualizada correctamente.');
    }

    public function destroy(Listing $listing, FidelityReward $reward)
    {
        abort_unless($listing->user_id === Auth::id(), 403);
        abort_unless($reward->listing_id === $listing->id, 403);

        if ($reward->cards()->exists()) {
            return redirect()->back()->with('error', 'No se puede eliminar una recompensa que tiene tarjetas asociadas.');
        }

        if ($reward->image) {
            Storage::disk('public')->delete($reward->image);
        }

        $reward->delete();

        return redirect()->route('member.listings.fidelity-rewards.index', $listing->id)
            ->with('success', 'Recompensa eliminada correctamente.');
    }
}
