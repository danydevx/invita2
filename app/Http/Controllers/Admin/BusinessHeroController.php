<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Modules\Businesses\Models\Business;
use Modules\Hero\Models\BusinessHero;

class BusinessHeroController extends Controller
{
    public function index(Request $request, Business $business)
    {
        $hero = $business->hero;

        return Inertia::render('Admin/Hero/Index', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'hero' => $hero,
        ]);
    }

    public function update(Request $request, Business $business, ActivityService $activity)
    {
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:1000'],
            'text_aux' => ['nullable', 'string', 'max:500'],
            'background_type' => ['required', 'in:color,gradient,image'],
            'background_color' => ['nullable', 'string', 'max:50'],
            'background_gradient_start' => ['nullable', 'string', 'max:50'],
            'background_gradient_end' => ['nullable', 'string', 'max:50'],
            'background_image' => ['nullable', 'image', 'max:5120'],
            'alignment' => ['required', 'in:left,center,right'],
            'buttons' => ['nullable', 'array'],
            'buttons.*.text' => ['required', 'string', 'max:100'],
            'buttons.*.url' => ['required', 'string', 'max:500'],
            'buttons.*.style' => ['required', 'in:primary,secondary,outline'],
            'social_links' => ['nullable', 'array'],
            'social_links.*.platform' => ['required', 'string', 'max:50'],
            'social_links.*.url' => ['required', 'string', 'max:500'],
            'social_links.*.name' => ['nullable', 'string', 'max:100'],
            'show_contact_info' => ['boolean'],
            'show_social_links' => ['boolean'],
            'is_active' => ['boolean'],
        ]);

        $hero = BusinessHero::updateOrCreate(
            ['business_id' => $business->id],
            [
                'title' => $data['title'] ?? null,
                'subtitle' => $data['subtitle'] ?? null,
                'text_aux' => $data['text_aux'] ?? null,
                'background_type' => $data['background_type'],
                'background_color' => $data['background_color'] ?? null,
                'background_gradient_start' => $data['background_gradient_start'] ?? null,
                'background_gradient_end' => $data['background_gradient_end'] ?? null,
                'alignment' => $data['alignment'],
                'buttons' => $data['buttons'] ?? null,
                'social_links' => $data['social_links'] ?? null,
                'show_contact_info' => $data['show_contact_info'] ?? true,
                'show_social_links' => $data['show_social_links'] ?? false,
                'is_active' => $data['is_active'] ?? true,
            ]
        );

        if ($request->hasFile('background_image')) {
            $file = $request->file('background_image');
            $path = $file->store('hero/' . $business->id, ['disk' => 'public']);
            $hero->update(['background_image_path' => Storage::disk('public')->url($path)]);
        }

        $activity->log('hero_updated', [
            'actor' => $request->user(),
            'subject' => $hero,
            'description' => 'Hero actualizado por admin',
            'request' => $request,
        ]);

        return redirect()->back()->with('success', 'Hero actualizado correctamente.');
    }
}
