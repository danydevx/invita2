<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\MinisiteTheme;
use App\Models\Industry;
use Modules\Businesses\Models\Business;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BusinessController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));

        $businesses = Business::query()
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

        return Inertia::render('Admin/Businesses/Index', [
            'businesses' => $businesses,
            'filters' => ['search' => $search],
        ]);
    }

    public function create()
    {
        $users = User::role('member')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $industries = Industry::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Admin/Businesses/Create', [
            'users' => $users,
            'industries' => $industries,
        ]);
    }

    public function store(Request $request, ActivityService $activity)
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['required', 'string', 'max:150', 'unique:businesses,slug'],
            'business_type' => ['required', 'string'],
            'industry_id' => ['nullable', 'exists:industries,id'],
            'description' => ['nullable', 'string', 'max:1000'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:150'],
            'website' => ['nullable', 'url', 'max:255'],
            'timezone' => ['nullable', 'string', 'max:100'],
            'currency' => ['nullable', 'string', 'max:10'],
            'is_active' => ['boolean'],
            'is_published' => ['boolean'],
        ]);

        $business = Business::create([
            'user_id' => $data['user_id'],
            'name' => trim($data['name']),
            'slug' => trim($data['slug']),
            'business_type' => $data['business_type'],
            'industry_id' => $data['industry_id'] ?? null,
            'description' => $data['description'] ?? null,
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'website' => $data['website'] ?? null,
            'timezone' => $data['timezone'] ?? 'UTC',
            'currency' => $data['currency'] ?? 'USD',
            'is_active' => (bool) ($data['is_active'] ?? true),
            'is_published' => (bool) ($data['is_published'] ?? false),
        ]);

        $activity->log('business_created', [
            'actor' => $request->user(),
            'subject' => $business,
            'description' => 'Negocio creado',
            'request' => $request,
        ]);

        return redirect()->route('admin.businesses.index')
            ->with('success', 'Negocio creado correctamente.');
    }

    public function edit(Business $business)
    {
        $users = User::role('member')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $themes = MinisiteTheme::where('is_active', true)->orderBy('name')->get(['id', 'name', 'slug']);
        $industries = Industry::orderBy('name')->get(['id', 'name']);

        $business->load('user', 'minisiteTheme');

        return Inertia::render('Admin/Businesses/Edit', [
            'business' => [
                'id' => $business->id,
                'user_id' => $business->user_id,
                'name' => $business->name,
                'slug' => $business->slug,
                'business_type' => $business->business_type->value ?? $business->business_type,
                'industry_id' => $business->industry_id,
                'description' => $business->description,
                'phone' => $business->phone,
                'email' => $business->email,
                'website' => $business->website,
                'timezone' => $business->timezone,
                'currency' => $business->currency,
                'is_active' => (bool) $business->is_active,
                'is_published' => (bool) $business->is_published,
                'minisite_theme_id' => $business->minisite_theme_id,
                'user' => $business->user ? [
                    'id' => $business->user->id,
                    'name' => $business->user->name,
                    'email' => $business->user->email,
                ] : null,
            ],
            'users' => $users,
            'themes' => $themes,
            'industries' => $industries,
        ]);
    }

    public function update(Request $request, Business $business, ActivityService $activity)
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['required', 'string', 'max:150', 'unique:businesses,slug,' . $business->id],
            'business_type' => ['required', 'string'],
            'industry_id' => ['nullable', 'exists:industries,id'],
            'description' => ['nullable', 'string', 'max:1000'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:150'],
            'website' => ['nullable', 'url', 'max:255'],
            'timezone' => ['nullable', 'string', 'max:100'],
            'currency' => ['nullable', 'string', 'max:10'],
            'is_active' => ['boolean'],
            'is_published' => ['boolean'],
            'minisite_theme_id' => ['nullable', 'exists:minisite_themes,id'],
        ]);

        $business->update([
            'user_id' => $data['user_id'],
            'name' => trim($data['name']),
            'slug' => trim($data['slug']),
            'business_type' => $data['business_type'],
            'industry_id' => $data['industry_id'] ?? null,
            'description' => $data['description'] ?? null,
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'website' => $data['website'] ?? null,
            'timezone' => $data['timezone'] ?? 'UTC',
            'currency' => $data['currency'] ?? 'USD',
            'is_active' => (bool) ($data['is_active'] ?? true),
            'is_published' => (bool) ($data['is_published'] ?? false),
            'minisite_theme_id' => $data['minisite_theme_id'] ?? null,
        ]);

        $activity->log('business_updated', [
            'actor' => $request->user(),
            'subject' => $business,
            'description' => 'Negocio actualizado',
            'request' => $request,
        ]);

        return redirect()->route('admin.businesses.index')
            ->with('success', 'Negocio actualizado correctamente.');
    }

    public function destroy(Business $business, ActivityService $activity)
    {
        $activity->log('business_deleted', [
            'actor' => request()->user(),
            'subject' => $business,
            'description' => 'Negocio eliminado',
        ]);

        $business->delete();

        return redirect()->route('admin.businesses.index')
            ->with('success', 'Negocio eliminado correctamente.');
    }
}
