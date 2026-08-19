<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_module_definitions', function (Blueprint $table) {
            $table->json('settings')->nullable()->after('has_settings');
            $table->json('settings_schema')->nullable()->after('settings');
        });
    }

    public function down(): void
    {
        Schema::table('business_module_definitions', function (Blueprint $table) {
            $table->dropColumn(['settings', 'settings_schema']);
        });
    }
};