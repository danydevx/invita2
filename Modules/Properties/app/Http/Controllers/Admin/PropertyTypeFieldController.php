<?php

namespace Modules\Properties\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Properties\GeneralFieldService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Properties\Models\GeneralFieldSection;
use Modules\Properties\Models\PropertyAmenity;
use Modules\Properties\Models\PropertyType;

class PropertyTypeFieldController extends Controller
{
    public function __construct(
        protected GeneralFieldService $generalFieldService
    ) {}

    public function edit(PropertyType $propertyType)
    {
        $generalSections = $this->generalFieldService->getGeneralSections();
        $assignedSections = $propertyType->generalFieldSections()
            ->with(['activeFields.activeFieldOptions'])
            ->get()
            ->map(function ($section) use ($propertyType) {
                $customizations = $this->generalFieldService->getAssignmentCustomizations($propertyType, $section);
                return [
                    'id' => $section->id,
                    'name' => $section->name,
                    'description' => $section->description,
                    'icon' => $section->icon,
                    'sort_order' => $section->pivot->sort_order ?? $section->sort_order,
                    'is_locked' => $section->is_locked,
                    'is_general' => true,
                    'general_field_section_id' => $section->id,
                    'custom_settings' => $customizations,
                    'fields' => $section->activeFields->map(fn($f) => [
                        'id' => $f->id,
                        'field_key' => $f->field_key,
                        'field_type' => $f->field_type,
                        'label' => $f->label,
                        'help_text' => $f->help_text,
                        'is_required' => $f->is_required,
                    ])->toArray(),
                ];
            })
            ->toArray();

        $exclusiveSections = $propertyType->activeSections()
            ->where('is_general', false)
            ->with(['activeFields.activeFieldOptions'])
            ->get()
            ->toArray();

        $allAmenities = PropertyAmenity::active()->sorted()->get()->map(fn($a) => [
            'id' => $a->id,
            'key' => $a->key,
            'name' => $a->name,
            'icon' => $a->icon,
        ])->toArray();

        $assignedAmenityIds = $propertyType->amenities()->pluck('property_amenities.id')->toArray();

        return Inertia::render('Admin/Properties/PropertyTypes/Fields', [
            'propertyType' => $propertyType,
            'generalSections' => $generalSections,
            'assignedSections' => $assignedSections,
            'exclusiveSections' => $exclusiveSections,
            'allAmenities' => $allAmenities,
            'assignedAmenityIds' => $assignedAmenityIds,
        ]);
    }

    public function assignSection(Request $request, PropertyType $propertyType)
    {
        $data = $request->validate([
            'section_id' => ['required', 'integer', 'exists:general_field_sections,id'],
            'custom_settings' => ['nullable', 'array'],
        ]);

        $section = GeneralFieldSection::findOrFail($data['section_id']);
        $this->generalFieldService->assignSectionToPropertyType($propertyType, $section, $data['custom_settings'] ?? null);

        return redirect()->back()->with('success', 'Sección asignada.');
    }

    public function assignSections(Request $request, PropertyType $propertyType)
    {
        $data = $request->validate([
            'section_ids' => ['required', 'array'],
            'section_ids.*' => ['required', 'integer', 'exists:general_field_sections,id'],
        ]);

        foreach ($data['section_ids'] as $sectionId) {
            $section = GeneralFieldSection::find($sectionId);
            if ($section && !$section->is_locked) {
                $this->generalFieldService->assignSectionToPropertyType($propertyType, $section);
            }
        }

        return redirect()->back()->with('success', 'Secciones asignadas.');
    }

    public function unassignSection(PropertyType $propertyType, GeneralFieldSection $generalFieldSection)
    {
        if ($generalFieldSection->is_locked) {
            return redirect()->back()->with('error', 'Esta sección no puede desasignarse.');
        }

        $this->generalFieldService->unassignSectionFromPropertyType($propertyType, $generalFieldSection);

        return redirect()->back()->with('success', 'Sección desasignada.');
    }

    public function updateCustomizations(Request $request, PropertyType $propertyType)
    {
        $data = $request->validate([
            'section_id' => ['required', 'integer', 'exists:general_field_sections,id'],
            'custom_settings' => ['nullable', 'array'],
        ]);

        $section = GeneralFieldSection::findOrFail($data['section_id']);
        $this->generalFieldService->updateAssignmentCustomizations($propertyType, $section, $data['custom_settings'] ?? null);

        return redirect()->back()->with('success', 'Personalización actualizada.');
    }

    public function reorderSections(Request $request, PropertyType $propertyType)
    {
        $data = $request->validate([
            'section_ids' => ['required', 'array'],
            'section_ids.*' => ['required'],
        ]);

        $sectionIds = array_map(function ($id) {
            return $id === 'amenities' ? 'amenities' : (int) $id;
        }, $data['section_ids']);

        $this->generalFieldService->reorderSections($propertyType, $sectionIds);

        return redirect()->back()->with('success', 'Orden de secciones guardado.');
    }
}
