<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $table->foreignId('property_type_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('operation_type');
            $table->decimal('price', 12, 2)->default(0);
            $table->string('currency', 10)->default('MXN');
            $table->string('price_period', 20)->default('single');
            $table->string('main_image')->nullable();
            $table->string('status', 30)->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_public')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('listing_id');
            $table->index('property_type_id');
            $table->index('operation_type');
            $table->index('status');
            $table->index('price');
            $table->index('is_public');
            $table->index(['listing_id', 'status']);
            $table->unique(['listing_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
