<?php

namespace Modules\VCards\Database\Seeders;

use Illuminate\Database\Seeder;

class VCardsDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            VCardsFieldTypesSeeder::class,
        ]);
    }
}
