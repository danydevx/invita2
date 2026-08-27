<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vcard_selected_galleries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vcard_id')->constrained()->cascadeOnDelete();
            $table->foreignId('gallery_id')->constrained('listing_galleries')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['vcard_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vcard_selected_galleries');
    }
};
