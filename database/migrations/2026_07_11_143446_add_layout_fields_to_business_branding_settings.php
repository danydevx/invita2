<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_branding_settings', function (Blueprint $table) {
            $table->json('page_style')->nullable();
            $table->json('section_style')->nullable();
            $table->json('hero_style')->nullable();
            $table->json('animations')->nullable();
            $table->boolean('buttons_uppercase')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('business_branding_settings', function (Blueprint $table) {
            $table->dropColumn(['page_style', 'section_style', 'hero_style', 'animations', 'buttons_uppercase']);
        });
    }
};
