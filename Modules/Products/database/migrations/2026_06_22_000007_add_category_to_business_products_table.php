<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_products', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('business_id')->constrained('business_product_categories')->onDelete('set null');
            $table->index('category_id');
        });
    }

    public function down(): void
    {
        Schema::table('business_products', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropIndex(['category_id']);
            $table->dropColumn('category_id');
        });
    }
};
