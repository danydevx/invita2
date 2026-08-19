<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Businesses\Models\Business;
use Modules\RestaurantMenu\Entities\MenuCategory;
use Modules\RestaurantMenu\Entities\MenuProduct;
use Modules\RestaurantMenu\Entities\MenuProductVariant;

class LavanderiaManolosMenuSeeder extends Seeder
{
    public function run(): void
    {
        $business = Business::where('slug', 'lavanderia-manolos')->first();
        if (!$business) {
            $this->command->error('Business lavanderia-manolos not found.');
            return;
        }

        $categories = [
            ['name' => 'Servicios de Lavado', 'slug' => 'servicios-lavado', 'description' => 'Servicios principales de lavado de ropa', 'sort' => 1],
            ['name' => 'Planchado', 'slug' => 'planchado', 'description' => 'Servicios de planchado profesional', 'sort' => 2],
            ['name' => 'Tintoreria', 'slug' => 'tintoreria', 'description' => 'Servicios de tintoreria y Lavado seco', 'sort' => 3],
            ['name' => 'Tratamientos', 'slug' => 'tratamientos', 'description' => 'Tratamientos especiales para prendas', 'sort' => 4],
            ['name' => 'Productos', 'slug' => 'productos', 'description' => 'Productos de limpieza y cuidado de ropa', 'sort' => 5],
        ];

        foreach ($categories as $cat) {
            $category = MenuCategory::updateOrCreate(
                ['business_id' => $business->id, 'slug' => $cat['slug']],
                [
                    'title' => $cat['name'],
                    'description' => $cat['description'],
                    'active' => true,
                    'sort_order' => $cat['sort'],
                ]
            );

            $products = $this->productsForCategory($cat['slug'], $business->id, $category->id);
            foreach ($products as $i => $prod) {
                $product = MenuProduct::updateOrCreate(
                    ['business_id' => $business->id, 'slug' => $prod['slug']],
                    [
                        'category_id' => $category->id,
                        'title' => $prod['name'],
                        'description' => $prod['description'],
                        'base_price' => $prod['price'],
                        'show_price' => true,
                        'featured' => $prod['featured'] ?? false,
                        'active' => true,
                        'sort_order' => $i + 1,
                    ]
                );

                if (!empty($prod['variants'])) {
                    foreach ($prod['variants'] as $vi => $variant) {
                        MenuProductVariant::updateOrCreate(
                            ['product_id' => $product->id, 'title' => $variant['name']],
                            [
                                'description' => $variant['description'] ?? null,
                                'price' => $variant['price'],
                                'active' => true,
                                'sort_order' => $vi + 1,
                            ]
                        );
                    }
                }
            }
        }

        $this->command->info("===========================================");
        $this->command->info("LAVANDERIA MANOLOS - MENU CARGADO");
        $this->command->info("===========================================");
        $this->command->info("Categorias: " . MenuCategory::where('business_id', $business->id)->count());
        $this->command->info("Productos: " . MenuProduct::where('business_id', $business->id)->count());
        $this->command->info("Variantes: " . MenuProductVariant::whereIn('product_id', MenuProduct::where('business_id', $business->id)->pluck('id'))->count());
        $this->command->info("===========================================");
    }

    private function productsForCategory(string $category, int $businessId, int $categoryId): array
    {
        $products = [
            'servicios-lavado' => [
                ['name' => 'Lavado Express 2hr', 'slug' => 'lavado-express-2hr', 'description' => 'Lavado rapido en solo 2 horas. Incluye centrifugado y secado basico.', 'price' => 450, 'featured' => true],
                ['name' => 'Lavado Normal', 'slug' => 'lavado-normal', 'description' => 'Lavado completo con detergente premium y suavizante. Entrega en 24hr.', 'price' => 350, 'featured' => false],
                ['name' => 'Lavado Sabanas y Toallas', 'slug' => 'lavado-sabanas-toallas', 'description' => 'Lavado especializado para sabanas, toallas y ropa de cama.', 'price' => 950, 'featured' => false],
                ['name' => 'Lavado Cortinas', 'slug' => 'lavado-cortinas', 'description' => 'Lavado y secado de cortinas de todos los tamanos.', 'price' => 1800, 'featured' => false],
                ['name' => 'Lavado Edredones', 'slug' => 'lavado-edredones', 'description' => 'Lavado para edredones y acolchados grandes.', 'price' => 2200, 'featured' => false],
            ],
            'planchado' => [
                ['name' => 'Planchado Express', 'slug' => 'plancha-express', 'description' => 'Planchado rapido para prendas urgentes.', 'price' => 500, 'featured' => false],
                ['name' => 'Planchado Normal', 'slug' => 'plancha-normal', 'description' => 'Planchado profesional con vapor. Resultado impecable.', 'price' => 600, 'featured' => true, 'variants' => [
                    ['name' => 'Por prenda', 'price' => 600],
                    ['name' => 'Por kilo', 'price' => 80],
                ]],
                ['name' => 'Planchado de Trajes', 'slug' => 'plancha-trajes', 'description' => 'Planchado especializado para trajes y ropa formal.', 'price' => 1200, 'featured' => false, 'variants' => [
                    ['name' => 'Saco', 'price' => 500],
                    ['name' => 'Pantalon', 'price' => 350],
                    ['name' => 'Completo', 'price' => 1200],
                ]],
                ['name' => 'Lavado + Planchado', 'slug' => 'lavado-plancha', 'description' => 'Servicio completo de lavado y planchado profesional.', 'price' => 800, 'featured' => true, 'variants' => [
                    ['name' => 'Por kilo', 'price' => 800],
                    ['name' => 'Por pieza', 'price' => 450],
                ]],
            ],
            'tintoreria' => [
                ['name' => 'Lavado en Seco', 'slug' => 'lavado-seco', 'description' => 'Limpieza en seco para trajes y prendas delicadas.', 'price' => 1200, 'featured' => true, 'variants' => [
                    ['name' => 'Saco', 'price' => 450],
                    ['name' => 'Vestido', 'price' => 800],
                    ['name' => 'Traje completo', 'price' => 1200],
                ]],
                ['name' => 'Tintado de Ropa', 'slug' => 'tintado-ropa', 'description' => 'Tintado profesional de telas y prendas.', 'price' => 1500, 'featured' => false, 'variants' => [
                    ['name' => 'Tinte solido', 'price' => 1500],
                    ['name' => 'Tinte degradado', 'price' => 2500],
                ]],
                ['name' => 'Teñido de Cortinas', 'slug' => 'tenido-cortinas', 'description' => 'Teñido de cortinas y fundas.', 'price' => 2000, 'featured' => false],
                ['name' => 'Planchado a Vapor Industrial', 'slug' => 'plancha-vapor-industrial', 'description' => 'Planchado con vapor industrial para grandes volumenes.', 'price' => 900, 'featured' => false],
            ],
            'tratamientos' => [
                ['name' => 'Tratamiento Anti Manchas', 'slug' => 'anti-manchas', 'description' => 'Tratamiento especializado para remover manchas dificiles.', 'price' => 700, 'featured' => true, 'variants' => [
                    ['name' => 'Manchas leves', 'price' => 500],
                    ['name' => 'Manchas moderadas', 'price' => 700],
                    ['name' => 'Manchas severas', 'price' => 1000],
                ]],
                ['name' => 'Impermeabilizado', 'slug' => 'impermeabilizado', 'description' => 'Tratamiento impermeable para telas y prendas.', 'price' => 1100, 'featured' => false],
                ['name' => 'Desodorizado', 'slug' => 'desodorizado', 'description' => 'Eliminacion de olores con tratamiento profesional.', 'price' => 600, 'featured' => false],
                ['name' => 'Lavado Higienico', 'slug' => 'lavado-higienico', 'description' => 'Lavado con productos antibacterianos. Ideal para ropa intima y ninos.', 'price' => 850, 'featured' => false],
            ],
            'productos' => [
                ['name' => 'Detergente Premium 1L', 'slug' => 'detergente-premium-1l', 'description' => 'Detergente de alta concentracion para lavado a mano o maquina.', 'price' => 850, 'featured' => true],
                ['name' => 'Suavizante Premium 1L', 'slug' => 'suavizante-premium-1l', 'description' => 'Suavizante concentrado con fragancia duradera.', 'price' => 750, 'featured' => false],
                ['name' => 'Anti Manchas Spray', 'slug' => 'anti-manchas-spray', 'description' => 'Quitamanchas en spray para manchas recientes.', 'price' => 450, 'featured' => false],
                ['name' => 'Bolsa de Lavado 10u', 'slug' => 'bolsa-lavado-10u', 'description' => 'Bolsas de malla para ropa delicada, pack de 10 unidades.', 'price' => 1200, 'featured' => false],
                ['name' => 'Perchas Premium 20u', 'slug' => 'perchas-premium-20u', 'description' => 'Perchas antideslizantes de plastico resistente, pack 20.', 'price' => 900, 'featured' => false],
                ['name' => 'Fundas de Ropa 5u', 'slug' => 'fundas-ropa-5u', 'description' => 'Fundas de tela para proteger prendas delicadas, pack 5.', 'price' => 1500, 'featured' => false],
                ['name' => 'Esponja Anti Bacteriana', 'slug' => 'esponja-anti-bacteriana', 'description' => 'Esponja con tratamiento antibacteriano para lavado.', 'price' => 350, 'featured' => false],
                ['name' => 'Cepillo para Ropa', 'slug' => 'cepillo-ropa', 'description' => 'Cepillo de cerdas suaves para prendas delicadas.', 'price' => 280, 'featured' => false],
                ['name' => 'Cloro Natural 1L', 'slug' => 'cloro-natural-1l', 'description' => 'Cloro ecologico sin productos quimicos agresivos.', 'price' => 650, 'featured' => false],
                ['name' => 'Ganchos de Ropa 30u', 'slug' => 'ganchos-ropa-30u', 'description' => 'Ganchos plasticos resistentes para tendederos, pack 30.', 'price' => 550, 'featured' => false],
            ],
        ];

        return $products[$category] ?? [];
    }
}
