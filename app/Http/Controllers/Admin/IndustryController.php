<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessModuleDefinition;
use App\Models\Industry;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class IndustryController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));

        $industries = Industry::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->with('moduleDefinitions')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $industries->getCollection()->transform(function ($industry) {
            $industry->module_count = $industry->moduleDefinitions->count();
            return $industry;
        });

        return Inertia::render('Admin/Industries/Index', [
            'industries' => $industries,
            'filters' => ['search' => $search],
        ]);
    }

    public function create()
    {
        $availableModules = BusinessModuleDefinition::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'key', 'name', 'icon', 'is_premium']);

        return Inertia::render('Admin/Industries/Create', [
            'availableModules' => $availableModules,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:150', 'unique:industries,slug'],
            'icon' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
            'module_ids' => ['array'],
            'module_ids.*' => ['exists:business_module_definitions,id'],
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        $industry = Industry::create([
            'name' => trim($data['name']),
            'slug' => $data['slug'],
            'icon' => $data['icon'] ?? null,
            'description' => $data['description'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? true),
        ]);

        if (!empty($data['module_ids'])) {
            $industry->moduleDefinitions()->attach($data['module_ids']);
        }

        return redirect()->route('admin.industries.index')
            ->with('success', 'Industria creada correctamente.');
    }

    public function edit(Industry $industry)
    {
        $industry->load('moduleDefinitions');

        $availableModules = BusinessModuleDefinition::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'key', 'name', 'icon', 'is_premium']);

        return Inertia::render('Admin/Industries/Edit', [
            'industry' => [
                'id' => $industry->id,
                'name' => $industry->name,
                'slug' => $industry->slug,
                'icon' => $industry->icon,
                'description' => $industry->description,
                'is_active' => (bool) $industry->is_active,
                'module_ids' => $industry->moduleDefinitions->pluck('id')->toArray(),
            ],
            'availableModules' => $availableModules,
        ]);
    }

    public function update(Request $request, Industry $industry)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:150', 'unique:industries,slug,' . $industry->id],
            'icon' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
            'module_ids' => ['array'],
            'module_ids.*' => ['exists:business_module_definitions,id'],
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        $industry->update([
            'name' => trim($data['name']),
            'slug' => $data['slug'],
            'icon' => $data['icon'] ?? null,
            'description' => $data['description'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? true),
        ]);

        $industry->moduleDefinitions()->sync($data['module_ids'] ?? []);

        return redirect()->route('admin.industries.index')
            ->with('success', 'Industria actualizada correctamente.');
    }

    public function destroy(Industry $industry)
    {
        if ($industry->businesses()->count() > 0) {
            return redirect()->route('admin.industries.index')
                ->with('error', 'No se puede eliminar una industria que tiene negocios asociados.');
        }

        $industry->moduleDefinitions()->detach();
        $industry->delete();

        return redirect()->route('admin.industries.index')
            ->with('success', 'Industria eliminada correctamente.');
    }
}
