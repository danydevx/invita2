<?php

namespace Modules\Features\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Features\Models\Feature;
use Modules\Features\Models\FeatureCategory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class FeatureController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAnyAdmin', Feature::class);

        $query = Feature::with('category')
            ->whereNull('business_id')
            ->whereNull('source_feature_id');

        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('search') && $request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $features = $query->orderBy('category_id')
            ->orderBy('sort_order')
            ->paginate(20);

        $categories = FeatureCategory::withCount('predefinedFeatures')
            ->orderBy('sort_order')
            ->get();

        return Inertia::render('Admin/Features/Index', [
            'features' => $features,
            'categories' => $categories,
            'filters' => $request->only(['category_id', 'search']),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('createAdmin', Feature::class);

        $validated = $request->validate([
            'category_id' => ['nullable', 'exists:feature_categories,id'],
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:100'],
            'image' => ['nullable', 'file', 'mimes:jpeg,png,webp,gif', 'max:2048'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('features', ['disk' => 'public']);
            $validated['image_path'] = Storage::disk('public')->url($path);
        }

        Feature::create($validated);

        return redirect()->back()->with('success', 'Feature creada correctamente.');
    }

    public function update(Request $request, Feature $feature)
    {
        $this->authorize('updateAdmin', $feature);

        $validated = $request->validate([
            'category_id' => ['nullable', 'exists:feature_categories,id'],
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:100'],
            'image' => ['nullable', 'file', 'mimes:jpeg,png,webp,gif', 'max:2048'],
            'remove_image' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        if ($request->boolean('remove_image')) {
            if ($feature->image_path) {
                $path = str_replace(url('/') . '/storage/', '', $feature->image_path);
                Storage::disk('public')->delete($path);
            }
            $validated['image_path'] = null;
        } elseif ($request->hasFile('image')) {
            if ($feature->image_path) {
                $oldPath = str_replace(url('/') . '/storage/', '', $feature->image_path);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('image')->store('features', ['disk' => 'public']);
            $validated['image_path'] = Storage::disk('public')->url($path);
        }

        $feature->update($validated);

        return redirect()->back()->with('success', 'Feature actualizada correctamente.');
    }

    public function destroy(Feature $feature)
    {
        $this->authorize('deleteAdmin', $feature);

        if ($feature->image_path) {
            $path = str_replace(url('/') . '/storage/', '', $feature->image_path);
            Storage::disk('public')->delete($path);
        }

        $feature->delete();

        return redirect()->back()->with('success', 'Feature eliminada correctamente.');
    }
}
