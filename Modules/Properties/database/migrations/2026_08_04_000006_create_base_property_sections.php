<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Properties\Models\GeneralFieldSection;
use Modules\Properties\Models\GeneralField;
use Modules\Properties\Models\GeneralFieldOption;
use Modules\Properties\Models\PropertyType;
use App\Services\Properties\GeneralFieldService;

return new class extends Migration
{
    public function up(): void
    {
        $sectionsData = [
            'caracteristicas-generales' => [
                'name' => 'Características generales',
                'slug' => 'caracteristicas-generales',
                'icon' => 'bi bi-rulers',
                'description' => 'Datos generales de la propiedad',
                'sort_order' => 3,
                'fields' => [
                    ['field_key' => 'terrain_surface', 'field_type' => 'decimal', 'label' => 'Superficie de terreno', 'is_required' => false],
                    ['field_key' => 'built_surface', 'field_type' => 'decimal', 'label' => 'Superficie construida', 'is_required' => false],
                    ['field_key' => 'measurement_unit', 'field_type' => 'select', 'label' => 'Unidad de medida', 'is_required' => false, 'options' => [['value' => 'm2', 'label' => 'm²'], ['value' => 'ft2', 'label' => 'ft²']]],
                    ['field_key' => 'antiquity', 'field_type' => 'integer', 'label' => 'Antigüedad (años)', 'is_required' => false],
                    ['field_key' => 'construction_year', 'field_type' => 'integer', 'label' => 'Año de construcción', 'is_required' => false],
                ],
            ],
            'multimedia' => [
                'name' => 'Multimedia',
                'slug' => 'multimedia',
                'icon' => 'bi bi-play-circle',
                'description' => 'Fotos, videos y tours de la propiedad',
                'sort_order' => 4,
                'fields' => [
                    ['field_key' => 'gallery_images', 'field_type' => 'gallery', 'label' => 'Galería de imágenes', 'is_required' => false],
                    ['field_key' => 'video_youtube', 'field_type' => 'url', 'label' => 'Video YouTube', 'is_required' => false],
                    ['field_key' => 'virtual_tour', 'field_type' => 'url', 'label' => 'Tour Virtual', 'is_required' => false],
                    ['field_key' => 'floor_plan_pdf', 'field_type' => 'file', 'label' => 'Plano PDF', 'is_required' => false],
                ],
            ],
            'contacto' => [
                'name' => 'Contacto',
                'slug' => 'contacto',
                'icon' => 'bi bi-person-badge',
                'description' => 'Información de contacto del agente',
                'sort_order' => 5,
                'fields' => [
                    ['field_key' => 'agent_name', 'field_type' => 'text', 'label' => 'Agente responsable', 'is_required' => false],
                    ['field_key' => 'agent_phone', 'field_type' => 'phone', 'label' => 'Teléfono', 'is_required' => false],
                    ['field_key' => 'agent_email', 'field_type' => 'email', 'label' => 'Email', 'is_required' => false],
                    ['field_key' => 'agent_whatsapp', 'field_type' => 'phone', 'label' => 'WhatsApp', 'is_required' => false],
                ],
            ],
            'seo' => [
                'name' => 'SEO',
                'slug' => 'seo',
                'icon' => 'bi bi-search',
                'description' => 'Configuración de SEO para motores de búsqueda',
                'sort_order' => 6,
                'fields' => [
                    ['field_key' => 'meta_title', 'field_type' => 'text', 'label' => 'Meta Title', 'is_required' => false],
                    ['field_key' => 'meta_description', 'field_type' => 'textarea', 'label' => 'Meta Description', 'is_required' => false],
                    ['field_key' => 'custom_url', 'field_type' => 'text', 'label' => 'URL personalizada', 'is_required' => false],
                ],
            ],
            'extras' => [
                'name' => 'Extras',
                'slug' => 'extras',
                'icon' => 'bi bi-plus-circle',
                'description' => 'Campos adicionales y notas',
                'sort_order' => 7,
                'fields' => [
                    ['field_key' => 'tags', 'field_type' => 'text', 'label' => 'Etiquetas', 'is_required' => false],
                    ['field_key' => 'publish_date', 'field_type' => 'date', 'label' => 'Fecha de publicación', 'is_required' => false],
                    ['field_key' => 'available_date', 'field_type' => 'date', 'label' => 'Fecha disponible', 'is_required' => false],
                    ['field_key' => 'internal_id', 'field_type' => 'text', 'label' => 'ID interno', 'is_required' => false],
                    ['field_key' => 'private_notes', 'field_type' => 'textarea', 'label' => 'Notas privadas', 'is_required' => false],
                ],
            ],
        ];

        $generalFieldService = app(GeneralFieldService::class);
        $propertyTypes = PropertyType::all();

        foreach ($sectionsData as $sectionData) {
            $section = GeneralFieldSection::create([
                'name' => $sectionData['name'],
                'slug' => $sectionData['slug'],
                'icon' => $sectionData['icon'],
                'description' => $sectionData['description'],
                'sort_order' => $sectionData['sort_order'],
                'is_active' => true,
            ]);

            foreach ($sectionData['fields'] as $fieldIndex => $fieldData) {
                $field = GeneralField::create([
                    'general_field_section_id' => $section->id,
                    'field_key' => $fieldData['field_key'],
                    'field_type' => $fieldData['field_type'],
                    'label' => $fieldData['label'],
                    'is_required' => $fieldData['is_required'],
                    'is_active' => true,
                    'sort_order' => $fieldIndex,
                ]);

                if (isset($fieldData['options'])) {
                    foreach ($fieldData['options'] as $optionData) {
                        GeneralFieldOption::create([
                            'general_field_id' => $field->id,
                            'value' => $optionData['value'],
                            'label' => $optionData['label'],
                            'sort_order' => 0,
                            'is_active' => true,
                        ]);
                    }
                }
            }

            foreach ($propertyTypes as $propertyType) {
                $generalFieldService->assignSectionToPropertyType($propertyType, $section);
            }
        }
    }

    public function down(): void
    {
        $slugs = ['caracteristicas-generales', 'multimedia', 'contacto', 'seo', 'extras'];
        $sections = GeneralFieldSection::whereIn('slug', $slugs)->get();

        foreach ($sections as $section) {
            $section->fields()->delete();
            $section->delete();
        }
    }
};
