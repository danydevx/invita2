<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vcard_selected_testimonials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vcard_id')->constrained('vcards')->onDelete('cascade');
            $table->foreignId('review_id')->constrained('listing_reviews')->onDelete('cascade');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['vcard_id', 'review_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vcard_selected_testimonials');
    }
};
