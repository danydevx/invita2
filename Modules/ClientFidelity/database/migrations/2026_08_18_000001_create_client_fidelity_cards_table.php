<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_fidelity_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->onDelete('cascade');
            $table->string('client_name');
            $table->string('client_email')->nullable();
            $table->string('client_phone', 50)->nullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('max_visits')->default(10);
            $table->unsignedInteger('current_visits')->default(10);
            $table->string('public_code', 15)->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamp('completed_at')->nullable();
            $table->unsignedInteger('reset_count')->default(0);
            $table->timestamps();

            $table->index(['listing_id', 'is_active']);
            $table->index('public_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_fidelity_cards');
    }
};
