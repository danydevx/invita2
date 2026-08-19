<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_product_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->string('filename');
            $table->string('original_name');
            $table->string('extension', 20)->nullable();
            $table->string('mime_type', 150)->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->index('business_product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_product_images');
    }
};
