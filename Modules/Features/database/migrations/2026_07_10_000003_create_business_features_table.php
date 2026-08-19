<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('feature_id')->constrained()->cascadeOnDelete();
            $table->foreignId('location_id')->nullable()->constrained('business_locations')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['business_id', 'feature_id', 'location_id']);
            $table->index(['business_id', 'is_active']);
            $table->index('location_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_features');
    }
};
