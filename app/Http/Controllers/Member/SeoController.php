<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Businesses\Models\Business;
use Modules\Seo\Models\BusinessSeoSetting;
use Illuminate\Support\Facades\Storage;

class SeoController extends Controller
{
    public function index(Request $request, Business $business)
    {
        $this->authorize('viewAny', [BusinessSeoSetting::class, $business]);

        $seo = $business->seoSetting;

        return Inertia::render('Member/Seo/Index', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'seo' => $seo ? [
                'id' => $seo->id,
                'seo_title' => $seo->seo_title,
                'seo_description' => $seo->seo_description,
                'focus_keyword' => $seo->focus_keyword,
                'allow_indexing' => $seo->allow_indexing,
                'follow_links' => $seo->follow_links,
                'include_in_sitemap' => $seo->include_in_sitemap,
                'canonical_url' => $seo->canonical_url,
                'og_title' => $seo->og_title,
                'og_description' => $seo->og_description,
                'og_image' => $seo->og_image,
                'og_image_alt' => $seo->og_image_alt,
                'schema_enabled' => $seo->schema_enabled,
                'schema_type' => $seo->schema_type,
                'settings' => $seo->settings,
            ] : null,
        ]);
    }

    public function update(Request $request, Business $business)
    {
        $this->authorize('updateForBusiness', [BusinessSeoSetting::class, $business]);

        $validated = $request->validate([
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:500'],
            'focus_keyword' => ['nullable', 'string', 'max:255'],
            'allow_indexing' => ['boolean'],
            'follow_links' => ['boolean'],
            'include_in_sitemap' => ['boolean'],
            'canonical_url' => ['nullable', 'string', 'max:255'],
            'og_title' => ['nullable', 'string', 'max:255'],
            'og_description' => ['nullable', 'string', 'max:500'],
            'og_image' => ['nullable', 'file', 'mimes:jpeg,png,webp', 'max:2048'],
            'og_image_alt' => ['nullable', 'string', 'max:255'],
            'schema_enabled' => ['boolean'],
            'schema_type' => ['nullable', 'string', 'max:100'],
            'settings' => ['nullable', 'array'],
        ]);

        $seo = $business->seoSetting;

        if ($request->hasFile('og_image')) {
            if ($seo?->og_image) {
                $oldPath = str_replace(url('/') . '/storage/', '', $seo->og_image);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('og_image')->store('seo/' . $business->id, ['disk' => 'public']);
            $validated['og_image'] = Storage::disk('public')->url($path);
        } else {
            unset($validated['og_image']);
        }

        $business->seoSetting()->updateOrCreate(
            ['business_id' => $business->id],
            $validated
        );

        return redirect()->back()->with('success', 'Configuracion SEO guardada correctamente.');
    }
}
