<?php

namespace Modules\Hero\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Hero\Models\BusinessHero;
use Modules\Businesses\Models\Business;

class HeroSeeder extends Seeder
{
    public function run(): void
    {
        $businesses = Business::where('is_active', true)->get();

        foreach ($businesses as $business) {
            BusinessHero::updateOrCreate(
                ['business_id' => $business->id],
                [
                    'title' => $business->name,
                    'subtitle' => $business->description,
                    'text_aux' => null,
                    'background_type' => 'gradient',
                    'background_color' => '#1A1A1A',
                    'background_gradient_start' => '#FF4500',
                    'background_gradient_end' => '#FF6B35',
                    'alignment' => 'left',
                    'buttons' => [],
                    'show_contact_info' => true,
                    'show_social_links' => false,
                    'is_active' => true,
                    'sort_order' => 0,
                ]
            );
        }
    }
}
