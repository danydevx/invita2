<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_branding_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->unique()->constrained()->cascadeOnDelete();

            $table->json('colors')->nullable();
            $table->json('fonts')->nullable();
            $table->string('custom_font_url')->nullable();
            $table->boolean('dark_mode')->default(false);

            $table->text('generated_css')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_branding_settings');
    }
};
