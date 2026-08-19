<?php

namespace Modules\Properties\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Modules\Properties\Models\PropertyType;

class PropertyTypeController extends Controller
{
    public function index()
    {
        $types = PropertyType::orderBy('sort_order')->get();

        return Inertia::render('Admin/Properties/Types/Index', [
            'types' => $types,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'key' => ['required', 'string', 'max:50', 'unique:property_types,key'],
            'slug' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:100'],
            'is_active' => ['boolean'],
            'is_public' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['key'] = Str::slug($data['key'], '_');

        PropertyType::create($data);

        return redirect()->back()->with('success', 'Tipo de propiedad creado.');
    }

    public function update(Request $request, PropertyType $propertyType)
    {
        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:100'],
            'slug' => ['sometimes', 'required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:100'],
            'is_active' => ['boolean'],
            'is_public' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        if (isset($data['slug'])) {
            $data['slug'] = Str::slug($data['slug']);
        }

        $propertyType->update($data);

        return redirect()->back()->with('success', 'Tipo de propiedad actualizado.');
    }

    public function destroy(PropertyType $propertyType)
    {
        if ($propertyType->properties()->count() > 0) {
            return redirect()->back()->with('error', 'No se puede eliminar un tipo con propiedades.');
        }

        $propertyType->fields()->delete();
        $propertyType->sections()->delete();
        $propertyType->delete();

        return redirect()->back()->with('success', 'Tipo de propiedad eliminado.');
    }
}
