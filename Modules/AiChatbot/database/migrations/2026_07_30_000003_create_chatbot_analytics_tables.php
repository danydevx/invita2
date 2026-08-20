<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chatbot_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->unsignedInteger('conversations_count')->default(0);
            $table->unsignedInteger('messages_count')->default(0);
            $table->unsignedInteger('tokens_used')->default(0);
            $table->unsignedInteger('errors_count')->default(0);
            $table->timestamps();

            $table->unique(['listing_id', 'date']);
            $table->index(['listing_id', 'date']);
        });

        Schema::create('chatbot_top_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $table->string('question', 500);
            $table->string('question_hash', 64);
            $table->unsignedInteger('times_asked')->default(1);
            $table->date('last_asked_at');
            $table->timestamps();

            $table->index(['listing_id', 'times_asked']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_top_questions');
        Schema::dropIfExists('chatbot_analytics');
    }
};
