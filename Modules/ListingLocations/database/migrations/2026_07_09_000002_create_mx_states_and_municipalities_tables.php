<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mx_states', function (Blueprint $table) {
            $table->string('code', 10)->primary();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('mx_municipalities', function (Blueprint $table) {
            $table->id();
            $table->string('state_code', 10);
            $table->string('code', 20);
            $table->string('name');
            $table->boolean('is_metropolitan')->default(false);
            $table->timestamps();

            $table->unique(['state_code', 'code']);
            $table->foreign('state_code')->references('code')->on('mx_states')->cascadeOnDelete();
            $table->index('state_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mx_municipalities');
        Schema::dropIfExists('mx_states');
    }
};
