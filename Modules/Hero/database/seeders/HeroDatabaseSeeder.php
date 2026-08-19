<?php

namespace Modules\Hero\Database\Seeders;

use Illuminate\Database\Seeder;

class HeroDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            HeroSeeder::class,
        ]);
    }
}
