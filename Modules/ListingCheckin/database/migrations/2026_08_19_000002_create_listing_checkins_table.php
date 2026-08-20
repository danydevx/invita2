<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listing_checkins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->onDelete('cascade');
            $table->foreignId('guest_id')->constrained('listing_guests')->onDelete('cascade');
            $table->timestamp('checkin_time');
            $table->unsignedInteger('plus_ones_checked_in')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['listing_id', 'guest_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listing_checkins');
    }
};