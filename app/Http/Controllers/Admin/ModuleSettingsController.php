<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ModuleDefinition;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ModuleSettingsController extends Controller
{
    public function show(Request $request, string $moduleKey)
    {
        $definition = ModuleDefinition::where('key', $moduleKey)->firstOrFail();

        if (!$definition->has_settings) {
            abort(404);
        }

        $settings = $definition->settings ?? $this->getDefaultSettings($moduleKey);
        $links = $this->getModuleLinks($moduleKey);

        return Inertia::render('Admin/ModuleSettings/Show', [
            'moduleKey' => $moduleKey,
            'moduleName' => $definition->name,
            'moduleIcon' => $definition->icon,
            'settings' => $settings,
            'schema' => $definition->settings_schema ?? $this->getSchemaForModule($moduleKey),
            'links' => $links,
        ]);
    }

    public function update(Request $request, string $moduleKey)
    {
        $definition = ModuleDefinition::where('key', $moduleKey)->firstOrFail();

        if (!$definition->has_settings) {
            abort(404);
        }

        $schema = $definition->settings_schema ?? $this->getSchemaForModule($moduleKey);

        $validated = $this->validateSettings($request, $schema);

        $definition->update(['settings' => $validated]);

        return redirect()->back()->with('success', 'Configuracion guardada correctamente.');
    }

    private function validateSettings(Request $request, array $schema): array
    {
        $rules = [];
        foreach ($schema as $field) {
            $fieldRules = [];
            if (isset($field['required']) && $field['required']) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            switch ($field['type'] ?? 'string') {
                case 'number':
                    $fieldRules[] = 'integer';
                    if (isset($field['min'])) {
                        $fieldRules[] = 'min:' . $field['min'];
                    }
                    if (isset($field['max'])) {
                        $fieldRules[] = 'max:' . $field['max'];
                    }
                    break;
                case 'boolean':
                    $fieldRules[] = 'boolean';
                    break;
                case 'array':
                    $fieldRules[] = 'array';
                    break;
                default:
                    $fieldRules[] = 'string';
                    if (isset($field['max'])) {
                        $fieldRules[] = 'max:' . $field['max'];
                    }
                    break;
            }

            $rules[$field['key']] = $fieldRules;
        }

        return $request->validate($rules);
    }

    private function getDefaultSettings(string $moduleKey): array
    {
        $defaults = [
            'features' => [
                'max_features' => 40,
                'allow_custom_features' => true,
                'require_category' => false,
            ],
            'gallery' => [
                'max_images' => 20,
                'max_image_size_mb' => 5,
                'allowed_formats' => ['jpg', 'jpeg', 'png', 'webp'],
                'enable_lightbox' => true,
            ],
            'services' => [
                'max_services' => 50,
                'allow_booking' => true,
                'require_location' => false,
            ],
            'products' => [
                'max_products' => 100,
                'enable_variants' => true,
                'require_stock' => false,
            ],
            'appointments' => [
                'max_daily_appointments' => 50,
                'booking_window_days' => 30,
                'require_confirmation' => true,
                'allow_cancellation' => true,
                'cancellation_hours' => 24,
            ],
            'leads' => [
                'max_leads' => 500,
                'email_notifications' => true,
                'require_phone' => false,
            ],
            'reviews' => [
                'require_approval' => true,
                'allow_replies' => false,
                'max_chars' => 500,
            ],
            'promotions' => [
                'max_promotions' => 20,
                'require_voucher' => false,
            ],
        ];

        return $defaults[$moduleKey] ?? [];
    }

    private function getSchemaForModule(string $moduleKey): array
    {
        $schemas = [
            'features' => [
                ['key' => 'max_features', 'label' => 'Maximo de caracteristicas', 'type' => 'number', 'min' => 1, 'max' => 100, 'default' => 40],
                ['key' => 'allow_custom_features', 'label' => 'Permitir caracteristicas personalizadas', 'type' => 'boolean', 'default' => true],
                ['key' => 'require_category', 'label' => 'Requerir categoria', 'type' => 'boolean', 'default' => false],
            ],
            'gallery' => [
                ['key' => 'max_images', 'label' => 'Maximo de imagenes', 'type' => 'number', 'min' => 1, 'max' => 100, 'default' => 20],
                ['key' => 'max_image_size_mb', 'label' => 'Tamano maximo por imagen (MB)', 'type' => 'number', 'min' => 1, 'max' => 20, 'default' => 5],
                ['key' => 'enable_lightbox', 'label' => 'Habilitar visor de imagenes', 'type' => 'boolean', 'default' => true],
            ],
            'services' => [
                ['key' => 'max_services', 'label' => 'Maximo de servicios', 'type' => 'number', 'min' => 1, 'max' => 200, 'default' => 50],
                ['key' => 'allow_booking', 'label' => 'Permitir reservas', 'type' => 'boolean', 'default' => true],
                ['key' => 'require_location', 'label' => 'Requerir ubicacion', 'type' => 'boolean', 'default' => false],
            ],
            'products' => [
                ['key' => 'max_products', 'label' => 'Maximo de productos', 'type' => 'number', 'min' => 1, 'max' => 500, 'default' => 100],
                ['key' => 'enable_variants', 'label' => 'Habilitar variantes', 'type' => 'boolean', 'default' => true],
                ['key' => 'require_stock', 'label' => 'Requerir control de stock', 'type' => 'boolean', 'default' => false],
            ],
            'appointments' => [
                ['key' => 'max_daily_appointments', 'label' => 'Maximo de citas por dia', 'type' => 'number', 'min' => 1, 'max' => 200, 'default' => 50],
                ['key' => 'booking_window_days', 'label' => 'Dias de anticipacion para reservas', 'type' => 'number', 'min' => 1, 'max' => 90, 'default' => 30],
                ['key' => 'require_confirmation', 'label' => 'Requerir confirmacion manual', 'type' => 'boolean', 'default' => true],
                ['key' => 'allow_cancellation', 'label' => 'Permitir cancelacion', 'type' => 'boolean', 'default' => true],
                ['key' => 'cancellation_hours', 'label' => 'Horas antes para cancelar', 'type' => 'number', 'min' => 0, 'max' => 72, 'default' => 24],
            ],
            'leads' => [
                ['key' => 'max_leads', 'label' => 'Maximo de leads', 'type' => 'number', 'min' => 10, 'max' => 10000, 'default' => 500],
                ['key' => 'email_notifications', 'label' => 'Notificaciones por email', 'type' => 'boolean', 'default' => true],
                ['key' => 'require_phone', 'label' => 'Requerir telefono', 'type' => 'boolean', 'default' => false],
            ],
            'reviews' => [
                ['key' => 'require_approval', 'label' => 'Requerir aprobacion antes de publicar', 'type' => 'boolean', 'default' => true],
                ['key' => 'allow_replies', 'label' => 'Permitir respuestas del negocio', 'type' => 'boolean', 'default' => false],
                ['key' => 'max_chars', 'label' => 'Caracteres maximos por review', 'type' => 'number', 'min' => 50, 'max' => 2000, 'default' => 500],
            ],
            'promotions' => [
                ['key' => 'max_promotions', 'label' => 'Maximo de promociones activas', 'type' => 'number', 'min' => 1, 'max' => 100, 'default' => 20],
                ['key' => 'require_voucher', 'label' => 'Requerir codigo de voucher', 'type' => 'boolean', 'default' => false],
            ],
            'properties' => [
                ['key' => 'max_properties', 'label' => 'Maximo de propiedades', 'type' => 'number', 'min' => 1, 'max' => 1000, 'default' => 100],
            ],
        ];

        return $schemas[$moduleKey] ?? [];
    }

    private function getModuleLinks(string $moduleKey): array
    {
        $links = [
            'properties' => [
                ['label' => 'Tipos de propiedad', 'icon' => 'bi bi-folder', 'href' => '/admin/modules/properties/types'],
                ['label' => 'Campos generales', 'icon' => 'bi bi-diagram-3', 'href' => '/admin/modules/properties/general-sections'],
            ],
        ];

        return $links[$moduleKey] ?? [];
    }
}
