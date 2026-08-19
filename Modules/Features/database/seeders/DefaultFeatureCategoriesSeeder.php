<?php

namespace Modules\Features\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Features\Models\FeatureCategory;

class DefaultFeatureCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Hotel', 'icon' => 'bi bi-building', 'sort_order' => 1],
            ['name' => 'Spa', 'icon' => 'bi bi-droplet', 'sort_order' => 2],
            ['name' => 'Barberia', 'icon' => 'bi bi-scissors', 'sort_order' => 3],
            ['name' => 'Salon de Belleza', 'icon' => 'bi bi-heart', 'sort_order' => 4],
            ['name' => 'Gimnasio', 'icon' => 'bi bi-person-bounding-box', 'sort_order' => 5],
            ['name' => 'Restaurante', 'icon' => 'bi bi-cup-hot', 'sort_order' => 6],
            ['name' => 'Salon de Eventos', 'icon' => 'bi bi-calendar-event', 'sort_order' => 7],
            ['name' => 'Consultorio Medico', 'icon' => 'bi bi-heart-pulse', 'sort_order' => 8],
            ['name' => 'Lavanderia', 'icon' => 'bi bi-droplet-half', 'sort_order' => 9],
            ['name' => 'Veterinaria', 'icon' => 'bi bi-bug', 'sort_order' => 10],
        ];

        foreach ($categories as $category) {
            FeatureCategory::updateOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($category['name'])],
                [
                    'name' => $category['name'],
                    'icon' => $category['icon'],
                    'sort_order' => $category['sort_order'],
                    'is_active' => true,
                ]
            );
        }
    }
}
