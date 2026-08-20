<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('listing_modules', function (Blueprint $table) {
            $table->foreignId('module_definition_id')->nullable()->after('listing_id')->constrained('listing_module_definitions')->nullOnDelete();
            $table->boolean('show_in_menu')->default(false)->after('is_enabled');
            $table->string('menu_title')->nullable()->after('show_in_menu');
        });
    }

    public function down(): void
    {
        Schema::table('listing_modules', function (Blueprint $table) {
            $table->dropForeign(['module_definition_id']);
            $table->dropColumn(['module_definition_id', 'show_in_menu', 'menu_title']);
        });
    }
};
