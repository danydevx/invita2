<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_availability', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('day_of_week');
            $table->boolean('is_available')->default(false);
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->unsignedInteger('slot_duration_minutes')->default(30);
            $table->unsignedInteger('slots_per_slot')->default(1);
            $table->timestamps();

            $table->unique(['business_id', 'day_of_week'], 'availability_business_day_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_availability');
    }
};