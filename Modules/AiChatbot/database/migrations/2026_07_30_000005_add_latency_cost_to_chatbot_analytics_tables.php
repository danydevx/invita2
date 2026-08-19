<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chatbot_analytics', function (Blueprint $table) {
            $table->unsignedInteger('total_latency_ms')->default(0)->after('tokens_used');
            $table->decimal('estimated_cost', 10, 6)->default(0)->after('total_latency_ms');
        });

        Schema::table('chatbot_top_questions', function (Blueprint $table) {
            $table->boolean('low_confidence')->default(false)->after('times_asked');
            $table->boolean('no_answer')->default(false)->after('low_confidence');
        });
    }

    public function down(): void
    {
        Schema::table('chatbot_analytics', function (Blueprint $table) {
            $table->dropColumn(['total_latency_ms', 'estimated_cost']);
        });

        Schema::table('chatbot_top_questions', function (Blueprint $table) {
            $table->dropColumn(['low_confidence', 'no_answer']);
        });
    }
};
