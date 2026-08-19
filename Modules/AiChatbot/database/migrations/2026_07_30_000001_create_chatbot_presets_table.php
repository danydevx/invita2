<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chatbot_presets', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->text('description')->nullable();
            $table->string('business_type', 50)->nullable();
            $table->text('system_prompt_template');
            $table->string('chatbot_name_template', 150)->nullable();
            $table->text('greeting_message')->nullable();
            $table->text('fallback_message')->nullable();
            $table->enum('personality', ['professional', 'friendly', 'formal', 'casual'])->default('friendly');
            $table->string('language', 10)->default('es');
            $table->json('configuration')->nullable();
            $table->json('initial_suggestions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_system')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['is_active', 'is_system']);
            $table->index('business_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_presets');
    }
};
