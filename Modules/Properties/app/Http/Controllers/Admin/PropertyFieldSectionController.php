<?php

namespace Modules\Properties\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Properties\Models\PropertyFieldSection;
use Modules\Properties\Models\PropertyType;

class PropertyFieldSectionController extends Controller
{
    public function store(Request $request, PropertyType $propertyType)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $data['property_type_id'] = $propertyType->id;

        PropertyFieldSection::create($data);

        return redirect()->back()->with('success', 'Sección creada.');
    }

    public function update(Request $request, PropertyType $propertyType, PropertyFieldSection $section)
    {
        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $section->update($data);

        return redirect()->back()->with('success', 'Sección actualizada.');
    }

    public function destroy(PropertyType $propertyType, PropertyFieldSection $section)
    {
        $section->fields()->delete();
        $section->delete();

        return redirect()->back()->with('success', 'Sección eliminada.');
    }
}
