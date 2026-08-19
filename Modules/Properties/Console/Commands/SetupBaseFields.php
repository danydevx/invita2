<?php

namespace Modules\Properties\Console\Commands;

use Illuminate\Console\Command;
use Modules\Properties\Models\GeneralField;
use Modules\Properties\Models\GeneralFieldOption;
use Modules\Properties\Models\GeneralFieldSection;
use Modules\Properties\Models\PropertyType;

class SetupBaseFields extends Command
{
    protected $signature = 'properties:setup-base-fields';

    protected $description = 'Crea las secciones y campos generales base y los asigna a todos los tipos de propiedad';

    public function handle(): int
    {
        $this->info('Creando secciones y campos generales base...');

        $mainSection = GeneralFieldSection::updateOrCreate(
            ['slug' => 'informacion-principal'],
            [
                'name' => 'Información principal',
                'icon' => 'bi bi-info-circle',
                'description' => 'Datos básicos de la propiedad',
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        $priceSection = GeneralFieldSection::updateOrCreate(
            ['slug' => 'operacion-y-precio'],
            [
                'name' => 'Operación y precio',
                'icon' => 'bi bi-currency-dollar',
                'description' => 'Tipo de operación y precio de venta o renta',
                'sort_order' => 2,
                'is_active' => true,
            ]
        );

        $this->createMainFields($mainSection);
        $this->createPriceFields($priceSection);

        $this->info('Asignando secciones a tipos de propiedad existentes...');

        $types = PropertyType::all();
        foreach ($types as $type) {
            $this->assignSectionsToType($type, $mainSection, $priceSection);
            $this->line("  - Asignado a: {$type->name}");
        }

        $this->info('¡Completado! Se han creado las secciones base y asignado a todos los tipos.');

        return Command::SUCCESS;
    }

    protected function createMainFields(GeneralFieldSection $section): void
    {
        $fields = [
            [
                'field_key' => 'title',
                'field_type' => 'text',
                'label' => 'Título',
                'description' => 'Título de la propiedad',
                'help_text' => 'Ej: Casa en venta en Guadalajara',
                'placeholder' => 'Ej: Casa en venta en Guadalajara',
                'is_required' => true,
                'sort_order' => 1,
            ],
            [
                'field_key' => 'description',
                'field_type' => 'textarea',
                'label' => 'Descripción',
                'description' => 'Descripción detallada de la propiedad',
                'help_text' => 'Describe las características, amenities y detalles de la propiedad',
                'placeholder' => 'Casa amplia con 3 recámaras, jardín, cochera para 2 autos...',
                'is_required' => true,
                'sort_order' => 2,
            ],
            [
                'field_key' => 'main_image',
                'field_type' => 'image',
                'label' => 'Imagen principal',
                'description' => 'Imagen principal de la propiedad',
                'help_text' => ' JPG, PNG o WebP. Máximo 5MB.',
                'placeholder' => '',
                'is_required' => false,
                'sort_order' => 3,
            ],
        ];

        foreach ($fields as $fieldData) {
            GeneralField::updateOrCreate(
                [
                    'general_field_section_id' => $section->id,
                    'field_key' => $fieldData['field_key'],
                ],
                array_merge($fieldData, [
                    'is_active' => true,
                ])
            );
        }
    }

    protected function createPriceFields(GeneralFieldSection $section): void
    {
        $operationField = GeneralField::updateOrCreate(
            [
                'general_field_section_id' => $section->id,
                'field_key' => 'operation_type',
            ],
            [
                'field_type' => 'select',
                'label' => 'Tipo de operación',
                'description' => 'Tipo de operación de la propiedad',
                'help_text' => 'Selecciona el tipo de operación',
                'placeholder' => 'Selecciona una opción',
                'is_required' => true,
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        $options = [
            ['value' => 'sale', 'label' => 'Venta', 'sort_order' => 1],
            ['value' => 'rent', 'label' => 'Renta', 'sort_order' => 2],
            ['value' => 'transfer', 'label' => 'Traspaso', 'sort_order' => 3],
        ];

        foreach ($options as $opt) {
            GeneralFieldOption::updateOrCreate(
                [
                    'general_field_id' => $operationField->id,
                    'value' => $opt['value'],
                ],
                [
                    'label' => $opt['label'],
                    'sort_order' => $opt['sort_order'],
                    'is_active' => true,
                ]
            );
        }

        GeneralField::updateOrCreate(
            [
                'general_field_section_id' => $section->id,
                'field_key' => 'price',
            ],
            [
                'field_type' => 'price',
                'label' => 'Precio',
                'description' => 'Precio de la propiedad',
                'help_text' => 'Cantidad sin formato (solo números)',
                'placeholder' => '2500000',
                'is_required' => true,
                'sort_order' => 2,
                'is_active' => true,
            ]
        );

        $currencyField = GeneralField::updateOrCreate(
            [
                'general_field_section_id' => $section->id,
                'field_key' => 'currency',
            ],
            [
                'field_type' => 'select',
                'label' => 'Moneda',
                'description' => 'Moneda del precio',
                'help_text' => 'Selecciona la moneda',
                'placeholder' => 'Selecciona una opción',
                'is_required' => true,
                'sort_order' => 3,
                'is_active' => true,
            ]
        );

        $currencyOptions = [
            ['value' => 'MXN', 'label' => 'Peso mexicano (MXN)', 'sort_order' => 1],
            ['value' => 'USD', 'label' => 'Dólar estadounidense (USD)', 'sort_order' => 2],
        ];

        foreach ($currencyOptions as $opt) {
            GeneralFieldOption::updateOrCreate(
                [
                    'general_field_id' => $currencyField->id,
                    'value' => $opt['value'],
                ],
                [
                    'label' => $opt['label'],
                    'sort_order' => $opt['sort_order'],
                    'is_active' => true,
                ]
            );
        }

        $periodField = GeneralField::updateOrCreate(
            [
                'general_field_section_id' => $section->id,
                'field_key' => 'price_period',
            ],
            [
                'field_type' => 'select',
                'label' => 'Periodicidad',
                'description' => 'Periodicidad del precio (para rentas)',
                'help_text' => 'Opcional para venta o traspaso',
                'placeholder' => 'Selecciona una opción',
                'is_required' => false,
                'sort_order' => 4,
                'is_active' => true,
            ]
        );

        $periodOptions = [
            ['value' => 'single', 'label' => 'Precio único', 'sort_order' => 1],
            ['value' => 'monthly', 'label' => 'Mensual', 'sort_order' => 2],
            ['value' => 'weekly', 'label' => 'Semanal', 'sort_order' => 3],
            ['value' => 'daily', 'label' => 'Diario', 'sort_order' => 4],
        ];

        foreach ($periodOptions as $opt) {
            GeneralFieldOption::updateOrCreate(
                [
                    'general_field_id' => $periodField->id,
                    'value' => $opt['value'],
                ],
                [
                    'label' => $opt['label'],
                    'sort_order' => $opt['sort_order'],
                    'is_active' => true,
                ]
            );
        }
    }

    protected function assignSectionsToType(PropertyType $type, GeneralFieldSection $mainSection, GeneralFieldSection $priceSection): void
    {
        $generalFieldService = app(\App\Services\Properties\GeneralFieldService::class);

        if (!$type->generalFieldSections()->where('general_field_section_id', $mainSection->id)->exists()) {
            $generalFieldService->assignSectionToPropertyType($type, $mainSection);
        }

        if (!$type->generalFieldSections()->where('general_field_section_id', $priceSection->id)->exists()) {
            $generalFieldService->assignSectionToPropertyType($type, $priceSection);
        }
    }
}
