<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\MinisiteTheme;
use App\Models\Industry;
use Modules\Listings\Models\Listing;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ListingController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));

        $listings = Listing::query()
            ->with('user')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Listings/Index', [
            'listings' => $listings,
            'filters' => ['search' => $search],
        ]);
    }

    public function create()
    {
        $users = User::role('member')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $industries = Industry::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Admin/Listings/Create', [
            'users' => $users,
            'industries' => $industries,
        ]);
    }

    public function store(Request $request, ActivityService $activity)
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['required', 'string', 'max:150', 'unique:listings,slug'],
            'industry_id' => ['nullable', 'exists:industries,id'],
            'timezone' => ['nullable', 'string', 'max:100'],
            'currency' => ['nullable', 'string', 'max:10'],
            'is_active' => ['boolean'],
            'is_published' => ['boolean'],
        ]);

        $listing = Listing::create([
            'user_id' => $data['user_id'],
            'name' => trim($data['name']),
            'slug' => trim($data['slug']),
            'business_type' => 'generic',
            'industry_id' => $data['industry_id'] ?? null,
            'timezone' => $data['timezone'] ?? 'UTC',
            'currency' => $data['currency'] ?? 'MXN',
            'is_active' => (bool) ($data['is_active'] ?? true),
            'is_published' => (bool) ($data['is_published'] ?? false),
        ]);

        $activity->log('listing_created', [
            'actor' => $request->user(),
            'subject' => $listing,
            'description' => 'Listing created',
            'request' => $request,
        ]);

        return redirect()->route('admin.listings.index')
            ->with('success', 'Listing created successfully.');
    }

    public function edit(Listing $listing)
    {
        $users = User::role('member')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $themes = MinisiteTheme::where('is_active', true)->orderBy('name')->get(['id', 'name', 'slug']);
        $industries = Industry::orderBy('name')->get(['id', 'name']);

        $listing->load('user', 'minisiteTheme');

        return Inertia::render('Admin/Listings/Edit', [
            'listing' => [
                'id' => $listing->id,
                'user_id' => $listing->user_id,
                'name' => $listing->name,
                'slug' => $listing->slug,
                'industry_id' => $listing->industry_id,
                'timezone' => $listing->timezone,
                'currency' => $listing->currency,
                'is_active' => (bool) $listing->is_active,
                'is_published' => (bool) $listing->is_published,
                'minisite_theme_id' => $listing->minisite_theme_id,
                'user' => $listing->user ? [
                    'id' => $listing->user->id,
                    'name' => $listing->user->name,
                    'email' => $listing->user->email,
                ] : null,
            ],
            'users' => $users,
            'themes' => $themes,
            'industries' => $industries,
        ]);
    }

    public function update(Request $request, Listing $listing, ActivityService $activity)
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['required', 'string', 'max:150', 'unique:listings,slug,' . $listing->id],
            'industry_id' => ['nullable', 'exists:industries,id'],
            'timezone' => ['nullable', 'string', 'max:100'],
            'currency' => ['nullable', 'string', 'max:10'],
            'is_active' => ['boolean'],
            'is_published' => ['boolean'],
            'minisite_theme_id' => ['nullable', 'exists:minisite_themes,id'],
        ]);

        $listing->update([
            'user_id' => $data['user_id'],
            'name' => trim($data['name']),
            'slug' => trim($data['slug']),
            'industry_id' => $data['industry_id'] ?? null,
            'timezone' => $data['timezone'] ?? 'UTC',
            'currency' => $data['currency'] ?? 'MXN',
            'is_active' => (bool) ($data['is_active'] ?? true),
            'is_published' => (bool) ($data['is_published'] ?? false),
            'minisite_theme_id' => $data['minisite_theme_id'] ?? null,
        ]);

        $activity->log('listing_updated', [
            'actor' => $request->user(),
            'subject' => $listing,
            'description' => 'Listing updated',
            'request' => $request,
        ]);

        return redirect()->route('admin.listings.index')
            ->with('success', 'Listing updated successfully.');
    }

    public function destroy(Listing $listing, ActivityService $activity)
    {
        $activity->log('listing_deleted', [
            'actor' => request()->user(),
            'subject' => $listing,
            'description' => 'Listing deleted',
        ]);

        $listing->delete();

        return redirect()->route('admin.listings.index')
            ->with('success', 'Listing deleted successfully.');
    }
}
