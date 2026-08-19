<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_minisite_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            $table->string('theme_key', 50)->default('default');
            $table->string('hero_layout', 20)->default('left');
            $table->string('hero_title')->nullable();
            $table->string('hero_subtitle')->nullable();
            $table->string('hero_background_image')->nullable();
            $table->text('footer_text')->nullable();
            $table->boolean('footer_show_social')->default(true);
            $table->boolean('is_active')->default(false);
            $table->timestamps();

            $table->unique('business_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_minisite_settings');
    }
};
