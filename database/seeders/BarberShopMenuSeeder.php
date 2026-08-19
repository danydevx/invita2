<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Businesses\Models\Business;
use Modules\RestaurantMenu\Entities\MenuCategory;
use Modules\RestaurantMenu\Entities\MenuProduct;
use Modules\RestaurantMenu\Entities\MenuProductVariant;

class BarberShopMenuSeeder extends Seeder
{
    public function run(): void
    {
        $business = Business::find(1);

        if (!$business) {
            $this->command->error('Business ID 1 not found');
            return;
        }

        $categories = [
            [
                'title' => 'Cortes de Cabello',
                'description' => 'Cortes clasicos y modernos para todo estilo',
                'image' => 'https://images.unsplash.com/photo-1599351431202-1e0f0137899a?w=400',
                'children' => [
                    ['title' => 'Corte Clasico', 'description' => 'Corte tradicional con navaja', 'image' => 'https://images.unsplash.com/photo-1503951914875-452162b0f3f1?w=400'],
                    ['title' => 'Corte Moderno', 'description' => 'Corte contemporaneo con diseño', 'image' => 'https://images.unsplash.com/photo-1621605815971-fbc98d665033?w=400'],
                    ['title' => 'Corte Degradado', 'description' => 'Fade con diferentes niveles', 'image' => 'https://images.unsplash.com/photo-1593702288056-7927b442d0fa?w=400'],
                    ['title' => 'Corte Naipes', 'description' => 'Lineas y diseños en el corte', 'image' => 'https://images.unsplash.com/photo-1585747860715-2ba37e788b70?w=400'],
                ],
            ],
            [
                'title' => 'Barba',
                'description' => 'Servicio completo de cuidado facial',
                'image' => 'https://images.unsplash.com/photo-1584483766114-2cea6facdf57?w=400',
                'children' => [
                    ['title' => 'Afeitado Clasico', 'description' => 'Afeitado con toalla caliente', 'image' => 'https://images.unsplash.com/photo-1621607512214-5e4c66e1c8eb?w=400'],
                    ['title' => 'Barba Completa', 'description' => 'Diseño y limpieza de barba', 'image' => 'https://images.unsplash.com/photo-1605497788046-5a8c8e82f1b0?w=400'],
                    ['title' => 'Perfilado', 'description' => 'Perfilado de barba y bigote', 'image' => 'https://images.unsplash.com/photo-1592663527359-cf6642f54cff?w=400'],
                    ['title' => 'Tratamiento Barba', 'description' => 'Hidratación y cuidado facial', 'image' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400'],
                ],
            ],
            [
                'title' => 'Paquetes',
                'description' => 'Combos especiales con descuento',
                'image' => 'https://images.unsplash.com/photo-1634302086887-13b5281d5a61?w=400',
                'children' => [
                    ['title' => 'Paquete Ejecutivo', 'description' => 'Corte + Barba + Cejas', 'image' => 'https://images.unsplash.com/photo-1622286342621-4bd5fd5f82b0?w=400'],
                    ['title' => 'Paquete Premium', 'description' => 'Corte + Barba + Tratamientos', 'image' => 'https://images.unsplash.com/photo-1493256338651-d82f7acb2b38?w=400'],
                    ['title' => 'Paquete Novios', 'description' => 'Servicio especial para bodas', 'image' => 'https://images.unsplash.com/photo-1464366400600-7168b8af9bc3?w=400'],
                ],
            ],
            [
                'title' => 'Productos',
                'description' => 'Productos de cuidado personal',
                'image' => 'https://images.unsplash.com/photo-1585751119414-ef2636f8aede?w=400',
                'children' => [
                    ['title' => 'Pomadas', 'description' => 'Para peinar y dar forma', 'image' => 'https://images.unsplash.com/photo-1608248543803-ba4f8c70ae0b?w=400'],
                    ['title' => 'Aceites para Barba', 'description' => 'Hidratación e brilho', 'image' => 'https://images.unsplash.com/photo-1626285861696-9f0bf5a49c6d?w=400'],
                    ['title' => 'Shampoo', 'description' => 'Limpieza y nutrición', 'image' => 'https://images.unsplash.com/photo-1556227702-d1e4e7b5c232?w=400'],
                ],
            ],
        ];

        foreach ($categories as $catData) {
            $children = $catData['children'] ?? [];
            $catImage = $catData['image'] ?? null;
            unset($catData['children'], $catData['image']);

            $parentCategory = MenuCategory::create([
                'business_id' => $business->id,
                'title' => $catData['title'],
                'description' => $catData['description'] ?? null,
                'image' => $catImage,
                'active' => true,
                'sort_order' => 0,
            ]);

            foreach ($children as $index => $childData) {
                $childImage = $childData['image'] ?? null;
                unset($childData['image']);

                $category = MenuCategory::create([
                    'business_id' => $business->id,
                    'parent_id' => $parentCategory->id,
                    'title' => $childData['title'],
                    'description' => $childData['description'] ?? null,
                    'image' => $childImage,
                    'active' => true,
                    'sort_order' => $index,
                ]);

                $products = $this->getProductsForCategory($childData['title']);
                foreach ($products as $prodIndex => $prodData) {
                    $product = MenuProduct::create([
                        'business_id' => $business->id,
                        'category_id' => $category->id,
                        'title' => $prodData['title'],
                        'description' => $prodData['description'] ?? null,
                        'image' => $prodData['image'] ?? null,
                        'base_price' => $prodData['price'],
                        'show_price' => true,
                        'featured' => $prodData['featured'] ?? false,
                        'active' => true,
                        'sort_order' => $prodIndex,
                    ]);

                    if (isset($prodData['variants'])) {
                        foreach ($prodData['variants'] as $varIndex => $variantData) {
                            MenuProductVariant::create([
                                'product_id' => $product->id,
                                'title' => $variantData['title'],
                                'price' => $variantData['price'],
                                'active' => true,
                                'sort_order' => $varIndex,
                            ]);
                        }
                    }
                }
            }
        }

        $this->command->info("BarberShop menu seeded for business: {$business->name}");
    }

    private function getProductsForCategory(string $categoryTitle): array
    {
        $products = [
            'Corte Clasico' => [
                ['title' => 'Corte Americano', 'description' => 'Clasico con lados cortos', 'price' => 150, 'image' => 'https://images.unsplash.com/photo-1599351431202-1e0f0137899a?w=400'],
                ['title' => 'Corte Italiano', 'description' => 'Clasico con volumen arriba', 'price' => 160, 'image' => 'https://images.unsplash.com/photo-1503951914875-452162b0f3f1?w=400'],
                ['title' => 'Corte Britanico', 'description' => 'Con línea definidas', 'price' => 170, 'image' => 'https://images.unsplash.com/photo-1621605815971-fbc98d665033?w=400'],
            ],
            'Corte Moderno' => [
                ['title' => 'Undercut', 'description' => 'Lados rapidos, arriba largo', 'price' => 180, 'featured' => true, 'image' => 'https://images.unsplash.com/photo-1593702288056-7927b442d0fa?w=400'],
                ['title' => 'Texturizado', 'description' => 'Con capas y movimiento', 'price' => 190, 'image' => 'https://images.unsplash.com/photo-1585747860715-2ba37e788b70?w=400'],
                ['title' => 'Quiff Moderno', 'description' => 'Estilo elegante hacia atras', 'price' => 200, 'image' => 'https://images.unsplash.com/photo-1622286342621-4bd5fd5f82b0?w=400'],
            ],
            'Corte Degradado' => [
                ['title' => 'Low Fade', 'description' => 'Degradado bajo', 'price' => 150, 'image' => 'https://images.unsplash.com/photo-1503951914875-452162b0f3f1?w=400'],
                ['title' => 'Mid Fade', 'description' => 'Degradado medio', 'price' => 170, 'image' => 'https://images.unsplash.com/photo-1599351431202-1e0f0137899a?w=400'],
                ['title' => 'High Fade', 'description' => 'Degradado alto', 'price' => 190, 'image' => 'https://images.unsplash.com/photo-1621607512214-5e4c66e1c8eb?w=400'],
                ['title' => 'Skin Fade', 'description' => 'Degradado al ras', 'price' => 220, 'featured' => true, 'image' => 'https://images.unsplash.com/photo-1626285861696-9f0bf5a49c6d?w=400'],
            ],
            'Corte Naipes' => [
                ['title' => 'Linea Simple', 'description' => 'Una línea en el costado', 'price' => 30, 'image' => 'https://images.unsplash.com/photo-1584483766114-2cea6facdf57?w=400'],
                ['title' => 'Lineas Múltiples', 'description' => 'Varias líneas geométricas', 'price' => 50, 'image' => 'https://images.unsplash.com/photo-1592663527359-cf6642f54cff?w=400'],
                ['title' => 'Diseño Completo', 'description' => 'Diseño personalizado', 'price' => 80, 'image' => 'https://images.unsplash.com/photo-1605497788046-5a8c8e82f1b0?w=400'],
            ],
            'Afeitado Clasico' => [
                ['title' => 'Afeitado Basic', 'description' => 'Con espuma y navaja', 'price' => 80, 'image' => 'https://images.unsplash.com/photo-1621607512214-5e4c66e1c8eb?w=400'],
                ['title' => 'Afeitado Premium', 'description' => 'Con toalla caliente y after shave', 'price' => 120, 'image' => 'https://images.unsplash.com/photo-1622286342621-4bd5fd5f82b0?w=400'],
            ],
            'Barba Completa' => [
                ['title' => 'Barba Clasica', 'description' => 'Diseño y limpieza completa', 'price' => 100, 'image' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400'],
                ['title' => 'Barba con Aceite', 'description' => 'Limpieza más hidratación', 'price' => 130, 'featured' => true, 'image' => 'https://images.unsplash.com/photo-1592663527359-cf6642f54cff?w=400'],
            ],
            'Perfilado' => [
                ['title' => 'Perfilado Basico', 'description' => 'Linea simple', 'price' => 50, 'image' => 'https://images.unsplash.com/photo-1584483766114-2cea6facdf57?w=400'],
                ['title' => 'Perfilado Completo', 'description' => 'Barba y bigote perfilados', 'price' => 70, 'image' => 'https://images.unsplash.com/photo-1605497788046-5a8c8e82f1b0?w=400'],
            ],
            'Tratamiento Barba' => [
                ['title' => 'Hidratación', 'description' => 'Mascarilla hidratante', 'price' => 90, 'image' => 'https://images.unsplash.com/photo-1493256338651-d82f7acb2b38?w=400'],
                ['title' => 'Masaje Facial', 'description' => 'Relajante con aceites', 'price' => 120, 'image' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400'],
            ],
            'Paquete Ejecutivo' => [
                ['title' => 'Ejecutivo Basico', 'description' => 'Corte + Barba + Cejas', 'price' => 250, 'featured' => true, 'image' => 'https://images.unsplash.com/photo-1622286342621-4bd5fd5f82b0?w=400', 'variants' => [
                    ['title' => 'Solo Corte + Barba', 'price' => 200],
                    ['title' => 'Incluye Cejas', 'price' => 250],
                ]],
            ],
            'Paquete Premium' => [
                ['title' => 'Premium Full', 'description' => 'Todo incluido + tratamiento', 'price' => 450, 'featured' => true, 'image' => 'https://images.unsplash.com/photo-1493256338651-d82f7acb2b38?w=400', 'variants' => [
                    ['title' => 'Basico', 'price' => 350],
                    ['title' => 'Premium', 'price' => 450],
                ]],
            ],
            'Paquete Novios' => [
                ['title' => 'Servicio Novios', 'description' => 'Para la boda especial', 'price' => 500, 'image' => 'https://images.unsplash.com/photo-1464366400600-7168b8af9bc3?w=400', 'variants' => [
                    ['title' => 'Él', 'price' => 300],
                    ['title' => 'Él + Ella', 'price' => 500],
                ]],
            ],
            'Pomadas' => [
                ['title' => 'Pomada Fijacion Fuerte', 'description' => 'Para estilos rígidos', 'price' => 120, 'image' => 'https://images.unsplash.com/photo-1608248543803-ba4f8c70ae0b?w=400'],
                ['title' => 'Pomada Fijacion Media', 'description' => 'Para looks naturales', 'price' => 110, 'image' => 'https://images.unsplash.com/photo-1585751119414-ef2636f8aede?w=400'],
                ['title' => 'Pomada Mate', 'description' => 'Sin brilho, aspecto natural', 'price' => 130, 'image' => 'https://images.unsplash.com/photo-1527799820374-dcf8d9d4a388?w=400'],
            ],
            'Aceites para Barba' => [
                ['title' => 'Aceite Hierbas', 'description' => 'Con romero y lavanda', 'price' => 150, 'image' => 'https://images.unsplash.com/photo-1626285861696-9f0bf5a49c6d?w=400'],
                ['title' => 'Aceite Clásico', 'description' => 'Con argán y jojoba', 'price' => 180, 'featured' => true, 'image' => 'https://images.unsplash.com/photo-1608248543803-ba4f8c70ae0b?w=400'],
                ['title' => 'Kit Barba Completo', 'description' => 'Aceite + Bálsamo + Peine', 'price' => 350, 'image' => 'https://images.unsplash.com/photo-1585751119414-ef2636f8aede?w=400'],
            ],
            'Shampoo' => [
                ['title' => 'Shampoo Anticaspa', 'description' => 'Con ketoconazol', 'price' => 90, 'image' => 'https://images.unsplash.com/photo-1556227702-d1e4e7b5c232?w=400'],
                ['title' => 'Shampoo Hidratante', 'description' => 'Para cabello seco', 'price' => 85, 'image' => 'https://images.unsplash.com/photo-1608248543803-ba4f8c70ae0b?w=400'],
                ['title' => 'Shampoo Protein', 'description' => 'Para fortalecer el cabello', 'price' => 95, 'image' => 'https://images.unsplash.com/photo-1527799820374-dcf8d9d4a388?w=400'],
            ],
        ];

        return $products[$categoryTitle] ?? [];
    }
}
