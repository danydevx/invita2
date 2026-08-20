<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_ai_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->onDelete('cascade');
            $table->enum('provider', ['openai', 'minimax'])->default('openai');
            $table->text('api_key');
            $table->string('model', 100)->default('gpt-4o-mini');
            $table->string('embedding_model', 100)->default('text-embedding-3-small');
            $table->text('system_prompt')->nullable();
            $table->integer('max_conversations_month')->default(500);
            $table->integer('max_messages_conversation')->default(50);
            $table->integer('max_tokens_response')->default(500);
            $table->string('widget_color', 7)->default('#3B82F6');
            $table->boolean('is_enabled')->default(false);
            $table->timestamps();

            $table->unique('listing_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_ai_settings');
    }
};
