<?php

namespace Modules\ListingAiChatbot\Database\Seeders;

use Illuminate\Database\Seeder;

class ListingAiChatbotDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PersonalitySeeder::class,
            PresetSeeder::class,
        ]);
    }
}
