<?php

namespace Modules\Properties\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Properties\GeneralFieldService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Properties\Models\GeneralField;
use Modules\Properties\Models\GeneralFieldSection;

class GeneralFieldController extends Controller
{
    public function __construct(
        protected GeneralFieldService $generalFieldService
    ) {}

    public function index(GeneralFieldSection $generalFieldSection)
    {
        $fields = $generalFieldSection->activeFields()
            ->with('activeFieldOptions')
            ->orderBy('sort_order')
            ->get();

        return Inertia::render('Admin/Properties/GeneralFields/FieldsIndex', [
            'section' => $generalFieldSection,
            'fields' => $fields,
        ]);
    }

    public function edit(GeneralFieldSection $generalFieldSection, GeneralField $generalField)
    {
        $generalField->load('fieldOptions');

        return response()->json([
            'field' => $generalField,
        ]);
    }

    public function store(Request $request, GeneralFieldSection $generalFieldSection)
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:100'],
            'field_key' => ['nullable', 'string', 'max:100'],
            'field_type' => ['required', 'string', 'in:' . implode(',', GeneralField::FIELD_TYPES)],
            'description' => ['nullable', 'string'],
            'help_text' => ['nullable', 'string', 'max:255'],
            'placeholder' => ['nullable', 'string', 'max:255'],
            'default_value' => ['nullable', 'string'],
            'options' => ['nullable', 'array'],
            'validation_rules' => ['nullable', 'array'],
            'is_required' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $field = $this->generalFieldService->createField($generalFieldSection, $data);

        if (!empty($data['options'])) {
            foreach ($data['options'] as $option) {
                $this->generalFieldService->createFieldOption($field, $option);
            }
        }

        return redirect()->back()->with('success', 'Campo creado.');
    }

    public function update(Request $request, GeneralFieldSection $generalFieldSection, GeneralField $generalField)
    {
        if ($generalFieldSection->is_locked) {
            $data = $request->validate([
                'label' => ['sometimes', 'required', 'string', 'max:100'],
                'field_key' => ['nullable', 'string', 'max:100'],
                'field_type' => ['sometimes', 'required', 'string', 'in:' . implode(',', GeneralField::FIELD_TYPES)],
                'description' => ['nullable', 'string'],
                'help_text' => ['nullable', 'string', 'max:255'],
                'placeholder' => ['nullable', 'string', 'max:255'],
                'default_value' => ['nullable', 'string'],
                'options' => ['nullable', 'array'],
                'validation_rules' => ['nullable', 'array'],
                'is_required' => ['boolean'],
                'sort_order' => ['nullable', 'integer', 'min:0'],
            ]);
        } else {
            $data = $request->validate([
                'label' => ['sometimes', 'required', 'string', 'max:100'],
                'field_key' => ['nullable', 'string', 'max:100'],
                'field_type' => ['sometimes', 'required', 'string', 'in:' . implode(',', GeneralField::FIELD_TYPES)],
                'description' => ['nullable', 'string'],
                'help_text' => ['nullable', 'string', 'max:255'],
                'placeholder' => ['nullable', 'string', 'max:255'],
                'default_value' => ['nullable', 'string'],
                'options' => ['nullable', 'array'],
                'validation_rules' => ['nullable', 'array'],
                'is_required' => ['boolean'],
                'sort_order' => ['nullable', 'integer', 'min:0'],
                'is_active' => ['boolean'],
            ]);
        }

        if ($generalFieldSection->is_locked && isset($data['is_active']) && !$data['is_active']) {
            return redirect()->back()->with('error', 'No se puede desactivar un campo de una sección bloqueada.');
        }

        $this->generalFieldService->updateField($generalField, $data);

        if (isset($data['options'])) {
            $generalField->fieldOptions()->delete();
            foreach ($data['options'] as $option) {
                $this->generalFieldService->createFieldOption($generalField, $option);
            }
        }

        return redirect()->back()->with('success', 'Campo actualizado.');
    }

    public function destroy(GeneralFieldSection $generalFieldSection, GeneralField $generalField)
    {
        if ($generalFieldSection->is_locked) {
            return redirect()->back()->with('error', 'No se puede eliminar un campo de una sección bloqueada.');
        }

        $this->generalFieldService->deleteField($generalField);

        return redirect()->back()->with('success', 'Campo eliminado.');
    }

    public function reorder(Request $request, GeneralFieldSection $generalFieldSection)
    {
        $data = $request->validate([
            'fields' => ['required', 'array'],
            'fields.*' => ['required', 'integer'],
        ]);

        foreach ($data['fields'] as $index => $id) {
            GeneralField::where('id', $id)
                ->where('general_field_section_id', $generalFieldSection->id)
                ->update(['sort_order' => $index]);
        }

        return response()->json(['success' => true]);
    }
}
