<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_ai_settings', function (Blueprint $table) {
            $table->string('chatbot_name', 100)->nullable()->after('system_prompt');
            $table->string('chatbot_avatar', 500)->nullable()->after('chatbot_name');
        });
    }

    public function down(): void
    {
        Schema::table('business_ai_settings', function (Blueprint $table) {
            $table->dropColumn(['chatbot_name', 'chatbot_avatar']);
        });
    }
};
