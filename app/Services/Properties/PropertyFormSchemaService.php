<?php

namespace App\Services\Properties;

use Modules\Properties\Models\PropertyType;
use Modules\Properties\Models\PropertyField;
use Modules\Properties\Models\GeneralFieldTypeAssignment;

class PropertyFormSchemaService
{
    public function getFormSchema(PropertyType $propertyType, ?int $excludeFieldId = null): array
    {
        $lockedSectionsData = [];
        $regularSections = [];

        foreach ($propertyType->activeSections as $section) {
            $fields = $section->activeFields;

            if ($excludeFieldId) {
                $fields = $fields->where('id', '!=', $excludeFieldId);
            }

            $customizations = $this->getSectionCustomizations($propertyType, $section);

            $sectionFields = [];
            foreach ($fields as $field) {
                $fieldCustomizations = $this->getFieldCustomizations($field, $customizations);
                $sectionFields[] = $this->formatField($field, $fieldCustomizations);
            }

            if (empty($sectionFields)) {
                continue;
            }

            $sectionData = [
                'id' => $section->id,
                'name' => $section->name,
                'description' => $section->description,
                'sort_order' => $section->sort_order,
                'is_general' => $section->is_general,
                'is_locked' => $section->is_locked,
                'general_field_section_id' => $section->general_field_section_id,
                'general_field_section_slug' => $section->generalFieldSection?->slug,
                'fields' => $sectionFields,
            ];

            if ($section->is_locked) {
                $lockedSectionsData[] = $sectionData;
            } else {
                $regularSections[] = $sectionData;
            }
        }

        $sections = [];

        foreach ($lockedSectionsData as $section) {
            $section['sort_order'] = count($sections) + 1;
            $sections[] = $section;
        }

        foreach ($regularSections as $section) {
            $section['sort_order'] = count($sections) + 1;
            $sections[] = $section;
        }

        $standaloneFields = $propertyType->activeFields()
            ->whereNull('section_id')
            ->when($excludeFieldId, fn($q) => $q->where('id', '!=', $excludeFieldId))
            ->get();

        if ($standaloneFields->isNotEmpty()) {
            $sections[] = [
                'id' => null,
                'name' => 'Información general',
                'description' => null,
                'sort_order' => count($sections) + 1,
                'is_general' => false,
                'is_locked' => false,
                'general_field_section_id' => null,
                'fields' => $standaloneFields->map(fn($f) => $this->formatField($f))->toArray(),
            ];
        }

        usort($sections, fn($a, $b) => $a['sort_order'] <=> $b['sort_order']);

        return [
            'property_type' => [
                'id' => $propertyType->id,
                'name' => $propertyType->name,
                'key' => $propertyType->key,
            ],
            'sections' => $sections,
        ];
    }

    protected function getSectionCustomizations(PropertyType $propertyType, $section): ?array
    {
        if (!$section->general_field_section_id) {
            return null;
        }

        $assignment = GeneralFieldTypeAssignment::where('property_type_id', $propertyType->id)
            ->where('general_field_section_id', $section->general_field_section_id)
            ->first();

        return $assignment?->custom_settings;
    }

    protected function getFieldCustomizations(PropertyField $field, ?array $sectionCustomizations): ?array
    {
        if (!$field->general_field_id || !$sectionCustomizations) {
            return null;
        }

        return $sectionCustomizations['fields'][$field->general_field_id] ?? null;
    }

    public function formatField(PropertyField $field, ?array $customizations = null): array
    {
        $label = $field->label;
        $helpText = $field->help_text;
        $placeholder = $field->placeholder;
        $isRequired = $field->is_required;

        if ($customizations) {
            $label = $customizations['label'] ?? $label;
            $helpText = $customizations['help_text'] ?? $helpText;
            $placeholder = $customizations['placeholder'] ?? $placeholder;
            $isRequired = $customizations['is_required'] ?? $isRequired;
        }

        $data = [
            'id' => $field->id,
            'field_key' => $field->field_key,
            'field_type' => $field->field_type,
            'label' => $label,
            'description' => $field->description,
            'help_text' => $helpText,
            'placeholder' => $placeholder,
            'default_value' => $field->default_value,
            'is_required' => $isRequired,
            'is_active' => $field->is_active,
            'sort_order' => $field->sort_order,
            'is_inherited' => $field->is_inherited,
            'general_field_id' => $field->general_field_id,
            'options' => null,
        ];

        if (in_array($field->field_type, ['select', 'multiselect', 'radio', 'checkbox'])) {
            $data['options'] = $field->activeFieldOptions->map(fn($opt) => [
                'value' => $opt->value,
                'label' => $opt->label,
            ])->toArray();
        }

        return $data;
    }

    public function getPublicSchema(PropertyType $propertyType): array
    {
        $sections = [];

        foreach ($propertyType->activeSections as $section) {
            $fields = $section->activeFields()->where('is_public', true)->get();

            if ($fields->isEmpty()) {
                continue;
            }

            $sections[] = [
                'name' => $section->name,
                'fields' => $fields->map(fn($f) => $this->formatField($f))->toArray(),
            ];
        }

        return [
            'property_type' => [
                'key' => $propertyType->key,
                'name' => $propertyType->name,
                'icon' => $propertyType->icon,
            ],
            'sections' => $sections,
        ];
    }

    public function getFilterableFields(PropertyType $propertyType): array
    {
        return $propertyType->activeFields()
            ->where('is_filterable', true)
            ->get()
            ->map(fn($f) => $this->formatField($f))
            ->toArray();
    }
}
