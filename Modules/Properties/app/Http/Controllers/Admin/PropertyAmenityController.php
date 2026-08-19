<?php

namespace Modules\Properties\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Properties\Models\PropertyAmenity;
use Illuminate\Support\Str;

class PropertyAmenityController extends Controller
{
    public function index()
    {
        $amenities = PropertyAmenity::sorted()
            ->active()
            ->get()
            ->map(fn($a) => [
                'id' => $a->id,
                'key' => $a->key,
                'name' => $a->name,
                'icon' => $a->icon,
                'sort_order' => $a->sort_order,
                'is_active' => $a->is_active,
            ]);

        return Inertia::render('Admin/Properties/Amenities/Index', [
            'amenities' => $amenities,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'icon' => ['nullable', 'string', 'max:50'],
        ]);

        $data['key'] = Str::slug($data['name']);
        $data['sort_order'] = PropertyAmenity::max('sort_order') + 1;
        $data['is_active'] = true;

        $amenity = PropertyAmenity::create($data);

        return redirect()->back()->with('success', 'Amenidad creada.');
    }

    public function update(Request $request, PropertyAmenity $amenity)
    {
        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:100'],
            'icon' => ['nullable', 'string', 'max:50'],
            'is_active' => ['boolean'],
        ]);

        if (isset($data['name'])) {
            $data['key'] = Str::slug($data['name']);
        }

        $amenity->update($data);

        return redirect()->back()->with('success', 'Amenidad actualizada.');
    }

    public function destroy(PropertyAmenity $amenity)
    {
        $amenity->delete();

        return redirect()->back()->with('success', 'Amenidad eliminada.');
    }

    public function reorder(Request $request)
    {
        $data = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:property_amenities,id'],
        ]);

        foreach ($data['ids'] as $index => $id) {
            PropertyAmenity::where('id', $id)->update(['sort_order' => $index]);
        }

        return redirect()->back();
    }
}
