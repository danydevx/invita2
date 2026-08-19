<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeatureFlag;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class FeatureFlagController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));

        $flags = FeatureFlag::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('key', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                });
            })
            ->orderBy('key')
            ->paginate(15)
            ->withQueryString()
            ->through(fn ($flag) => [
                'id' => $flag->id,
                'key' => $flag->key,
                'name' => $flag->name,
                'type' => $flag->type,
                'default_value' => $flag->default_value,
                'is_active' => $flag->is_active,
                'created_at' => $flag->created_at?->toDateTimeString(),
            ]);

        return Inertia::render('Admin/FeatureFlags/Index', [
            'flags' => $flags,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/FeatureFlags/Create');
    }

    public function store(Request $request, ActivityService $activity)
    {
        $data = $request->validate([
            'key' => ['required', 'string', 'max:150', 'unique:feature_flags,key'],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:500'],
            'type' => ['required', 'string', Rule::in(['boolean', 'string', 'integer'])],
            'default_value' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $flag = FeatureFlag::create([
            'key' => trim($data['key']),
            'name' => trim($data['name']),
            'description' => $data['description'] ?? null,
            'type' => $data['type'],
            'default_value' => $data['default_value'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? false),
        ]);

        $activity->log('feature_flag_created', [
            'actor' => $request->user(),
            'subject' => $flag,
            'description' => 'Feature flag creada',
            'request' => $request,
        ]);

        return redirect()->route('admin.feature-flags.index');
    }

    public function edit(FeatureFlag $flag)
    {
        return Inertia::render('Admin/FeatureFlags/Edit', [
            'flag' => [
                'id' => $flag->id,
                'key' => $flag->key,
                'name' => $flag->name,
                'description' => $flag->description,
                'type' => $flag->type,
                'default_value' => $flag->default_value,
                'is_active' => $flag->is_active,
            ],
        ]);
    }

    public function update(Request $request, FeatureFlag $flag, ActivityService $activity)
    {
        $data = $request->validate([
            'key' => ['required', 'string', 'max:150', Rule::unique('feature_flags', 'key')->ignore($flag->id)],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:500'],
            'type' => ['required', 'string', Rule::in(['boolean', 'string', 'integer'])],
            'default_value' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $flag->update([
            'key' => trim($data['key']),
            'name' => trim($data['name']),
            'description' => $data['description'] ?? null,
            'type' => $data['type'],
            'default_value' => $data['default_value'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? false),
        ]);

        $activity->log('feature_flag_updated', [
            'actor' => $request->user(),
            'subject' => $flag,
            'description' => 'Feature flag actualizada',
            'request' => $request,
        ]);

        return redirect()->route('admin.feature-flags.index');
    }
}
