<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listing_guests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->enum('rsvp_status', ['pending', 'confirmed', 'declined', 'maybe'])->default('pending');
            $table->unsignedInteger('plus_ones')->default(0);
            $table->text('notes')->nullable();
            $table->string('confirmation_token')->unique()->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('declined_at')->nullable();
            $table->timestamps();

            $table->index(['listing_id', 'rsvp_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listing_guests');
    }
};