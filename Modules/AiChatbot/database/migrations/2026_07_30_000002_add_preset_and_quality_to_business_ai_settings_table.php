<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_ai_settings', function (Blueprint $table) {
            $table->foreignId('preset_id')->nullable()->after('business_id')
                  ->constrained('chatbot_presets')->nullOnDelete();
            $table->enum('personality', ['professional', 'friendly', 'formal', 'casual'])
                  ->default('friendly')->after('chatbot_avatar');
            $table->enum('response_length', ['short', 'medium', 'long'])
                  ->default('medium')->after('personality');
            $table->boolean('expandable_responses')->default(true)->after('response_length');
            $table->boolean('show_citations')->default(true)->after('expandable_responses');
        });
    }

    public function down(): void
    {
        Schema::table('business_ai_settings', function (Blueprint $table) {
            $table->dropForeign(['preset_id']);
            $table->dropColumn([
                'preset_id',
                'personality',
                'response_length',
                'expandable_responses',
                'show_citations',
            ]);
        });
    }
};
