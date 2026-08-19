<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Industry;
use App\Services\ActivityService;
use App\Services\PlanLimits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Modules\Businesses\Models\Business;

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

        if ($planLimits->exceeded($user, 'max_businesses', $user->businesses()->count())) {
            return redirect()->route('member.businesses.index')
                ->with('error', 'Has alcanzado el limite de negocios de tu plan.');
        }

        return inertia('Member/Businesses/Create', [
            'industries' => Industry::where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'icon']),
            'maxBusinesses' => $planLimits->max($user, 'max_businesses'),
            'businessCount' => $user->businesses()->count(),
            'planName' => $planLimits->currentPlan($user)?->name ?? 'Sin plan',
        ]);
    }

    public function store(Request $request, PlanLimits $planLimits, ActivityService $activity)
    {
        $user = $request->user();
        $businessCount = $user->businesses()->count();

        if ($planLimits->exceeded($user, 'max_businesses', $businessCount)) {
            return redirect()->route('member.businesses.index')
                ->with('error', 'Has alcanzado el limite de negocios de tu plan.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'industry_id' => [
                'required',
                Rule::exists('industries', 'id')->where('is_active', true),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:150'],
            'website' => ['nullable', 'url', 'max:255'],
            'timezone' => ['nullable', 'string', 'max:100'],
            'currency' => ['nullable', 'string', 'size:3'],
        ]);

        $industry = Industry::where('is_active', true)->findOrFail($validated['industry_id']);

        $business = Business::create([
            ...$validated,
            'user_id' => $user->id,
            'slug' => $this->uniqueSlug($validated['name']),
            'business_type' => $industry->businessType(),
            'timezone' => $validated['timezone'] ?? 'UTC',
            'currency' => strtoupper($validated['currency'] ?? 'USD'),
            'is_active' => true,
            'is_published' => false,
        ]);

        $activity->log('business_created', [
            'user' => $user,
            'actor' => $user,
            'subject' => $business,
            'description' => 'Negocio creado por miembro',
            'request' => $request,
        ]);

        return redirect()->route('member.businesses.edit', $business)
            ->with('success', 'Negocio creado correctamente.');
    }

    public function edit(Request $request, Business $business)
    {
        $user = Auth::user();
        abort_unless($business->user_id === $user->id, 403);

        $industries = Industry::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'icon']);

        return inertia('Member/Businesses/Edit', [
            'business' => $business,
            'industries' => $industries,
        ]);
    }

    public function update(Request $request, Business $business)
    {
        $user = Auth::user();
        abort_unless($business->user_id === $user->id, 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'industry_id' => ['nullable', 'exists:industries,id'],
            'description' => ['nullable', 'string', 'max:1000'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:150'],
            'website' => ['nullable', 'url', 'max:255'],
            'timezone' => ['nullable', 'string', 'max:100'],
            'is_active' => ['boolean'],
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

        while (Business::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}
