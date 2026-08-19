<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('general_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('general_field_section_id')->constrained()->cascadeOnDelete();
            $table->string('field_key');
            $table->string('field_type');
            $table->string('label');
            $table->text('description')->nullable();
            $table->string('help_text')->nullable();
            $table->string('placeholder')->nullable();
            $table->text('default_value')->nullable();
            $table->json('options')->nullable();
            $table->json('validation_rules')->nullable();
            $table->boolean('is_required')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['general_field_section_id', 'field_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('general_fields');
    }
};
