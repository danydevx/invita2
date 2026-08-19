<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('business_module_definitions')->updateOrInsert(
            ['key' => 'office_hours'],
            [
                'name' => 'Horarios de Atención',
                'description' => 'Gestiona los horarios de atención de tus ubicaciones',
                'icon' => 'bi bi-clock',
                'sort_order' => 22,
                'has_settings' => false,
                'is_premium' => false,
                'show_in_menu' => true,
                'menu_title' => 'Horarios',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('business_module_definitions')->where('key', 'office_hours')->delete();
    }
};
