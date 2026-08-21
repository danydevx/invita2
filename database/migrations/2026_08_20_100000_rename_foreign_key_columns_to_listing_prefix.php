<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('listing_service_images', 'business_service_id')) {
            Schema::table('listing_service_images', function (Blueprint $table) {
                $table->renameColumn('business_service_id', 'listing_service_id');
            });
        }

        if (Schema::hasColumn('listing_product_images', 'business_product_id')) {
            Schema::table('listing_product_images', function (Blueprint $table) {
                $table->renameColumn('business_product_id', 'listing_product_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('listing_service_images', 'listing_service_id')) {
            Schema::table('listing_service_images', function (Blueprint $table) {
                $table->renameColumn('listing_service_id', 'business_service_id');
            });
        }

        if (Schema::hasColumn('listing_product_images', 'listing_product_id')) {
            Schema::table('listing_product_images', function (Blueprint $table) {
                $table->renameColumn('listing_product_id', 'business_product_id');
            });
        }
    }
};
