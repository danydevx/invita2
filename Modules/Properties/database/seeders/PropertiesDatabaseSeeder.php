<?php

namespace Modules\Properties\Database\Seeders;

use Illuminate\Database\Seeder;

class PropertiesDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PropertyTypeSeeder::class,
        ]);
    }
}
