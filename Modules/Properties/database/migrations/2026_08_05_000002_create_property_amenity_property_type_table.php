<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_amenity_property_type', function (Blueprint $table) {
            $table->foreignId('property_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('property_amenity_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->primary(['property_type_id', 'property_amenity_id']);
            $table->index(['property_type_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_amenity_property_type');
    }
};
