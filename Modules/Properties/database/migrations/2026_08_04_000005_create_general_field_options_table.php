<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('general_field_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('general_field_id')->constrained()->cascadeOnDelete();
            $table->string('value');
            $table->string('label');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['general_field_id', 'value']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('general_field_options');
    }
};
