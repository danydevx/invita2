<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('automation_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('automation_id')->constrained('automations')->cascadeOnDelete();
            $table->string('event_key', 150);
            $table->string('status', 20);
            $table->timestamp('executed_at');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['event_key', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('automation_runs');
    }
};
