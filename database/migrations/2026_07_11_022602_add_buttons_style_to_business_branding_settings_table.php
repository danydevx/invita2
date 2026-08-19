<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('business_branding_settings', function (Blueprint $table) {
            $table->string('buttons_style')->default('round')->after('dark_mode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_branding_settings', function (Blueprint $table) {
            $table->dropColumn('buttons_style');
        });
    }
};
