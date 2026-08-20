<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listing_service_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_service_id')->constrained()->cascadeOnDelete();
            $table->string('path', 500);
            $table->string('filename', 255);
            $table->string('original_name', 255);
            $table->string('extension', 50);
            $table->string('mime_type', 100);
            $table->unsignedInteger('size');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->index('listing_service_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listing_service_images');
    }
};
