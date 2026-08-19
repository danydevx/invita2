<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_ai_settings', function (Blueprint $table) {
            $table->json('additional_preset_ids')->nullable()->after('preset_id');
        });
    }

    public function down(): void
    {
        Schema::table('business_ai_settings', function (Blueprint $table) {
            $table->dropColumn('additional_preset_ids');
        });
    }
};
