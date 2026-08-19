<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_appointment_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('business_service_id')->constrained()->cascadeOnDelete();
            $table->foreignId('business_location_id')->nullable()->constrained()->nullOnDelete();
            $table->tinyInteger('day_of_week')->nullable();
            $table->date('specific_date')->nullable();
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_available')->default(true);
            $table->unsignedInteger('slots_available')->default(1);
            $table->timestamps();

            $table->index(['business_id', 'business_service_id', 'day_of_week'], 'slots_business_service_day_idx');
            $table->index(['business_id', 'specific_date'], 'slots_business_date_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_appointment_slots');
    }
};
