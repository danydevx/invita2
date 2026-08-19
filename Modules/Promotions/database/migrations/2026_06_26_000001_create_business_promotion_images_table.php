<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_promotion_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promotion_id')->constrained('business_promotions')->cascadeOnDelete();
            $table->string('path');
            $table->string('filename');
            $table->string('original_name');
            $table->string('extension')->nullable();
            $table->string('mime_type');
            $table->unsignedInteger('size');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('promotion_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_promotion_images');
    }
};
