<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Properties\Models\GeneralField;
use Modules\Properties\Models\GeneralFieldSection;
use Modules\Properties\Models\GeneralFieldTypeAssignment;
use Modules\Properties\Models\PropertyField;

return new class extends Migration
{
    public function up(): void
    {
        $locationSectionId = 3;

        GeneralFieldTypeAssignment::where('general_field_section_id', $locationSectionId)->delete();

        $fieldIds = GeneralField::where('general_field_section_id', $locationSectionId)->pluck('id')->toArray();

        if (! empty($fieldIds)) {
            PropertyField::whereIn('general_field_id', $fieldIds)->delete();
        }

        GeneralField::where('general_field_section_id', $locationSectionId)->delete();

        GeneralFieldSection::where('id', $locationSectionId)->delete();
    }

    public function down(): void
    {
        $section = GeneralFieldSection::create([
            'name' => 'Ubicación',
            'slug' => 'ubicacion',
            'icon' => 'bi bi-geo-alt',
            'description' => 'Datos de ubicación de la propiedad',
            'sort_order' => 3,
            'is_active' => true,
        ]);

        $fields = [
            ['field_key' => 'country', 'field_type' => 'select', 'label' => 'País', 'is_required' => false],
            ['field_key' => 'state', 'field_type' => 'select', 'label' => 'Estado', 'is_required' => false],
            ['field_key' => 'city', 'field_type' => 'text', 'label' => 'Ciudad', 'is_required' => false],
            ['field_key' => 'municipality', 'field_type' => 'select', 'label' => 'Municipio', 'is_required' => false],
            ['field_key' => 'colony', 'field_type' => 'text', 'label' => 'Colonia', 'is_required' => false],
            ['field_key' => 'postal_code', 'field_type' => 'text', 'label' => 'Código Postal', 'is_required' => false],
            ['field_key' => 'street', 'field_type' => 'text', 'label' => 'Calle', 'is_required' => false],
            ['field_key' => 'exterior_number', 'field_type' => 'text', 'label' => 'Número Exterior', 'is_required' => false],
            ['field_key' => 'interior_number', 'field_type' => 'text', 'label' => 'Número Interior', 'is_required' => false],
            ['field_key' => 'references', 'field_type' => 'textarea', 'label' => 'Referencias', 'is_required' => false],
            ['field_key' => 'latitude', 'field_type' => 'decimal', 'label' => 'Latitud', 'is_required' => false],
            ['field_key' => 'longitude', 'field_type' => 'decimal', 'label' => 'Longitud', 'is_required' => false],
            ['field_key' => 'show_exact_location', 'field_type' => 'boolean', 'label' => 'Mostrar ubicación exacta', 'is_required' => false],
        ];

        foreach ($fields as $index => $fieldData) {
            GeneralField::create(array_merge($fieldData, [
                'general_field_section_id' => $section->id,
                'sort_order' => $index,
                'is_active' => true,
            ]));
        }
    }
};
