<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $table->enum('order_type', ['delivery', 'pickup', 'both'])->default('both');
            $table->decimal('delivery_radius_km', 8, 2)->default(10);
            $table->decimal('delivery_fee_base', 10, 2)->default(30);
            $table->decimal('delivery_fee_per_km', 10, 2)->default(3);
            $table->decimal('free_delivery_threshold', 10, 2)->nullable();
            $table->decimal('min_order_amount', 10, 2)->default(0);
            $table->string('whatsapp_number', 20)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_settings');
    }
};
