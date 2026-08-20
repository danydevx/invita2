<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('short_description');
            $table->text('long_description')->nullable();
            $table->string('image')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('promo_price', 10, 2)->nullable();
            $table->string('whatsapp', 50)->nullable();
            $table->text('whatsapp_message')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['listing_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_packages');
    }
};
