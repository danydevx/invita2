<?php

namespace Modules\Properties\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Properties\Models\PropertyTypeOption;
use Modules\Properties\Models\PropertyField;

class PropertyTypeOptionController extends Controller
{
    public function store(Request $request, PropertyField $field)
    {
        $data = $request->validate([
            'value' => ['required', 'string', 'max:100'],
            'label' => ['required', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $data['property_field_id'] = $field->id;

        PropertyTypeOption::create($data);

        return redirect()->back()->with('success', 'Opción creada.');
    }

    public function update(Request $request, PropertyField $field, PropertyTypeOption $option)
    {
        $data = $request->validate([
            'value' => ['sometimes', 'required', 'string', 'max:100'],
            'label' => ['sometimes', 'required', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $option->update($data);

        return redirect()->back()->with('success', 'Opción actualizada.');
    }

    public function destroy(PropertyField $field, PropertyTypeOption $option)
    {
        $option->delete();

        return redirect()->back()->with('success', 'Opción eliminada.');
    }
}
