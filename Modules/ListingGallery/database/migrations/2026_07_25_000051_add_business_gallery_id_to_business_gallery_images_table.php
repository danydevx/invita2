<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $columnExists = collect(DB::select(
            "SHOW COLUMNS FROM business_gallery_images WHERE Field = ?",
            ['business_gallery_id']
        ))->isNotEmpty();

        $indexExists = collect(DB::select(
            "SHOW INDEXES FROM business_gallery_images WHERE Key_name = ?",
            ['bgi_business_gallery_sort_idx']
        ))->isNotEmpty();

        $foreignExists = collect(DB::select(
            "SELECT CONSTRAINT_NAME AS name FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_NAME = ?",
            ['business_gallery_images', 'business_gallery_images_business_gallery_id_foreign']
        ))->isNotEmpty();

        Schema::table('business_gallery_images', function (Blueprint $table) use ($columnExists, $indexExists, $foreignExists) {
            if (! $columnExists) {
                $table->foreignId('business_gallery_id')
                    ->nullable()
                    ->after('listing_id');
            }

            if (! $foreignExists) {
                $table->foreign('business_gallery_id')
                    ->references('id')->on('business_galleries')
                    ->cascadeOnDelete();
            }

            if (! $indexExists) {
                $table->index(
                    ['listing_id', 'business_gallery_id', 'sort_order'],
                    'bgi_business_gallery_sort_idx'
                );
            }
        });
    }

    public function down(): void
    {
        Schema::table('business_gallery_images', function (Blueprint $table) {
            $table->dropForeign(['business_gallery_id']);
            $table->dropIndex('bgi_business_gallery_sort_idx');
            $table->dropColumn('business_gallery_id');
        });
    }
};
