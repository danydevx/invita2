<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_minisite_settings', function (Blueprint $table) {
            $table->boolean('hero_show_social')->default(false)->after('hero_background_image');
        });
    }

    public function down(): void
    {
        Schema::table('business_minisite_settings', function (Blueprint $table) {
            $table->dropColumn('hero_show_social');
        });
    }
};
