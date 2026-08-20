<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('listing_branding_settings', function (Blueprint $table) {
            $table->dropColumn([
                'section_variants',
                'page_style',
                'section_style',
                'hero_style',
                'animations',
                'generated_css',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('listing_branding_settings', function (Blueprint $table) {
            $table->json('section_variants')->nullable();
            $table->string('page_style', 50)->nullable();
            $table->string('section_style', 50)->nullable();
            $table->string('hero_style', 50)->nullable();
            $table->json('animations')->nullable();
            $table->longText('generated_css')->nullable();
        });
    }
};
