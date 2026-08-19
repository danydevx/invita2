<?php

namespace Database\Seeders;

use App\Models\BusinessModuleDefinition;
use App\Models\Plan;
use App\Models\PlanBusinessModule;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanBusinessModuleSeeder extends Seeder
{
    public function run(): void
    {
        $definitions = BusinessModuleDefinition::where('is_active', true)
            ->pluck('id', 'key');

        if ($definitions->isEmpty()) {
            $this->command->error('No module definitions found. Run BusinessModuleDefinitionSeeder first.');
            return;
        }

        $plansData = [
            'free' => [
                'name' => 'Plan Free',
                'modules' => ['locations', 'contact_form', 'gallery', 'about', 'socialmedia'],
            ],
            'business' => [
                'name' => 'Plan Negocio',
                'modules' => ['locations', 'contact_form', 'gallery', 'leads', 'services', 'appointments', 'products', 'reviews', 'promotions', 'restaurant_menu', 'about', 'socialmedia', 'features', 'properties', 'team_members', 'packages'],
            ],
            'ai' => [
                'name' => 'Plan IA',
                'modules' => ['locations', 'contact_form', 'gallery', 'leads', 'services', 'appointments', 'products', 'ai_chatbot', 'reviews', 'promotions', 'restaurant_menu', 'about', 'socialmedia', 'features', 'properties', 'team_members', 'packages'],
            ],
        ];

        foreach ($plansData as $slug => $data) {
            $plan = Plan::updateOrCreate(
                ['slug' => $slug],
                ['name' => $data['name']]
            );

            foreach ($definitions as $key => $definitionId) {
                $isEnabled = in_array($key, $data['modules']);

                PlanBusinessModule::updateOrCreate(
                    [
                        'plan_id' => $plan->id,
                        'module_definition_id' => $definitionId,
                    ],
                    [
                        'is_enabled' => $isEnabled,
                        'module_key' => $key,
                    ]
                );
            }

            $enabledCount = count($data['modules']);
            $this->command->info("Plan '{$slug}' created/updated with {$enabledCount} modules enabled.");
        }
    }
}
