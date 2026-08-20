<?php

namespace Modules\ListingMinisite\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Listings\Models\Listing;
use Modules\ListingMinisite\Models\ListingMinisiteSetting;
use Illuminate\Support\Facades\Storage;

class ListingMinisiteController extends Controller
{
    public function index(Request $request, Listing $listing)
    {
        $this->authorize('viewAny', [ListingMinisiteSetting::class, $listing]);

        $setting = ListingMinisiteSetting::where('listing_id', $listing->id)->first();

        return Inertia::render('Member/Minisite/Index', [
            'business' => [
                'id' => $listing->id,
                'name' => $listing->name,
            ],
            'setting' => $setting ? [
                'id' => $setting->id,
                'theme_key' => $setting->theme_key,
                'hero_layout' => $setting->hero_layout,
                'hero_title' => $setting->hero_title,
                'hero_subtitle' => $setting->hero_subtitle,
                'hero_background_image' => $setting->hero_background_image,
                'hero_show_social' => $setting->hero_show_social,
                'footer_text' => $setting->footer_text,
                'footer_show_social' => $setting->footer_show_social,
                'is_active' => $setting->is_active,
            ] : null,
            'heroLayouts' => ListingMinisiteSetting::getHeroLayouts(),
        ]);
    }

    public function store(Request $request, Listing $listing)
    {
        $this->authorize('create', [ListingMinisiteSetting::class, $listing]);

        $data = $request->validate([
            'hero_layout' => ['required', 'string', 'in:left,center,right'],
            'hero_title' => ['nullable', 'string', 'max:150'],
            'hero_subtitle' => ['nullable', 'string', 'max:255'],
            'hero_background_image' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:5120'],
            'hero_show_social' => ['boolean'],
            'footer_text' => ['nullable', 'string', 'max:500'],
            'footer_show_social' => ['boolean'],
            'is_active' => ['boolean'],
        ]);

        if ($request->hasFile('hero_background_image')) {
            $path = $request->file('hero_background_image')->store('minisite/' . $listing->id, ['disk' => 'public']);
            $data['hero_background_image'] = Storage::disk('public')->url($path);
        }

        $data['listing_id'] = $listing->id;

        $setting = ListingMinisiteSetting::updateOrCreate(
            ['listing_id' => $listing->id],
            $data
        );

        return redirect()->back()->with('success', 'Configuración guardada.');
    }

    public function update(Request $request, Listing $listing)
    {
        $user = $request->user();
        if (!$user->hasAnyRole(['superadmin', 'admin']) && $user->id !== $listing->user_id) {
            abort(403);
        }

        $setting = ListingMinisiteSetting::where('listing_id', $listing->id)->first();

        if (!$setting) {
            $setting = new ListingMinisiteSetting(['listing_id' => $listing->id]);
        }

        $data = $request->validate([
            'hero_layout' => ['nullable', 'string', 'in:left,center,right'],
            'hero_title' => ['nullable', 'string', 'max:150'],
            'hero_subtitle' => ['nullable', 'string', 'max:255'],
            'hero_background_image' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:5120'],
            'hero_show_social' => ['boolean'],
            'footer_text' => ['nullable', 'string', 'max:500'],
            'footer_show_social' => ['boolean'],
            'is_active' => ['boolean'],
        ]);

        if ($request->hasFile('hero_background_image')) {
            if ($setting->hero_background_image) {
                $oldPath = str_replace(url('/') . '/storage/', '', $setting->hero_background_image);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('hero_background_image')->store('minisite/' . $listing->id, ['disk' => 'public']);
            $data['hero_background_image'] = Storage::disk('public')->url($path);
        } else {
            unset($data['hero_background_image']);
        }

        $setting->fill($data);
        $setting->save();

        return redirect()->back()->with('success', 'Configuración guardada.');
    }
}