<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chatbot_presets', function (Blueprint $table) {
            $table->json('context_ids')->nullable()->after('initial_suggestions');
        });
    }

    public function down(): void
    {
        Schema::table('chatbot_presets', function (Blueprint $table) {
            $table->dropColumn('context_ids');
        });
    }
};
