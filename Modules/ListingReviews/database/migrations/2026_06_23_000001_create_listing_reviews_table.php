<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listing_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $table->foreignId('business_location_id')->nullable()->constrained()->nullOnDelete();
            $table->string('client_name');
            $table->string('company')->nullable();
            $table->text('comment')->nullable();
            $table->tinyInteger('rating');
            $table->string('google_link')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('listing_id');
            $table->index('business_location_id');
            $table->index('rating');
            $table->index(['listing_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listing_reviews');
    }
};
