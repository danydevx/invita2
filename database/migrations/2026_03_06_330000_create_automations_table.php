<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('automations', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('event_key', 150);
            $table->string('action_key', 150);
            $table->boolean('is_active')->default(true);
            $table->json('config')->nullable();
            $table->timestamps();

            $table->index(['event_key', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('automations');
    }
};
