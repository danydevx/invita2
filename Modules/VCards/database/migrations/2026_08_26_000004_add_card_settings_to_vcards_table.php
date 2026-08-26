<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vcards', function (Blueprint $table) {
            $table->boolean('search_engine_indexing')->default(true)->after('active');
            $table->boolean('renew')->default(true)->after('search_engine_indexing');
            $table->json('tracking_code')->nullable()->after('renew');
            $table->boolean('paused')->default(false)->after('tracking_code');
        });
    }

    public function down(): void
    {
        Schema::table('vcards', function (Blueprint $table) {
            $table->dropColumn(['search_engine_indexing', 'renew', 'tracking_code', 'paused']);
        });
    }
};
