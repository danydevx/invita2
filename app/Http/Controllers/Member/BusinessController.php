<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use App\Services\PlanLimits;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Listings\Enums\ListingType;
use Modules\Listings\Models\Listing;

class BusinessController extends Controller
{
    private const MAX_LOGO_SIZE_KB = 2048;

    private const ALLOWED_MIME_TYPES = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

    private const RESERVED_SLUGS = [
        'admin',
        'member',
        'login',
        'register',
        'dashboard',
        'api',
        'storage',
        'logout',
        'password',
        'email',
        'sanctum',
    ];

    public function create(Request $request, PlanLimits $planLimits)
    {
        $user = $request->user();

        if ($planLimits->exceeded($user, 'max_businesses', $user->listings()->count())) {
            return redirect()->route('member.listings.index')
                ->with('error', 'Has alcanzado el limite de negocios de tu plan.');
        }

        return inertia('Member/Listings/Create', [
            'listingTypes' => array_map(fn ($type) => [
                'value' => $type->value,
                'label' => $type->label(),
                'icon' => $type->icon(),
                'color' => $type->color(),
            ], ListingType::cases()),
            'maxBusinesses' => $planLimits->max($user, 'max_businesses'),
            'businessCount' => $user->listings()->count(),
            'planName' => $planLimits->currentPlan($user)?->name ?? 'Sin plan',
        ]);
    }

    public function store(Request $request, PlanLimits $planLimits, ActivityService $activity)
    {
        $user = $request->user();
        $businessCount = $user->listings()->count();

        if ($planLimits->exceeded($user, 'max_businesses', $businessCount)) {
            return redirect()->route('member.listings.index')
                ->with('error', 'Has alcanzado el limite de negocios de tu plan.');
        }

        $validTypes = array_column(ListingType::cases(), 'value');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'listing_type' => ['required', 'string', Rule::in($validTypes)],
            'description' => ['nullable', 'string', 'max:1000'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:150'],
            'website' => ['nullable', 'url', 'max:255'],
            'timezone' => ['nullable', 'string', 'max:100'],
            'currency' => ['nullable', 'string', 'size:3'],
        ]);

        $listing = Listing::create([
            'user_id' => $user->id,
            'name' => $validated['name'],
            'slug' => $this->uniqueSlug($validated['name']),
            'listing_type' => $validated['listing_type'],
            'description' => $validated['description'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'website' => $validated['website'] ?? null,
            'timezone' => $validated['timezone'] ?? 'UTC',
            'currency' => strtoupper($validated['currency'] ?? 'USD'),
            'is_active' => true,
            'is_published' => false,
        ]);

        $activity->log('business_created', [
            'user' => $user,
            'actor' => $user,
            'subject' => $listing,
            'description' => 'Negocio creado por miembro',
            'request' => $request,
        ]);

        return redirect()->route('member.listings.edit', $listing)
            ->with('success', 'Negocio creado correctamente.');
    }

    public function edit(Request $request, Listing $business)
    {
        $user = Auth::user();
        abort_unless($business->user_id === $user->id, 403);

        return inertia('Member/Listings/Edit', [
            'listing' => $business,
        ]);
    }

    public function update(Request $request, Listing $business)
    {
        $user = Auth::user();
        abort_unless($business->user_id === $user->id, 403);

        $validTypes = array_column(ListingType::cases(), 'value');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'logo' => ['nullable', 'file', 'mimes:jpeg,png,gif,webp', 'max:'.self::MAX_LOGO_SIZE_KB],
            'remove_logo' => ['boolean'],
        ], [
            'logo.mimes' => 'El logo debe ser una imagen JPG, PNG, GIF o WebP.',
            'logo.max' => 'El logo no puede superar los 2MB.',
        ]);

        if ($request->boolean('remove_logo')) {
            if ($business->logo_path) {
                $path = str_replace(url('/').'/storage/', '', $business->logo_path);
                Storage::disk('public')->delete($path);
            }
            $validated['logo_path'] = null;
        } elseif ($request->hasFile('logo')) {
            if ($business->logo_path) {
                $oldPath = str_replace(url('/').'/storage/', '', $business->logo_path);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('logo')->store('businesses/'.$business->id, ['disk' => 'public']);
            $validated['logo_path'] = Storage::disk('public')->url($path);
        } else {
            unset($validated['logo_path']);
        }

        $business->update($validated);

        return redirect()->back()->with('success', 'Negocio actualizado correctamente.');
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'negocio';

        if (in_array($base, self::RESERVED_SLUGS, true)) {
            $base .= '-negocio';
        }

        $slug = $base;
        $suffix = 2;

        while (Listing::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}
