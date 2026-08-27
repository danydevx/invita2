<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vcard_business_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vcard_id')->constrained('vcards')->onDelete('cascade');
            $table->tinyInteger('day_of_week')->comment('0=Sunday, 1=Monday, ..., 6=Saturday');
            $table->boolean('is_open')->default(true);
            $table->string('opening_time', 5)->default('08:00')->comment('HH:MM format');
            $table->string('closing_time', 5)->default('18:00')->comment('HH:MM format');
            $table->string('lunch_start_time', 5)->nullable()->comment('HH:MM format');
            $table->string('lunch_end_time', 5)->nullable()->comment('HH:MM format');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['vcard_id', 'day_of_week']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vcard_business_hours');
    }
};
