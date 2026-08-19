<?php

namespace Modules\Properties\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Properties\Models\PropertyType;

class PropertyFieldController extends Controller
{
    public function index(PropertyType $propertyType)
    {
        $propertyType->load(['sections.activeFields.fieldOptions', 'activeFields.fieldOptions']);

        return Inertia::render('Admin/Properties/Fields/Index', [
            'propertyType' => [
                'id' => $propertyType->id,
                'name' => $propertyType->name,
                'key' => $propertyType->key,
            ],
            'sections' => $propertyType->sections,
            'fields' => $propertyType->fields,
        ]);
    }

    public function store(Request $request, PropertyType $propertyType)
    {
        $data = $request->validate([
            'section_id' => ['nullable', 'exists:property_field_sections,id'],
            'label' => ['required', 'string', 'max:100'],
            'field_key' => ['required', 'string', 'max:50', 'unique:property_fields,field_key,NULL,id,property_type_id,' . $propertyType->id],
            'field_type' => ['required', 'in:text,textarea,number,decimal,price,select,multiselect,radio,checkbox,date,url,email,phone,image,gallery,address,boolean'],
            'description' => ['nullable', 'string'],
            'help_text' => ['nullable', 'string'],
            'placeholder' => ['nullable', 'string'],
            'default_value' => ['nullable', 'string'],
            'options' => ['nullable', 'array'],
            'validation_rules' => ['nullable', 'array'],
            'is_required' => ['boolean'],
            'is_active' => ['boolean'],
            'is_listable' => ['boolean'],
            'is_public' => ['boolean'],
            'is_filterable' => ['boolean'],
            'is_searchable' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['property_type_id'] = $propertyType->id;

        if (isset($data['options']) && is_array($data['options'])) {
            $data['options'] = json_encode($data['options']);
        }

        if (isset($data['validation_rules']) && is_array($data['validation_rules'])) {
            $data['validation_rules'] = json_encode($data['validation_rules']);
        }

        $field = \Modules\Properties\Models\PropertyField::create($data);

        if (! empty($request->options_list)) {
            foreach ($request->options_list as $index => $opt) {
                if (! empty($opt['value'])) {
                    \Modules\Properties\Models\PropertyTypeOption::create([
                        'property_field_id' => $field->id,
                        'value' => $opt['value'],
                        'label' => $opt['label'] ?? $opt['value'],
                        'sort_order' => $index,
                        'is_active' => true,
                    ]);
                }
            }
        }

        return redirect()->back()->with('success', 'Campo creado.');
    }

    public function update(Request $request, PropertyType $propertyType, \Modules\Properties\Models\PropertyField $field)
    {
        $field->load('section.generalFieldSection');
        $section = $field->section;
        $isLockedSection = $section && $section->generalFieldSection && $section->generalFieldSection->is_locked;

        if ($isLockedSection) {
            $data = $request->validate([
                'label' => ['sometimes', 'required', 'string', 'max:100'],
                'field_type' => ['sometimes', 'required', 'in:text,textarea,number,decimal,price,select,multiselect,radio,checkbox,date,url,email,phone,image,gallery,address,boolean'],
                'description' => ['nullable', 'string'],
                'help_text' => ['nullable', 'string'],
                'placeholder' => ['nullable', 'string'],
                'default_value' => ['nullable', 'string'],
                'options' => ['nullable', 'array'],
                'validation_rules' => ['nullable', 'array'],
                'is_required' => ['boolean'],
                'sort_order' => ['nullable', 'integer', 'min:0'],
            ]);
        } else {
            $data = $request->validate([
                'section_id' => ['nullable', 'exists:property_field_sections,id'],
                'label' => ['sometimes', 'required', 'string', 'max:100'],
                'field_type' => ['sometimes', 'required', 'in:text,textarea,number,decimal,price,select,multiselect,radio,checkbox,date,url,email,phone,image,gallery,address,boolean'],
                'description' => ['nullable', 'string'],
                'help_text' => ['nullable', 'string'],
                'placeholder' => ['nullable', 'string'],
                'default_value' => ['nullable', 'string'],
                'options' => ['nullable', 'array'],
                'validation_rules' => ['nullable', 'array'],
                'is_required' => ['boolean'],
                'is_active' => ['boolean'],
                'is_listable' => ['boolean'],
                'is_public' => ['boolean'],
                'is_filterable' => ['boolean'],
                'is_searchable' => ['boolean'],
                'sort_order' => ['nullable', 'integer', 'min:0'],
            ]);
        }

        if ($isLockedSection && isset($data['is_active']) && !$data['is_active']) {
            return redirect()->back()->with('error', 'No se puede desactivar un campo de una sección bloqueada.');
        }

        if (isset($data['options']) && is_array($data['options'])) {
            $data['options'] = json_encode($data['options']);
        }

        if (isset($data['validation_rules']) && is_array($data['validation_rules'])) {
            $data['validation_rules'] = json_encode($data['validation_rules']);
        }

        $field->update($data);

        return redirect()->back()->with('success', 'Campo actualizado.');
    }

    public function destroy(PropertyType $propertyType, \Modules\Properties\Models\PropertyField $field)
    {
        $field->load('section.generalFieldSection');
        $section = $field->section;
        $isLockedSection = $section && $section->generalFieldSection && $section->generalFieldSection->is_locked;

        if ($isLockedSection) {
            return redirect()->back()->with('error', 'No se puede eliminar un campo de una sección bloqueada.');
        }

        $field->fieldOptions()->delete();
        $field->delete();

        return redirect()->back()->with('success', 'Campo eliminado.');
    }
}
