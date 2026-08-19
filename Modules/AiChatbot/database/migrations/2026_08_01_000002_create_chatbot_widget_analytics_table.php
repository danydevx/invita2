<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chatbot_widget_analytics', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_key')->index();
            $table->string('domain', 255);
            $table->string('event_type', 50);
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['public_key', 'event_type']);
            $table->index(['public_key', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_widget_analytics');
    }
};
