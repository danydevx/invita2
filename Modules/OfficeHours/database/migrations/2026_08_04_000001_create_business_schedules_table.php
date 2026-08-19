<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('business_location_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->json('days_of_week')->nullable();
            $table->string('opening_time', 5);
            $table->string('closing_time', 5);
            $table->string('lunch_start_time', 5)->nullable();
            $table->string('lunch_end_time', 5)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('business_id');
            $table->index('business_location_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_schedules');
    }
};
