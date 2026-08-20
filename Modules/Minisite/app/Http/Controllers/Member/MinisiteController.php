<?php

namespace Modules\Minisite\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Listings\Models\Listing;
use Modules\Minisite\Models\BusinessMinisiteSetting;
use Illuminate\Support\Facades\Storage;

class MinisiteController extends Controller
{
    public function index(Request $request, Business $business)
    {
        $this->authorize('viewAny', [BusinessMinisiteSetting::class, $business]);

        $setting = BusinessMinisiteSetting::where('listing_id', $business->id)->first();

        return Inertia::render('Member/Minisite/Index', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
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
            'heroLayouts' => BusinessMinisiteSetting::getHeroLayouts(),
        ]);
    }

    public function store(Request $request, Business $business)
    {
        $this->authorize('create', [BusinessMinisiteSetting::class, $business]);

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
            $path = $request->file('hero_background_image')->store('minisite/' . $business->id, ['disk' => 'public']);
            $data['hero_background_image'] = Storage::disk('public')->url($path);
        }

        $data['listing_id'] = $business->id;

        $setting = BusinessMinisiteSetting::updateOrCreate(
            ['listing_id' => $business->id],
            $data
        );

        return redirect()->back()->with('success', 'Configuración guardada.');
    }

    public function update(Request $request, Business $business)
    {
        $user = $request->user();
        if (!$user->hasAnyRole(['superadmin', 'admin']) && $user->id !== $business->user_id) {
            abort(403);
        }

        $setting = BusinessMinisiteSetting::where('listing_id', $business->id)->first();

        if (!$setting) {
            $setting = new BusinessMinisiteSetting(['listing_id' => $business->id]);
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
            $path = $request->file('hero_background_image')->store('minisite/' . $business->id, ['disk' => 'public']);
            $data['hero_background_image'] = Storage::disk('public')->url($path);
        } else {
            unset($data['hero_background_image']);
        }

        $setting->fill($data);
        $setting->save();

        return redirect()->back()->with('success', 'Configuración guardada.');
    }
}
