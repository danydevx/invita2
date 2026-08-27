<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vcard_selected_menu_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vcard_id')->constrained('vcards')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('menu_categories')->onDelete('cascade');
            $table->json('product_ids')->nullable()->comment('Array of selected product IDs (max 5)');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['vcard_id', 'category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vcard_selected_menu_categories');
    }
};
