<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chatbot_widgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->unique()->constrained()->onDelete('cascade');
            $table->uuid('public_key')->unique();
            $table->string('allowed_domain', 255)->nullable();
            $table->boolean('is_enabled')->default(false);
            $table->string('version', 20)->default('1.0.0');
            $table->timestamps();

            $table->index(['public_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_widgets');
    }
};
