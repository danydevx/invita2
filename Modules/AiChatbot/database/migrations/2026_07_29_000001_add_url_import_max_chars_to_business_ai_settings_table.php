<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_ai_settings', function (Blueprint $table) {
            $table->integer('url_import_max_chars')->default(5000)->after('allow_reset_chat');
        });
    }

    public function down(): void
    {
        Schema::table('business_ai_settings', function (Blueprint $table) {
            $table->dropColumn('url_import_max_chars');
        });
    }
};
