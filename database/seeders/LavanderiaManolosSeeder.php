<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\Businesses\Models\Business;
use Modules\Businesses\Enums\BusinessType;
use Modules\Services\Models\BusinessService;
use Modules\Products\Models\BusinessProduct;
use Modules\Gallery\Models\BusinessGalleryImage;

class LavanderiaManolosSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::find(12);
        if (!$user) {
            $this->command->error('User ID 12 (Juan Perez) not found.');
            return;
        }

        $planBusiness = Plan::where('slug', 'business')->first();
        if (!$planBusiness) {
            $this->command->error("Plan 'business' not found. Run PlanBusinessModuleSeeder first.");
            return;
        }

        $subscription = \App\Models\Subscription::updateOrCreate(
            ['user_id' => $user->id],
            [
                'plan_id' => $planBusiness->id,
                'status' => 'active',
                'starts_at' => now(),
                'ends_at' => now()->addYear(),
            ]
        );

        $business = Business::updateOrCreate(
            ['slug' => 'lavanderia-manolos'],
            [
                'user_id' => $user->id,
                'name' => 'Lavanderia Manolos',
                'business_type' => BusinessType::GENERIC,
                'description' => 'Lavanderia tradicional con mas de 20 anos de experiencia. Servicios de lavado, planchado, tintoreria y Lavado seco.',
                'phone' => '+54 11 4567 8901',
                'email' => 'contacto@lavanderiamanolos.com',
                'website' => 'https://lavanderiamanolos.com',
                'timezone' => 'America/Argentina/Buenos_Aires',
                'currency' => 'ARS',
                'is_active' => true,
                'is_published' => true,
            ]
        );

        $business->syncModulesFromPlan();

        $business->modules()->where('module_key', 'locations')->update(['is_enabled' => false]);

        $services = [
            ['name' => 'Lavado Express', 'slug' => 'lavado-express', 'description' => 'Lavado rapido de ropa comun en 2 horas. Incluye centrifugado.', 'duration' => 30, 'price' => 450],
            ['name' => 'Lavado Normal', 'slug' => 'lavado-normal', 'description' => 'Lavado completo con detergente premium y suavizante.', 'duration' => 60, 'price' => 350],
            ['name' => 'Lavado y Planchado', 'slug' => 'lavado-y-plancha', 'description' => 'Lavado mas planchado profesional de prendas.', 'duration' => 120, 'price' => 800],
            ['name' => 'Planchado only', 'slug' => 'plancha-only', 'description' => 'Solo planchado de prendas traidas de casa.', 'duration' => 45, 'price' => 500],
            ['name' => 'Tintoreria', 'slug' => 'tintoreria', 'description' => 'Tintado de telas y prendas con colorantes profesionales.', 'duration' => 90, 'price' => 1500],
            ['name' => 'Lavado Seco', 'slug' => 'lavado-seco', 'description' => 'Limpieza en seco para trajes y prendas delicadas.', 'duration' => 60, 'price' => 1200],
            ['name' => 'Lavado de Sabanas', 'slug' => 'lavado-sabanas', 'description' => 'Servicio especializado para sabanas, fundas y acolchados.', 'duration' => 90, 'price' => 950],
            ['name' => 'Lavado de Cortinas', 'slug' => 'lavado-cortinas', 'description' => 'Lavado y secado de cortinas de todos los tamanos.', 'duration' => 120, 'price' => 1800],
            ['name' => 'Tratamiento Anti Manchas', 'slug' => 'anti-manchas', 'description' => 'Tratamiento especializado para remover manchas dificiles.', 'duration' => 45, 'price' => 700],
            ['name' => 'Plancha a Vapor', 'slug' => 'plancha-vapor', 'description' => 'Planchado con vapor profesional para resultados impecables.', 'duration' => 60, 'price' => 600],
        ];

        foreach ($services as $i => $service) {
            BusinessService::updateOrCreate(
                ['business_id' => $business->id, 'slug' => $service['slug']],
                [
                    'name' => $service['name'],
                    'description' => $service['description'],
                    'duration_minutes' => $service['duration'],
                    'price' => $service['price'],
                    'allows_online_booking' => true,
                    'is_active' => true,
                    'sort_order' => $i + 1,
                ]
            );
        }

        $products = [
            ['name' => 'Detergente Premium 1L', 'slug' => 'detergente-premium-1l', 'description' => 'Detergente de alta concentracion para lavado a mano o maquina.', 'price' => 850, 'sku' => 'DET-001'],
            ['name' => 'Suavizante Premium 1L', 'slug' => 'suavizante-premium-1l', 'description' => 'Suavizante concentrado con fragancia duradera.', 'price' => 750, 'sku' => 'SUA-001'],
            ['name' => 'Anti Manchas Spray', 'slug' => 'anti-manchas-spray', 'description' => 'Quitamanchas en spray para manchas recientes.', 'price' => 450, 'sku' => 'QMT-001'],
            ['name' => 'Bolsa de Lavado 10u', 'slug' => 'bolsa-lavado-10u', 'description' => 'Bolsas de malla para ropa delicada, pack de 10 unidades.', 'price' => 1200, 'sku' => 'BOL-010'],
            ['name' => 'Perchas Premium 20u', 'slug' => 'perchas-premium-20u', 'description' => 'Perchas antideslizantes de plastico resistente, pack 20.', 'price' => 900, 'sku' => 'PER-020'],
            ['name' => 'Fundas de Ropa 5u', 'slug' => 'fundas-ropa-5u', 'description' => 'Fundas de tela para proteger prendas delicadas, pack 5.', 'price' => 1500, 'sku' => 'FUN-005'],
            ['name' => 'Esponja Anti Bacteria', 'slug' => 'esponja-anti-bacteria', 'description' => 'Esponja con tratamiento antibacteriano para lavado.', 'price' => 350, 'sku' => 'ESP-001'],
            ['name' => 'Cepillo para Ropa', 'slug' => 'cepillo-ropa', 'description' => 'Cepillo de cerdas suaves para prendas delicadas.', 'price' => 280, 'sku' => 'CEP-001'],
            ['name' => 'Cloro Natural 1L', 'slug' => 'cloro-natural-1l', 'description' => 'Cloro ecologico sin productos quimicos agresivos.', 'price' => 650, 'sku' => 'CLO-001'],
            ['name' => 'Ganchos de Ropa 30u', 'slug' => 'ganchos-ropa-30u', 'description' => 'Ganchos plasticos resistente para tendederos, pack 30.', 'price' => 550, 'sku' => 'GAN-030'],
        ];

        foreach ($products as $i => $product) {
            BusinessProduct::updateOrCreate(
                ['business_id' => $business->id, 'slug' => $product['slug']],
                [
                    'name' => $product['name'],
                    'description' => $product['description'],
                    'price' => $product['price'],
                    'sku' => $product['sku'],
                    'quantity' => rand(10, 100),
                    'is_active' => true,
                    'is_featured' => $i < 3,
                    'sort_order' => $i + 1,
                ]
            );
        }

        $galleryImages = [
            ['title' => 'Interior del local', 'filename' => 'lavanderia-interior-1.jpg'],
            ['title' => 'Maquinas de lavado industriales', 'filename' => 'lavanderia-maquinas.jpg'],
            ['title' => 'Proceso de planchado', 'filename' => 'lavanderia-plancha.jpg'],
            ['title' => 'Tintoreria profesional', 'filename' => 'lavanderia-tinto.jpg'],
            ['title' => 'Zona de espera', 'filename' => 'lavanderia-espera.jpg'],
            ['title' => 'Productos de limpieza', 'filename' => 'lavanderia-productos.jpg'],
            ['title' => 'Entrada del local', 'filename' => 'lavanderia-entrada.jpg'],
            ['title' => 'Centro de planchado', 'filename' => 'lavanderia-centro-plancha.jpg'],
            ['title' => 'Ropa limpia organizada', 'filename' => 'lavanderia-organizada.jpg'],
            ['title' => 'Equipo de trabajo', 'filename' => 'lavanderia-equipo.jpg'],
        ];

        foreach ($galleryImages as $i => $image) {
            $path = "https://picsum.photos/seed/lavanderia{$i}/800/600";
            BusinessGalleryImage::updateOrCreate(
                ['business_id' => $business->id, 'filename' => $image['filename']],
                [
                    'path' => $path,
                    'original_name' => $image['filename'],
                    'title' => $image['title'],
                    'sort_order' => $i + 1,
                    'is_active' => true,
                ]
            );
        }

        $this->command->info("===========================================");
        $this->command->info("LAVANDERIA MANOLOS CARGADO");
        $this->command->info("===========================================");
        $this->command->info("Usuario: lavanderia@manolos.com / password123");
        $this->command->info("Plan: {$planBusiness->name}");
        $this->command->info("Negocio: {$business->name}");
        $this->command->info("URL: http://saas.local/b/lavanderia-manolos");
        $this->command->info("-------------------------------------------");
        $this->command->info("Modulos activos:");
        foreach ($business->modules as $module) {
            if ($module->is_enabled) {
                $this->command->info("  - {$module->moduleDefinition?->name}: ON");
            }
        }
        $this->command->info("-------------------------------------------");
        $this->command->info("Datos cargados:");
        $this->command->info("  - Servicios: {$business->services()->count()}");
        $this->command->info("  - Productos: {$business->products()->count()}");
        $this->command->info("  - Imagenes Galeria: {$business->galleryImages()->count()}");
        $this->command->info("===========================================");
    }
}
