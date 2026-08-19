<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('industry_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('industry_id')->constrained('industries')->cascadeOnDelete();
            $table->foreignId('module_definition_id')->constrained('business_module_definitions')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['industry_id', 'module_definition_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('industry_modules');
    }
};
