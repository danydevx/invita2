<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Properties\Models\GeneralFieldSection;
use Modules\Properties\Models\GeneralField;
use Modules\Properties\Models\PropertyType;
use App\Services\Properties\GeneralFieldService;

return new class extends Migration
{
    public function up(): void
    {
        $section = GeneralFieldSection::create([
            'name' => 'Características del Inmueble',
            'slug' => 'caracteristicas-inmueble',
            'icon' => 'bi bi-building',
            'description' => 'Características físicas del inmueble',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        $fields = [
            ['field_key' => 'bathrooms', 'field_type' => 'integer', 'label' => 'Baños', 'is_required' => false],
            ['field_key' => 'half_bathrooms', 'field_type' => 'integer', 'label' => 'Medio Baño', 'is_required' => false],
            ['field_key' => 'bedrooms', 'field_type' => 'integer', 'label' => 'Recámaras', 'is_required' => false],
            ['field_key' => 'levels', 'field_type' => 'integer', 'label' => 'Niveles', 'is_required' => false],
            ['field_key' => 'parking_spaces', 'field_type' => 'integer', 'label' => 'Estacionamientos', 'is_required' => false],
            ['field_key' => 'age', 'field_type' => 'integer', 'label' => 'Años de Antigüedad', 'is_required' => false],
        ];

        foreach ($fields as $index => $fieldData) {
            GeneralField::create([
                'general_field_section_id' => $section->id,
                'field_key' => $fieldData['field_key'],
                'field_type' => $fieldData['field_type'],
                'label' => $fieldData['label'],
                'is_required' => $fieldData['is_required'],
                'is_active' => true,
                'sort_order' => $index,
            ]);
        }

        $generalFieldService = app(GeneralFieldService::class);
        $propertyTypes = PropertyType::all();

        foreach ($propertyTypes as $propertyType) {
            $generalFieldService->assignSectionToPropertyType($propertyType, $section);
        }
    }

    public function down(): void
    {
        $section = GeneralFieldSection::where('slug', 'caracteristicas-inmueble')->first();
        if ($section) {
            $section->fields()->delete();
            $section->delete();
        }
    }
};
