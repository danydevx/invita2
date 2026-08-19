<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_module_definitions', function (Blueprint $table) {
            $table->boolean('show_in_menu')->default(false)->after('has_settings');
            $table->string('menu_title', 50)->nullable()->after('show_in_menu');
        });
    }

    public function down(): void
    {
        Schema::table('business_module_definitions', function (Blueprint $table) {
            $table->dropColumn(['show_in_menu', 'menu_title']);
        });
    }
};
