<?php

namespace Modules\Properties\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Properties\Models\PropertyField;
use Modules\Properties\Models\PropertyFieldSection;
use Modules\Properties\Models\PropertyType;
use Modules\Properties\Models\PropertyTypeOption;

class PropertyTypeSeeder extends Seeder
{
    public function run(): void
    {
        $houseType = PropertyType::updateOrCreate(
            ['key' => 'house'],
            [
                'name' => 'Casa',
                'slug' => 'casas',
                'description' => 'Propiedades tipo casa residencial',
                'icon' => 'bi bi-house',
                'is_active' => true,
                'is_public' => true,
                'sort_order' => 1,
            ]
        );

        if ($houseType->wasRecentlyCreated || $houseType->sections()->count() === 0) {
            $this->seedHouseFields($houseType);
        }
    }

    protected function seedHouseFields(PropertyType $houseType): void
    {
        $mainSection = PropertyFieldSection::updateOrCreate(
            [
                'property_type_id' => $houseType->id,
                'name' => 'Información principal',
            ],
            [
                'description' => 'Datos básicos de la propiedad',
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        $priceSection = PropertyFieldSection::updateOrCreate(
            [
                'property_type_id' => $houseType->id,
                'name' => 'Operación y precio',
            ],
            [
                'description' => 'Tipo de operación y precio de venta o renta',
                'sort_order' => 2,
                'is_active' => true,
            ]
        );

        PropertyField::updateOrCreate(
            [
                'property_type_id' => $houseType->id,
                'field_key' => 'title',
            ],
            [
                'section_id' => $mainSection->id,
                'label' => 'Título',
                'field_type' => 'text',
                'description' => 'Título de la propiedad',
                'help_text' => 'Ej: Casa en venta en Guadalajara',
                'placeholder' => 'Ej: Casa en venta en Guadalajara',
                'is_required' => true,
                'is_active' => true,
                'is_listable' => true,
                'is_public' => true,
                'is_filterable' => false,
                'is_searchable' => true,
                'sort_order' => 1,
                'validation_rules' => ['max' => 180],
            ]
        );

        PropertyField::updateOrCreate(
            [
                'property_type_id' => $houseType->id,
                'field_key' => 'description',
            ],
            [
                'section_id' => $mainSection->id,
                'label' => 'Descripción',
                'field_type' => 'textarea',
                'description' => 'Descripción detallada de la propiedad',
                'help_text' => 'Describe las características, amenities y detalles de la propiedad',
                'placeholder' => 'Casa amplia con 3 recámaras, jardín, cochera para 2 autos...',
                'is_required' => true,
                'is_active' => true,
                'is_listable' => false,
                'is_public' => true,
                'is_filterable' => false,
                'is_searchable' => false,
                'sort_order' => 2,
                'validation_rules' => ['max' => 5000],
            ]
        );

        PropertyField::updateOrCreate(
            [
                'property_type_id' => $houseType->id,
                'field_key' => 'main_image',
            ],
            [
                'section_id' => $mainSection->id,
                'label' => 'Imagen principal',
                'field_type' => 'image',
                'description' => 'Imagen principal de la propiedad',
                'help_text' => ' JPG, PNG o WebP. Máximo 5MB.',
                'placeholder' => '',
                'is_required' => false,
                'is_active' => true,
                'is_listable' => true,
                'is_public' => true,
                'is_filterable' => false,
                'is_searchable' => false,
                'sort_order' => 3,
            ]
        );

        $operationField = PropertyField::updateOrCreate(
            [
                'property_type_id' => $houseType->id,
                'field_key' => 'operation_type',
            ],
            [
                'section_id' => $priceSection->id,
                'label' => 'Tipo de operación',
                'field_type' => 'select',
                'description' => 'Tipo de operación de la propiedad',
                'help_text' => 'Selecciona el tipo de operación',
                'placeholder' => 'Selecciona una opción',
                'is_required' => true,
                'is_active' => true,
                'is_listable' => true,
                'is_public' => true,
                'is_filterable' => true,
                'is_searchable' => false,
                'sort_order' => 1,
            ]
        );

        foreach ([
            ['value' => 'sale', 'label' => 'Venta', 'sort_order' => 1],
            ['value' => 'rent', 'label' => 'Renta', 'sort_order' => 2],
            ['value' => 'transfer', 'label' => 'Traspaso', 'sort_order' => 3],
        ] as $index => $option) {
            PropertyTypeOption::updateOrCreate(
                [
                    'property_field_id' => $operationField->id,
                    'value' => $option['value'],
                ],
                [
                    'label' => $option['label'],
                    'sort_order' => $option['sort_order'],
                    'is_active' => true,
                ]
            );
        }

        PropertyField::updateOrCreate(
            [
                'property_type_id' => $houseType->id,
                'field_key' => 'price',
            ],
            [
                'section_id' => $priceSection->id,
                'label' => 'Precio',
                'field_type' => 'price',
                'description' => 'Precio de la propiedad',
                'help_text' => 'Cantidad sin formato (solo números)',
                'placeholder' => '2500000',
                'is_required' => true,
                'is_active' => true,
                'is_listable' => true,
                'is_public' => true,
                'is_filterable' => true,
                'is_searchable' => false,
                'sort_order' => 2,
                'validation_rules' => ['min' => 0],
            ]
        );

        $currencyField = PropertyField::updateOrCreate(
            [
                'property_type_id' => $houseType->id,
                'field_key' => 'currency',
            ],
            [
                'section_id' => $priceSection->id,
                'label' => 'Moneda',
                'field_type' => 'select',
                'description' => 'Moneda del precio',
                'help_text' => 'Selecciona la moneda',
                'placeholder' => 'Selecciona una opción',
                'is_required' => true,
                'is_active' => true,
                'is_listable' => true,
                'is_public' => true,
                'is_filterable' => true,
                'is_searchable' => false,
                'sort_order' => 3,
            ]
        );

        foreach ([
            ['value' => 'MXN', 'label' => 'Peso mexicano (MXN)', 'sort_order' => 1],
            ['value' => 'USD', 'label' => 'Dólar estadounidense (USD)', 'sort_order' => 2],
        ] as $option) {
            PropertyTypeOption::updateOrCreate(
                [
                    'property_field_id' => $currencyField->id,
                    'value' => $option['value'],
                ],
                [
                    'label' => $option['label'],
                    'sort_order' => $option['sort_order'],
                    'is_active' => true,
                ]
            );
        }

        $periodField = PropertyField::updateOrCreate(
            [
                'property_type_id' => $houseType->id,
                'field_key' => 'price_period',
            ],
            [
                'section_id' => $priceSection->id,
                'label' => 'Periodicidad',
                'field_type' => 'select',
                'description' => 'Periodicidad del precio (para rentas)',
                'help_text' => 'Opcional para venta o traspaso',
                'placeholder' => 'Selecciona una opción',
                'is_required' => false,
                'is_active' => true,
                'is_listable' => true,
                'is_public' => true,
                'is_filterable' => true,
                'is_searchable' => false,
                'sort_order' => 4,
            ]
        );

        foreach ([
            ['value' => 'single', 'label' => 'Precio único', 'sort_order' => 1],
            ['value' => 'monthly', 'label' => 'Mensual', 'sort_order' => 2],
            ['value' => 'weekly', 'label' => 'Semanal', 'sort_order' => 3],
            ['value' => 'daily', 'label' => 'Diario', 'sort_order' => 4],
        ] as $option) {
            PropertyTypeOption::updateOrCreate(
                [
                    'property_field_id' => $periodField->id,
                    'value' => $option['value'],
                ],
                [
                    'label' => $option['label'],
                    'sort_order' => $option['sort_order'],
                    'is_active' => true,
                ]
            );
        }
    }
}
