<?php

namespace App\Services\Properties;

use Illuminate\Support\Str;
use Modules\Properties\Models\GeneralField;
use Modules\Properties\Models\GeneralFieldOption;
use Modules\Properties\Models\GeneralFieldSection;
use Modules\Properties\Models\GeneralFieldTypeAssignment;
use Modules\Properties\Models\PropertyField;
use Modules\Properties\Models\PropertyFieldSection;
use Modules\Properties\Models\PropertyType;

class GeneralFieldService
{
    public function getGeneralSections(): array
    {
        return GeneralFieldSection::active()
            ->with(['activeFields.activeFieldOptions'])
            ->orderBy('sort_order')
            ->get()
            ->toArray();
    }

    public function createSection(array $data): GeneralFieldSection
    {
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        if (!isset($data['sort_order'])) {
            $maxSort = GeneralFieldSection::max('sort_order') ?? -1;
            $data['sort_order'] = $maxSort + 1;
        }

        return GeneralFieldSection::create($data);
    }

    public function updateSection(GeneralFieldSection $section, array $data): GeneralFieldSection
    {
        if (isset($data['name']) && !isset($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $section->update($data);

        return $section;
    }

    public function deleteSection(GeneralFieldSection $section): void
    {
        $section->fields()->delete();
        $section->delete();
    }

    public function createField(GeneralFieldSection $section, array $data): GeneralField
    {
        $data['general_field_section_id'] = $section->id;
        $data['field_key'] = $data['field_key'] ?? Str::slug($data['label'], '_');

        if (!isset($data['is_active'])) {
            $data['is_active'] = true;
        }

        if (!isset($data['sort_order'])) {
            $maxSort = $section->fields()->max('sort_order') ?? -1;
            $data['sort_order'] = $maxSort + 1;
        }

        return GeneralField::create($data);
    }

    public function updateField(GeneralField $field, array $data): GeneralField
    {
        $field->update($data);

        return $field;
    }

    public function deleteField(GeneralField $field): void
    {
        $field->fieldOptions()->delete();
        $field->delete();
    }

    public function createFieldOption(GeneralField $field, array $data): GeneralFieldOption
    {
        $data['general_field_id'] = $field->id;

        return GeneralFieldOption::create($data);
    }

    public function updateFieldOption(GeneralFieldOption $option, array $data): GeneralFieldOption
    {
        $option->update($data);

        return $option;
    }

    public function deleteFieldOption(GeneralFieldOption $option): void
    {
        $option->delete();
    }

    public function assignSectionToPropertyType(PropertyType $propertyType, GeneralFieldSection $section, ?array $customSettings = null): GeneralFieldTypeAssignment
    {
        $assignment = GeneralFieldTypeAssignment::updateOrCreate(
            [
                'property_type_id' => $propertyType->id,
                'general_field_section_id' => $section->id,
            ],
            [
                'custom_settings' => $customSettings,
                'sort_order' => $section->sort_order,
            ]
        );

        $this->syncFieldsFromGeneralSection($propertyType, $section);

        return $assignment;
    }

    public function unassignSectionFromPropertyType(PropertyType $propertyType, GeneralFieldSection $section): void
    {
        $assignment = GeneralFieldTypeAssignment::where('property_type_id', $propertyType->id)
            ->where('general_field_section_id', $section->id)
            ->first();

        if ($assignment) {
            $this->removeInheritedFields($propertyType, $section);
            $assignment->delete();
        }
    }

    public function syncFieldsFromGeneralSection(PropertyType $propertyType, GeneralFieldSection $section): void
    {
        $existingSection = PropertyFieldSection::where('property_type_id', $propertyType->id)
            ->where('general_field_section_id', $section->id)
            ->first();

        if (!$existingSection) {
            $existingSection = PropertyFieldSection::create([
                'property_type_id' => $propertyType->id,
                'name' => $section->name,
                'description' => $section->description,
                'is_general' => true,
                'general_field_section_id' => $section->id,
                'sort_order' => $section->sort_order,
                'is_active' => true,
            ]);
        } elseif ($existingSection->sort_order !== $section->sort_order) {
            $existingSection->update(['sort_order' => $section->sort_order]);
        }

        $generalFields = $section->activeFields;

        foreach ($generalFields as $generalField) {
            $existingField = PropertyField::where('property_type_id', $propertyType->id)
                ->where('general_field_id', $generalField->id)
                ->first();

            $fieldData = [
                'section_id' => $existingSection->id,
                'general_field_id' => $generalField->id,
                'field_key' => $generalField->field_key,
                'field_type' => $generalField->field_type,
                'label' => $generalField->label,
                'description' => $generalField->description,
                'help_text' => $generalField->help_text,
                'placeholder' => $generalField->placeholder,
                'default_value' => $generalField->default_value,
                'options' => $generalField->options,
                'validation_rules' => $generalField->validation_rules,
                'is_required' => $generalField->is_required,
                'is_active' => $generalField->is_active,
                'is_inherited' => true,
                'sort_order' => $generalField->sort_order,
            ];

            if ($existingField) {
                $existingField->update($fieldData);
                $this->syncFieldOptions($generalField, $existingField);
            } else {
                $fieldData['property_type_id'] = $propertyType->id;
                $newField = PropertyField::create($fieldData);
                $this->syncFieldOptions($generalField, $newField);
            }
        }
    }

    protected function syncFieldOptions(GeneralField $generalField, PropertyField $propertyField): void
    {
        $generalOptions = $generalField->activeFieldOptions;

        $propertyField->fieldOptions()->delete();

        foreach ($generalOptions as $generalOption) {
            $propertyField->fieldOptions()->create([
                'value' => $generalOption->value,
                'label' => $generalOption->label,
                'sort_order' => $generalOption->sort_order,
                'is_active' => $generalOption->is_active,
            ]);
        }
    }

    public function removeInheritedFields(PropertyType $propertyType, GeneralFieldSection $section): void
    {
        // Get the general field IDs that belong to this section
        $generalFieldIds = $section->activeFields->pluck('id');

        $fieldIds = PropertyField::where('property_type_id', $propertyType->id)
            ->whereIn('general_field_id', $generalFieldIds)
            ->pluck('id');

        PropertyField::whereIn('id', $fieldIds)->delete();

        PropertyFieldSection::where('property_type_id', $propertyType->id)
            ->where('general_field_section_id', $section->id)
            ->delete();
    }

    public function getAssignmentCustomizations(PropertyType $propertyType, GeneralFieldSection $section): ?array
    {
        $assignment = GeneralFieldTypeAssignment::where('property_type_id', $propertyType->id)
            ->where('general_field_section_id', $section->id)
            ->first();

        return $assignment?->custom_settings;
    }

    public function updateAssignmentCustomizations(PropertyType $propertyType, GeneralFieldSection $section, ?array $customSettings): GeneralFieldTypeAssignment
    {
        $assignment = GeneralFieldTypeAssignment::updateOrCreate(
            [
                'property_type_id' => $propertyType->id,
                'general_field_section_id' => $section->id,
            ],
            [
                'custom_settings' => $customSettings,
            ]
        );

        return $assignment;
    }

    public function reorderSections(PropertyType $propertyType, array $sectionIds): void
    {
        $amenityIndex = array_search('amenities', $sectionIds);
        if ($amenityIndex !== false) {
            $settings = $propertyType->settings ?? [];
            $settings['amenity_section_sort_order'] = $amenityIndex;
            $propertyType->update(['settings' => $settings]);
        }

        foreach ($sectionIds as $index => $sectionId) {
            if ($sectionId === 'amenities') {
                continue;
            }

            $sectionId = (int) $sectionId;
            if ($sectionId <= 0) {
                continue;
            }

            // First try to find by PropertyFieldSection.id
            $propertyFieldSection = PropertyFieldSection::with('generalFieldSection')
                ->where('property_type_id', $propertyType->id)
                ->where('id', $sectionId)
                ->first();

            // If not found, try to find by general_field_section_id
            if (!$propertyFieldSection) {
                $propertyFieldSection = PropertyFieldSection::with('generalFieldSection')
                    ->where('property_type_id', $propertyType->id)
                    ->where('general_field_section_id', $sectionId)
                    ->first();
            }

            if ($propertyFieldSection) {
                if ($propertyFieldSection->is_locked) {
                    continue;
                }

                $propertyFieldSection->update(['sort_order' => $index]);

                if ($propertyFieldSection->is_general && $propertyFieldSection->general_field_section_id) {
                    GeneralFieldTypeAssignment::where('property_type_id', $propertyType->id)
                        ->where('general_field_section_id', $propertyFieldSection->general_field_section_id)
                        ->update(['sort_order' => $index]);
                }
                continue;
            }

            $assignment = GeneralFieldTypeAssignment::where('property_type_id', $propertyType->id)
                ->where('general_field_section_id', $sectionId)
                ->first();

            if ($assignment) {
                $generalSection = GeneralFieldSection::find($sectionId);
                if ($generalSection && $generalSection->is_locked) {
                    continue;
                }

                $assignment->update(['sort_order' => $index]);

                PropertyFieldSection::where('property_type_id', $propertyType->id)
                    ->where('general_field_section_id', $sectionId)
                    ->update(['sort_order' => $index]);
            }
        }
    }
}
