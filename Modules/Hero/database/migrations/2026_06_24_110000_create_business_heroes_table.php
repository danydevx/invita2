<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_heroes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->text('subtitle')->nullable();
            $table->enum('background_type', ['color', 'gradient', 'image'])->default('gradient');
            $table->string('background_color')->nullable();
            $table->string('background_gradient_start')->nullable();
            $table->string('background_gradient_end')->nullable();
            $table->string('background_image_path')->nullable();
            $table->enum('alignment', ['left', 'center', 'right'])->default('left');
            $table->json('buttons')->nullable();
            $table->boolean('show_contact_info')->default(true);
            $table->boolean('show_social_links')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('business_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_heroes');
    }
};
