<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_minisite_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->onDelete('cascade');
            $table->string('section_type', 50);
            $table->string('section_key', 100);
            $table->string('title')->nullable();
            $table->json('config')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['listing_id', 'section_key']);
            $table->index(['listing_id', 'section_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_minisite_sections');
    }
};
