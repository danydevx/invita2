<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listing_service_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['listing_id', 'slug']);
            $table->index(['listing_id', 'is_active']);
        });

        Schema::table('listing_services', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('listing_id')->constrained('listing_service_categories')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('listing_services', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });

        Schema::dropIfExists('listing_service_categories');
    }
};
