<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_module_definitions', function (Blueprint $table) {
            $table->string('settings_url')->nullable()->after('has_settings');
        });
    }

    public function down(): void
    {
        Schema::table('business_module_definitions', function (Blueprint $table) {
            $table->dropColumn('settings_url');
        });
    }
};