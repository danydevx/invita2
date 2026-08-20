<?php

namespace Modules\ListingHero\Database\Seeders;

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
