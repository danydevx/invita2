<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_field_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_type_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['property_type_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_field_sections');
    }
};
