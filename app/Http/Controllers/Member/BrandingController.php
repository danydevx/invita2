<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\ListingBranding\Models\ListingBrandingSetting;
use Modules\Listings\Models\Listing;

class BrandingController extends Controller
{
    public function index(Request $request, Listing $business)
    {
        $this->authorize('updateForBusiness', [ListingBrandingSetting::class, $business]);

        $branding = ListingBrandingSetting::where('listing_id', $business->id)->first();

        return Inertia::render('Member/Branding/Index', [
            'listing' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'branding' => $branding,
        ]);
    }

    public function update(Request $request, Listing $business)
    {
        $this->authorize('updateForBusiness', [ListingBrandingSetting::class, $business]);

        $validated = $request->validate([
            'colors' => 'nullable|string',
            'fonts' => 'nullable|string',
            'custom_font_url' => 'nullable|url|max:500',
            'dark_mode' => 'nullable|boolean',
            'buttons_style' => 'nullable|in:rounded,square,round',
            'buttons_uppercase' => 'nullable|boolean',
        ]);

        $colors = $validated['colors'] ? json_decode($validated['colors'], true) : null;
        $fonts = $validated['fonts'] ? json_decode($validated['fonts'], true) : null;

        $branding = ListingBrandingSetting::updateOrCreate(
            ['listing_id' => $business->id],
            [
                'colors' => $colors,
                'fonts' => $fonts,
                'custom_font_url' => $validated['custom_font_url'] ?? null,
                'dark_mode' => $validated['dark_mode'] ?? false,
                'buttons_style' => $validated['buttons_style'] ?? 'round',
                'buttons_uppercase' => $validated['buttons_uppercase'] ?? false,
            ]
        );

        return redirect()->back()->with('success', 'Configuración de marca guardada correctamente');
    }
}