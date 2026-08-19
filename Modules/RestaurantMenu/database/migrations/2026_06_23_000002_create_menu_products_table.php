<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained('menu_categories')->onDelete('cascade');
            $table->string('image')->nullable();
            $table->string('title');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->decimal('base_price', 10, 2)->nullable();
            $table->boolean('show_price')->default(true);
            $table->boolean('featured')->default(false);
            $table->boolean('active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['business_id', 'slug']);
            $table->index(['business_id', 'category_id']);
            $table->index(['business_id', 'active']);
            $table->index(['business_id', 'featured']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_products');
    }
};