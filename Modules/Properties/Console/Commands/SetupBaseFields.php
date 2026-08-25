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
                'is_locked' => true,
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
                'is_locked' => true,
            ]
        );

        $locationSection = GeneralFieldSection::updateOrCreate(
            ['slug' => 'ubicacion'],
            [
                'name' => 'Ubicación',
                'icon' => 'bi bi-geo-alt',
                'description' => 'Dirección y ubicación de la propiedad',
                'sort_order' => 3,
                'is_active' => true,
                'is_locked' => true,
            ]
        );

        $this->createMainFields($mainSection);
        $this->createPriceFields($priceSection);
        $this->createLocationFields($locationSection);

        $this->info('Asignando secciones a tipos de propiedad existentes...');

        $types = PropertyType::all();
        foreach ($types as $type) {
            $this->assignSectionsToType($type, $mainSection, $priceSection, $locationSection);
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

    protected function createLocationFields(GeneralFieldSection $section): void
    {
        $fields = [
            [
                'field_key' => 'country',
                'field_type' => 'text',
                'label' => 'País',
                'description' => 'País de la propiedad',
                'help_text' => '',
                'placeholder' => 'México',
                'is_required' => false,
                'sort_order' => 1,
            ],
            [
                'field_key' => 'state',
                'field_type' => 'text',
                'label' => 'Estado',
                'description' => 'Estado o entidad federativa',
                'help_text' => '',
                'placeholder' => 'Jalisco',
                'is_required' => false,
                'sort_order' => 2,
            ],
            [
                'field_key' => 'state_code',
                'field_type' => 'text',
                'label' => 'Código de estado',
                'description' => 'Código ISO del estado',
                'help_text' => '',
                'placeholder' => 'JAL',
                'is_required' => false,
                'sort_order' => 3,
            ],
            [
                'field_key' => 'city',
                'field_type' => 'text',
                'label' => 'Ciudad',
                'description' => 'Ciudad o municipio',
                'help_text' => '',
                'placeholder' => 'Guadalajara',
                'is_required' => false,
                'sort_order' => 4,
            ],
            [
                'field_key' => 'municipality',
                'field_type' => 'text',
                'label' => 'Delegación/Municipio',
                'description' => 'Delegación o municipio',
                'help_text' => '',
                'placeholder' => 'Zapopan',
                'is_required' => false,
                'sort_order' => 5,
            ],
            [
                'field_key' => 'colony',
                'field_type' => 'text',
                'label' => 'Colonia',
                'description' => 'Colonia o fraccionamiento',
                'help_text' => '',
                'placeholder' => 'Chapalita',
                'is_required' => false,
                'sort_order' => 6,
            ],
            [
                'field_key' => 'postal_code',
                'field_type' => 'text',
                'label' => 'Código postal',
                'description' => 'Código postal',
                'help_text' => '',
                'placeholder' => '45000',
                'is_required' => false,
                'sort_order' => 7,
            ],
            [
                'field_key' => 'street',
                'field_type' => 'text',
                'label' => 'Calle',
                'description' => 'Nombre de la calle',
                'help_text' => '',
                'placeholder' => 'Av. Vallarta',
                'is_required' => false,
                'sort_order' => 8,
            ],
            [
                'field_key' => 'exterior_number',
                'field_type' => 'text',
                'label' => 'Número exterior',
                'description' => 'Número exterior',
                'help_text' => '',
                'placeholder' => '1234',
                'is_required' => false,
                'sort_order' => 9,
            ],
            [
                'field_key' => 'interior_number',
                'field_type' => 'text',
                'label' => 'Número interior',
                'description' => 'Número interior (opcional)',
                'help_text' => '',
                'placeholder' => 'A',
                'is_required' => false,
                'sort_order' => 10,
            ],
            [
                'field_key' => 'references',
                'field_type' => 'textarea',
                'label' => 'Referencias',
                'description' => 'Referencias de ubicación',
                'help_text' => 'Entre calles, puntos de referencia',
                'placeholder' => 'Entre Av. México y Av. EUA',
                'is_required' => false,
                'sort_order' => 11,
            ],
            [
                'field_key' => 'latitude',
                'field_type' => 'text',
                'label' => 'Latitud',
                'description' => 'Coordenada de latitud',
                'help_text' => '',
                'placeholder' => '20.659698',
                'is_required' => false,
                'sort_order' => 12,
            ],
            [
                'field_key' => 'longitude',
                'field_type' => 'text',
                'label' => 'Longitud',
                'description' => 'Coordenada de longitud',
                'help_text' => '',
                'placeholder' => '-103.349609',
                'is_required' => false,
                'sort_order' => 13,
            ],
            [
                'field_key' => 'show_exact_location',
                'field_type' => 'boolean',
                'label' => 'Mostrar ubicación exacta',
                'description' => 'Si está activado, se mostrará la ubicación exacta en el mapa público',
                'help_text' => '',
                'placeholder' => '',
                'is_required' => false,
                'sort_order' => 14,
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

    protected function assignSectionsToType(PropertyType $type, GeneralFieldSection $mainSection, GeneralFieldSection $priceSection, GeneralFieldSection $locationSection): void
    {
        $generalFieldService = app(\App\Services\Properties\GeneralFieldService::class);

        if (!$type->generalFieldSections()->where('general_field_section_id', $mainSection->id)->exists()) {
            $generalFieldService->assignSectionToPropertyType($type, $mainSection);
        }

        if (!$type->generalFieldSections()->where('general_field_section_id', $priceSection->id)->exists()) {
            $generalFieldService->assignSectionToPropertyType($type, $priceSection);
        }

        if (!$type->generalFieldSections()->where('general_field_section_id', $locationSection->id)->exists()) {
            $generalFieldService->assignSectionToPropertyType($type, $locationSection);
        }
    }
}
