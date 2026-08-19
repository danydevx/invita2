<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_ai_settings', function (Blueprint $table) {
            $table->boolean('cta_enabled')->default(false)->after('show_citations');
            $table->string('cta_primary_text', 100)->nullable()->after('cta_enabled');
            $table->string('cta_primary_url', 500)->nullable()->after('cta_primary_text');
            $table->string('cta_secondary_text', 100)->nullable()->after('cta_primary_url');
            $table->string('cta_secondary_url', 500)->nullable()->after('cta_secondary_text');
        });
    }

    public function down(): void
    {
        Schema::table('business_ai_settings', function (Blueprint $table) {
            $table->dropColumn(['cta_enabled', 'cta_primary_text', 'cta_primary_url', 'cta_secondary_text', 'cta_secondary_url']);
        });
    }
};
