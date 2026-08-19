<?php

namespace Modules\AiChatbot\Database\Seeders;

use Illuminate\Database\Seeder;

class AiChatbotDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            PersonalitySeeder::class,
            PresetSeeder::class,
        ]);
    }
}
