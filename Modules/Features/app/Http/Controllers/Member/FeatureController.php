<?php

namespace Modules\Features\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Businesses\Models\Business;
use Modules\Features\Models\Feature;
use Modules\Features\Models\FeatureCategory;
use Modules\Features\Models\BusinessFeature;
use Illuminate\Support\Facades\Storage;

class FeatureController extends Controller
{
    private const MAX_FEATURES_PER_BUSINESS = 40;

    public function index(Request $request, Business $business)
    {
        $this->authorize('viewAnyMember', [Feature::class, $business]);

        $predefinedCategories = FeatureCategory::with(['predefinedFeatures' => function ($q) {
            $q->where('is_active', true)->orderBy('sort_order');
        }])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(function ($cat) use ($business) {
                $importedIds = $business->features()
                    ->whereNotNull('source_feature_id')
                    ->pluck('source_feature_id')
                    ->toArray();

                return [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'icon' => $cat->icon,
                    'features' => $cat->predefinedFeatures->map(function ($f) use ($importedIds) {
                        return [
                            'id' => $f->id,
                            'title' => $f->title,
                            'description' => $f->description,
                            'icon' => $f->icon,
                            'image_path' => $f->image_path,
                            'is_imported' => in_array($f->id, $importedIds),
                        ];
                    }),
                ];
            });

        $importedSourceIds = Feature::where('business_id', $business->id)
            ->whereNotNull('source_feature_id')
            ->pluck('source_feature_id')
            ->toArray();

        $availableFeatures = Feature::whereNull('business_id')
            ->whereNull('source_feature_id')
            ->where('is_active', true)
            ->whereNotIn('id', $importedSourceIds)
            ->with('category')
            ->orderBy('title')
            ->get()
            ->map(function ($f) {
                return [
                    'id' => $f->id,
                    'title' => $f->title,
                    'description' => $f->description,
                    'icon' => $f->icon,
                    'image_path' => $f->image_path,
                    'category_id' => $f->category_id,
                    'category' => $f->category ? [
                        'id' => $f->category->id,
                        'name' => $f->category->name,
                        'icon' => $f->category->icon,
                    ] : null,
                ];
            });

        $customFeatures = Feature::where('business_id', $business->id)
            ->whereNull('source_feature_id')
            ->whereNotNull('business_id')
            ->orderBy('sort_order')
            ->get();

        $businessFeaturesPaginated = BusinessFeature::with('feature', 'location')
            ->where('business_id', $business->id)
            ->orderBy('sort_order')
            ->paginate(30);

        $businessFeatures = $businessFeaturesPaginated->getCollection()->map(function ($bf) {
            return [
                'id' => $bf->id,
                'feature_id' => $bf->feature_id,
                'source_feature_id' => $bf->feature->source_feature_id,
                'feature_title' => $bf->feature->title,
                'feature_description' => $bf->feature->description,
                'feature_icon' => $bf->feature->icon,
                'feature_image' => $bf->feature->image_path,
                'location_id' => $bf->location_id,
                'location_name' => $bf->location?->name,
                'is_active' => $bf->is_active,
                'sort_order' => $bf->sort_order,
            ];
        });

        $locations = $business->locations()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $featureCount = BusinessFeature::where('business_id', $business->id)->count();

        return Inertia::render('Member/Features/Index', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'predefinedCategories' => $predefinedCategories,
            'availableFeatures' => $availableFeatures,
            'customFeatures' => $customFeatures,
            'businessFeatures' => $businessFeatures,
            'businessFeaturesPaginated' => [
                'current_page' => $businessFeaturesPaginated->currentPage(),
                'last_page' => $businessFeaturesPaginated->lastPage(),
                'per_page' => $businessFeaturesPaginated->perPage(),
                'total' => $businessFeaturesPaginated->total(),
            ],
            'locations' => $locations,
            'featureCount' => $featureCount,
            'maxFeatures' => self::MAX_FEATURES_PER_BUSINESS,
        ]);
    }

    public function store(Request $request, Business $business)
    {
        $this->authorize('createMember', [Feature::class, $business]);

        $currentCount = BusinessFeature::where('business_id', $business->id)->count();
        if ($currentCount >= self::MAX_FEATURES_PER_BUSINESS) {
            return redirect()->back()->with('error', 'Has alcanzado el limite de ' . self::MAX_FEATURES_PER_BUSINESS . ' features.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:100'],
            'image' => ['nullable', 'file', 'mimes:jpeg,png,webp,gif', 'max:2048'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'assign_to' => ['nullable', 'in:business,location'],
            'location_id' => ['nullable', 'exists:business_locations,id'],
        ]);

        $feature = Feature::create([
            'business_id' => $business->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'icon' => $validated['icon'] ?? null,
            'image_path' => null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('features/' . $business->id, ['disk' => 'public']);
            $feature->update(['image_path' => Storage::disk('public')->url($path)]);
        }

        $locationId = ($validated['assign_to'] ?? 'business') === 'location'
            ? ($validated['location_id'] ?? null)
            : null;

        BusinessFeature::create([
            'business_id' => $business->id,
            'feature_id' => $feature->id,
            'location_id' => $locationId,
            'is_active' => $feature->is_active,
        ]);

        return redirect()->back()->with('success', 'Feature creada correctamente.');
    }

    public function importBulk(Request $request, Business $business)
    {
        $this->authorize('import', [Feature::class, $business]);

        $validated = $request->validate([
            'feature_ids' => ['required', 'array', 'min:1'],
            'feature_ids.*' => ['required', 'exists:features,id'],
        ]);

        $currentCount = BusinessFeature::where('business_id', $business->id)->count();
        $remainingSlots = self::MAX_FEATURES_PER_BUSINESS - $currentCount;

        if ($remainingSlots <= 0) {
            return redirect()->back()->with('error', 'Has alcanzado el limite de ' . self::MAX_FEATURES_PER_BUSINESS . ' features.');
        }

        $featuresToImport = array_slice($validated['feature_ids'], 0, $remainingSlots);
        $importedCount = 0;
        $errors = [];

        $importedSourceIds = Feature::where('business_id', $business->id)
            ->whereNotNull('source_feature_id')
            ->pluck('source_feature_id')
            ->toArray();

        foreach ($featuresToImport as $featureId) {
            if (in_array($featureId, $importedSourceIds)) {
                $errors[] = "Feature ID $featureId ya fue importada.";
                continue;
            }

            $sourceFeature = Feature::find($featureId);
            if (!$sourceFeature || !$sourceFeature->isPredefined()) {
                continue;
            }

            $clone = Feature::create([
                'business_id' => $business->id,
                'source_feature_id' => $sourceFeature->id,
                'title' => $sourceFeature->title,
                'description' => $sourceFeature->description,
                'icon' => $sourceFeature->icon,
                'image_path' => $sourceFeature->image_path,
                'sort_order' => $sourceFeature->sort_order,
                'is_active' => true,
            ]);

            BusinessFeature::create([
                'business_id' => $business->id,
                'feature_id' => $clone->id,
                'location_id' => null,
                'is_active' => true,
            ]);

            $importedCount++;
        }

        if ($importedCount > 0) {
            return redirect()->back()->with('success', "$importedCount caracteristicas importadas correctamente.");
        }

        return redirect()->back()->with('error', 'No se pudo importar ninguna caracteristica.');
    }

    public function import(Request $request, Business $business, Feature $feature)
    {
        $this->authorize('import', [Feature::class, $business]);

        if (!$feature->isPredefined()) {
            return redirect()->back()->with('error', 'Solo puedes importar features predefinidas.');
        }

        $currentCount = BusinessFeature::where('business_id', $business->id)->count();
        if ($currentCount >= self::MAX_FEATURES_PER_BUSINESS) {
            return redirect()->back()->with('error', 'Has alcanzado el limite de ' . self::MAX_FEATURES_PER_BUSINESS . ' features.');
        }

        $existingClone = Feature::where('business_id', $business->id)
            ->where('source_feature_id', $feature->id)
            ->first();

        if ($existingClone) {
            return redirect()->back()->with('error', 'Esta feature ya fue importada.');
        }

        $clone = Feature::create([
            'business_id' => $business->id,
            'source_feature_id' => $feature->id,
            'title' => $feature->title,
            'description' => $feature->description,
            'icon' => $feature->icon,
            'image_path' => $feature->image_path,
            'sort_order' => $feature->sort_order,
            'is_active' => true,
        ]);

        $maxSortOrder = BusinessFeature::where('business_id', $business->id)->max('sort_order') ?? 0;

        BusinessFeature::create([
            'business_id' => $business->id,
            'feature_id' => $clone->id,
            'location_id' => null,
            'is_active' => true,
            'sort_order' => $maxSortOrder + 1,
        ]);

        return redirect()->back()->with('success', 'Feature importada correctamente.');
    }

    public function update(Request $request, Business $business, Feature $feature)
    {
        $this->authorize('updateMember', [$feature, $business]);

        $validated = $request->validate([
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
            $path = $request->file('image')->store('features/' . $business->id, ['disk' => 'public']);
            $validated['image_path'] = Storage::disk('public')->url($path);
        }

        $feature->update($validated);

        return redirect()->back()->with('success', 'Feature actualizada correctamente.');
    }

    public function destroy(Request $request, Business $business, Feature $feature)
    {
        $this->authorize('deleteMember', [$feature, $business]);

        if ($feature->image_path) {
            $path = str_replace(url('/') . '/storage/', '', $feature->image_path);
            Storage::disk('public')->delete($path);
        }

        BusinessFeature::where('feature_id', $feature->id)->delete();
        $feature->delete();

        return redirect()->back()->with('success', 'Feature eliminada correctamente.');
    }

    public function updateAssignment(Request $request, Business $business)
    {
        $user = $request->user();
        if (!$user->hasAnyRole(['superadmin', 'admin']) && $business->user_id !== $user->id) {
            abort(403, 'No tienes permiso para modificar esta asignacion.');
        }

        $validated = $request->validate([
            'feature_id' => ['required', 'exists:features,id'],
            'location_id' => ['nullable', 'exists:business_locations,id'],
            'is_active' => ['boolean'],
        ]);

        $businessFeature = BusinessFeature::where('business_id', $business->id)
            ->where('feature_id', $validated['feature_id'])
            ->first();

        if (!$businessFeature) {
            return redirect()->back()->with('error', 'Asignacion no encontrada.');
        }

        $businessFeature->update([
            'location_id' => $validated['location_id'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->back()->with('success', 'Asignacion actualizada.');
    }

    public function removeAssignment(Request $request, Business $business, $assignmentId)
    {
        $user = $request->user();
        if (!$user->hasAnyRole(['superadmin', 'admin']) && $business->user_id !== $user->id) {
            abort(403, 'No tienes permiso para remover esta asignacion.');
        }

        $businessFeature = BusinessFeature::where('business_id', $business->id)
            ->where('id', $assignmentId)
            ->first();

        if (!$businessFeature) {
            return redirect()->back()->with('error', 'Asignacion no encontrada.');
        }

        $feature = $businessFeature->feature;

        if ($feature->isClone() || $feature->isCustom()) {
            BusinessFeature::where('id', $assignmentId)->delete();
            $feature->delete();
        } else {
            BusinessFeature::where('id', $assignmentId)->delete();
        }

        return redirect()->back()->with('success', 'Feature removida del negocio.');
    }

    public function reorder(Request $request, Business $business)
    {
        $user = $request->user();
        if (!$user->hasAnyRole(['superadmin', 'admin']) && $business->user_id !== $user->id) {
            abort(403, 'No tienes permiso para reordenar.');
        }

        $validated = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['required', 'exists:business_features,id'],
        ]);

        foreach ($validated['order'] as $index => $id) {
            BusinessFeature::where('id', $id)
                ->where('business_id', $business->id)
                ->update(['sort_order' => $index]);
        }

        return redirect()->back();
    }
}
