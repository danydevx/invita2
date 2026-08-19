<?php

namespace Modules\Properties\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Properties\Models\PropertyType;
use Modules\Properties\Models\PropertyAmenity;
use Modules\Properties\Models\PropertyAmenityPropertyType;

class PropertyTypeAmenityController extends Controller
{
    public function edit(PropertyType $propertyType)
    {
        $allAmenities = PropertyAmenity::active()
            ->sorted()
            ->get()
            ->map(fn($a) => [
                'id' => $a->id,
                'key' => $a->key,
                'name' => $a->name,
                'icon' => $a->icon,
            ]);

        $assignedIds = $propertyType->amenities()
            ->pluck('property_amenities.id')
            ->toArray();

        $assignedAmenities = $propertyType->amenities()
            ->sorted()
            ->get()
            ->map(fn($a) => [
                'id' => $a->id,
                'key' => $a->key,
                'name' => $a->name,
                'icon' => $a->icon,
                'sort_order' => $a->pivot->sort_order,
            ]);

        return Inertia::render('Admin/Properties/PropertyTypes/Amenities', [
            'propertyType' => [
                'id' => $propertyType->id,
                'name' => $propertyType->name,
            ],
            'allAmenities' => $allAmenities,
            'assignedAmenities' => $assignedAmenities,
            'assignedIds' => $assignedIds,
        ]);
    }

    public function sync(Request $request, PropertyType $propertyType)
    {
        $data = $request->validate([
            'amenity_ids' => ['array'],
            'amenity_ids.*' => ['integer', 'exists:property_amenities,id'],
        ]);

        $ids = $data['amenity_ids'] ?? [];

        PropertyAmenityPropertyType::where('property_type_id', $propertyType->id)->delete();

        foreach ($ids as $index => $amenityId) {
            PropertyAmenityPropertyType::create([
                'property_type_id' => $propertyType->id,
                'property_amenity_id' => $amenityId,
                'sort_order' => $index,
                'is_active' => true,
            ]);
        }

        return redirect()->back()->with('success', 'Amenidades asignadas.');
    }
}
