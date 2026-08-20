<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listing_minisite_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('listing_id');
            $table->string('theme_key', 50)->default('default');
            $table->string('hero_layout', 20)->default('left');
            $table->string('hero_title')->nullable();
            $table->string('hero_subtitle')->nullable();
            $table->string('hero_background_image')->nullable();
            $table->boolean('hero_show_social')->default(false);
            $table->text('footer_text')->nullable();
            $table->boolean('footer_show_social')->default(true);
            $table->boolean('is_active')->default(false);
            $table->timestamps();

            $table->unique('listing_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listing_minisite_settings');
    }
};