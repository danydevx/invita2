<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Branding\Models\BusinessBrandingSetting;
use Modules\Businesses\Models\Business;

class BrandingController extends Controller
{
    public function index(Request $request, Business $business)
    {
        $this->authorize('updateForBusiness', [BusinessBrandingSetting::class, $business]);

        $branding = BusinessBrandingSetting::where('business_id', $business->id)->first();

        return Inertia::render('Member/Branding/Index', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'branding' => $branding,
        ]);
    }

    public function update(Request $request, Business $business)
    {
        $this->authorize('updateForBusiness', [BusinessBrandingSetting::class, $business]);

        $validated = $request->validate([
            'colors' => 'nullable|string',
            'fonts' => 'nullable|string',
            'custom_font_url' => 'nullable|url|max:500',
            'dark_mode' => 'nullable|boolean',
            'buttons_style' => 'nullable|in:rounded,square,round',
            'buttons_uppercase' => 'nullable|boolean',
            'section_variants' => 'nullable|string',
            'page_style' => 'nullable|string',
            'section_style' => 'nullable|string',
            'hero_style' => 'nullable|string',
            'animations' => 'nullable|string',
        ]);

        $colors = $validated['colors'] ? json_decode($validated['colors'], true) : null;
        $fonts = $validated['fonts'] ? json_decode($validated['fonts'], true) : null;
        $sectionVariants = $validated['section_variants'] ? json_decode($validated['section_variants'], true) : null;
        $animations = $validated['animations'] ? json_decode($validated['animations'], true) : null;

        $branding = BusinessBrandingSetting::updateOrCreate(
            ['business_id' => $business->id],
            [
                'colors' => $colors,
                'fonts' => $fonts,
                'custom_font_url' => $validated['custom_font_url'] ?? null,
                'dark_mode' => $validated['dark_mode'] ?? false,
                'buttons_style' => $validated['buttons_style'] ?? 'round',
                'buttons_uppercase' => $validated['buttons_uppercase'] ?? false,
                'section_variants' => $sectionVariants,
                'page_style' => $validated['page_style'] ?? null,
                'section_style' => $validated['section_style'] ?? null,
                'hero_style' => $validated['hero_style'] ?? null,
                'animations' => $animations,
                'generated_css' => null,
            ]
        );

        $branding->generateCss();
        $branding->save();

        return redirect()->back()->with('success', 'Configuración de marca guardada correctamente');
    }
}
