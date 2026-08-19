<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->text('message');
            $table->string('type', 20);
            $table->string('audience', 20);
            $table->boolean('is_active')->default(false);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('dismissible')->default(true);
            $table->string('priority', 20)->nullable();
            $table->string('action_label', 100)->nullable();
            $table->string('action_url', 500)->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['is_active', 'starts_at', 'ends_at']);
            $table->index(['audience', 'priority']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_announcements');
    }
};
