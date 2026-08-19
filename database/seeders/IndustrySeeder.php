<?php

namespace Database\Seeders;

use App\Models\BusinessModuleDefinition;
use App\Models\Industry;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IndustrySeeder extends Seeder
{
    public function run(): void
    {
        $modules = BusinessModuleDefinition::where('is_active', true)->get()->keyBy('key');

        $industries = [
            [
                'name' => 'Barbería',
                'slug' => 'barberia',
                'icon' => 'bi bi-scissors',
                'description' => 'Negocios de barbería y peluquería',
                'module_keys' => ['gallery', 'services', 'appointments', 'reviews', 'about', 'locations', 'contact_form', 'socialmedia', 'team_members', 'packages', 'client_fidelity'],
            ],
            [
                'name' => 'Spa y Belleza',
                'slug' => 'spa-belleza',
                'icon' => 'bi bi-spa',
                'description' => 'Negocios de spa, centro estética y tratamientos de belleza',
                'module_keys' => ['gallery', 'services', 'appointments', 'reviews', 'about', 'locations', 'contact_form', 'socialmedia', 'team_members', 'packages', 'client_fidelity'],
            ],
            [
                'name' => 'Restaurante',
                'slug' => 'restaurante',
                'icon' => 'bi bi-cup-hot',
                'description' => 'Negocios de restaurantes y comida',
                'module_keys' => ['gallery', 'restaurant_menu', 'about', 'locations', 'contact_form', 'socialmedia', 'reviews', 'team_members', 'packages', 'client_fidelity'],
            ],
            [
                'name' => 'Clínica Médica',
                'slug' => 'clinica-medica',
                'icon' => 'bi bi-heart-pulse',
                'description' => 'Negocios de clínicas y consultorios médicos',
                'module_keys' => ['gallery', 'services', 'appointments', 'about', 'locations', 'contact_form', 'socialmedia', 'leads', 'team_members', 'packages', 'client_fidelity'],
            ],
            [
                'name' => 'Lavandería',
                'slug' => 'lavanderia',
                'icon' => 'bi bi-droplet',
                'description' => 'Negocios de lavandería y tintorería',
                'module_keys' => ['gallery', 'services', 'appointments', 'about', 'locations', 'contact_form', 'socialmedia', 'leads', 'team_members', 'packages', 'client_fidelity'],
            ],
        ];

        foreach ($industries as $industryData) {
            $moduleKeys = $industryData['module_keys'];
            unset($industryData['module_keys']);

            $industry = Industry::updateOrCreate(
                ['slug' => $industryData['slug']],
                $industryData
            );

            $moduleIds = [];
            foreach ($moduleKeys as $key) {
                if (isset($modules[$key])) {
                    $moduleIds[] = $modules[$key]->id;
                }
            }

            $industry->moduleDefinitions()->sync($moduleIds);
        }

        $this->command->info('Industries seeded: ' . count($industries) . ' industries');
    }
}
