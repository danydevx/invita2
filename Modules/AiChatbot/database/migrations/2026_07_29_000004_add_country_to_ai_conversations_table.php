<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_conversations', function (Blueprint $table) {
            $table->string('country', 100)->nullable()->after('user_agent');
            $table->string('city', 100)->nullable()->after('country');
            $table->string('country_code', 2)->nullable()->after('country');
        });
    }

    public function down(): void
    {
        Schema::table('ai_conversations', function (Blueprint $table) {
            $table->dropColumn(['country', 'city', 'country_code']);
        });
    }
};
