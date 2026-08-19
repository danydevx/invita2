<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('subject');
            $table->text('message')->nullable();
            $table->string('status');
            $table->string('priority')->nullable();
            $table->string('category')->nullable();
            $table->timestamp('last_reply_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->index(['status']);
            $table->index(['priority']);
            $table->index(['category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};
