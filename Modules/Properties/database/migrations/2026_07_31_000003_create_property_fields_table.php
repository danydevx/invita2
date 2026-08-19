<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('section_id')->nullable()->constrained('property_field_sections')->nullOnDelete();
            $table->string('label');
            $table->string('field_key');
            $table->string('field_type');
            $table->text('description')->nullable();
            $table->text('help_text')->nullable();
            $table->string('placeholder')->nullable();
            $table->text('default_value')->nullable();
            $table->json('options')->nullable();
            $table->json('validation_rules')->nullable();
            $table->boolean('is_required')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_listable')->default(false);
            $table->boolean('is_public')->default(false);
            $table->boolean('is_filterable')->default(false);
            $table->boolean('is_searchable')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['property_type_id', 'field_key']);
            $table->index(['property_type_id', 'is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_fields');
    }
};
