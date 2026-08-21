<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('settings')->insert([
            ['key' => 'regional.default_language', 'value' => 'es', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'regional.user_default_language', 'value' => 'es', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'regional.country_code', 'value' => 'MX', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'regional.currency_code', 'value' => 'MXN', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        DB::table('settings')->whereIn('key', [
            'regional.default_language',
            'regional.user_default_language',
            'regional.country_code',
            'regional.currency_code',
        ])->delete();
    }
};
