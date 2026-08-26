<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vcards', function (Blueprint $table) {
            $table->string('meta_pixel_id', 255)->nullable()->after('paused');
            $table->string('google_analytics_id', 255)->nullable()->after('meta_pixel_id');
            $table->string('google_webmasters_verification', 255)->nullable()->after('google_analytics_id');
            $table->string('bing_webmasters_verification', 255)->nullable()->after('google_webmasters_verification');
        });
    }

    public function down(): void
    {
        Schema::table('vcards', function (Blueprint $table) {
            $table->dropColumn([
                'meta_pixel_id',
                'google_analytics_id',
                'google_webmasters_verification',
                'bing_webmasters_verification',
            ]);
        });
    }
};
