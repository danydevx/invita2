<?php

namespace Modules\Features\Database\Seeders;

use Illuminate\Database\Seeder;

class FeaturesDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            DefaultFeatureCategoriesSeeder::class,
            DefaultFeaturesSeeder::class,
        ]);
    }
}
