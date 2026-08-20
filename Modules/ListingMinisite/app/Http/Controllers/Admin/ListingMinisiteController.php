<?php

namespace Modules\ListingMinisite\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\ListingMinisite\Models\ListingMinisiteSetting;

class ListingMinisiteController extends Controller
{
    public function index(Request $request, $listing)
    {
        $setting = ListingMinisiteSetting::where('listing_id', $listing->id)->firstOrCreate([
            'listing_id' => $listing->id,
        ]);

        return inertia('Admin/Minisite/Index', [
            'business' => $listing,
            'setting' => $setting,
        ]);
    }

    public function update(Request $request, $listing)
    {
        $setting = ListingMinisiteSetting::where('listing_id', $listing->id)->firstOrCreate([
            'listing_id' => $listing->id,
        ]);

        $validated = $request->validate([
            'theme' => 'nullable|string|max:50',
            'primary_color' => 'nullable|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
            'font_family' => 'nullable|string|max:100',
            'custom_css' => 'nullable|string',
        ]);

        $setting->update($validated);

        return redirect()->back()->with('success', 'Minisite settings updated.');
    }
}