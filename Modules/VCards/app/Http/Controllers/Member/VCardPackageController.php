<?php

namespace Modules\VCards\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Modules\Listings\Models\Listing;
use Modules\VCards\Models\VCard;
use Modules\VCards\Models\VCardPackage;

class VCardPackageController extends Controller
{
    public function index(Listing $listing, VCard $vcard)
    {
        abort_unless($listing->user_id === Auth::id(), 403);
        abort_unless($vcard->listing_id === $listing->id, 403);

        $packages = VCardPackage::where('vcard_id', $vcard->id)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return Inertia::render('Member/VCards/Packages/Index', [
            'listing' => [
                'id' => $listing->id,
                'name' => $listing->name,
            ],
            'vcard' => [
                'id' => $vcard->id,
                'name' => $vcard->name,
            ],
            'packages' => $packages,
        ]);
    }

    public function store(Request $request, Listing $listing, VCard $vcard)
    {
        abort_unless($listing->user_id === Auth::id(), 403);
        abort_unless($vcard->listing_id === $listing->id, 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:10'],
            'duration_days' => ['nullable', 'integer', 'min:1'],
            'active' => ['boolean'],
        ]);

        $maxOrder = VCardPackage::where('vcard_id', $vcard->id)->max('sort_order') ?? 0;

        VCardPackage::create([
            'vcard_id' => $vcard->id,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'] ?? null,
            'currency' => $validated['currency'] ?? 'USD',
            'duration_days' => $validated['duration_days'] ?? null,
            'active' => $validated['active'] ?? true,
            'sort_order' => $maxOrder + 1,
        ]);

        return redirect()->back()->with('success', 'Paquete creado correctamente.');
    }

    public function update(Request $request, Listing $listing, VCard $vcard, VCardPackage $package)
    {
        abort_unless($listing->user_id === Auth::id(), 403);
        abort_unless($vcard->listing_id === $listing->id, 403);
        abort_unless($package->vcard_id === $vcard->id, 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:10'],
            'duration_days' => ['nullable', 'integer', 'min:1'],
            'active' => ['boolean'],
        ]);

        $package->update($validated);

        return redirect()->back()->with('success', 'Paquete actualizado correctamente.');
    }

    public function destroy(Listing $listing, VCard $vcard, VCardPackage $package)
    {
        abort_unless($listing->user_id === Auth::id(), 403);
        abort_unless($vcard->listing_id === $listing->id, 403);
        abort_unless($package->vcard_id === $vcard->id, 403);

        $package->delete();

        return redirect()->back()->with('success', 'Paquete eliminado correctamente.');
    }

    public function reorder(Request $request, Listing $listing, VCard $vcard)
    {
        abort_unless($listing->user_id === Auth::id(), 403);
        abort_unless($vcard->listing_id === $listing->id, 403);

        $request->validate([
            'order' => ['required', 'array'],
        ]);

        foreach ($request->order as $index => $id) {
            VCardPackage::where('id', $id)->where('vcard_id', $vcard->id)->update(['sort_order' => $index]);
        }

        return response()->json(['success' => true]);
    }
}
