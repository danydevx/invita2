<?php

namespace Modules\Features\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Features\Models\FeatureCategory;
use Illuminate\Support\Str;

class FeatureCategoryController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', FeatureCategory::class);

        $categories = FeatureCategory::withCount('predefinedFeatures')
            ->orderBy('sort_order')
            ->get();

        return Inertia::render('Admin/Features/Categories/Index', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', FeatureCategory::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'icon' => ['nullable', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        FeatureCategory::create($validated);

        return redirect()->back()->with('success', 'Categoria creada correctamente.');
    }

    public function update(Request $request, FeatureCategory $category)
    {
        $this->authorize('update', $category);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'icon' => ['nullable', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $category->update($validated);

        return redirect()->back()->with('success', 'Categoria actualizada correctamente.');
    }

    public function destroy(FeatureCategory $category)
    {
        $this->authorize('delete', $category);

        if ($category->features()->whereNotNull('business_id')->exists()) {
            return redirect()->back()->with('error', 'No se puede eliminar una categoria con features asignados a negocios.');
        }

        $category->delete();

        return redirect()->back()->with('success', 'Categoria eliminada correctamente.');
    }
}
