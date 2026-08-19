<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_announcement_states', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('system_announcement_id')->constrained('system_announcements')->cascadeOnDelete();
            $table->timestamp('dismissed_at')->nullable();
            $table->timestamp('seen_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'system_announcement_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_announcement_states');
    }
};
