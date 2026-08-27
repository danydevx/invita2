<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vcard_selected_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vcard_id')->constrained()->cascadeOnDelete();
            $table->foreignId('package_id')->constrained('listing_packages')->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['vcard_id', 'package_id']);
            $table->index(['vcard_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vcard_selected_packages');
    }
};
