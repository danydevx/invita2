<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fidelity_card_completions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_fidelity_card_id');
            $table->unsignedBigInteger('fidelity_reward_id')->nullable();
            $table->string('client_name');
            $table->string('client_email')->nullable();
            $table->string('client_phone', 50)->nullable();
            $table->unsignedInteger('visits_completed');
            $table->unsignedBigInteger('completed_by_user_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('client_fidelity_card_id', 'fk_completions_card')
                ->references('id')->on('client_fidelity_cards')->onDelete('cascade');
            $table->foreign('fidelity_reward_id', 'fk_completions_reward')
                ->references('id')->on('fidelity_rewards')->onDelete('cascade');
            $table->foreign('completed_by_user_id', 'fk_completions_user')
                ->references('id')->on('users')->onDelete('set null');

            $table->index('client_fidelity_card_id');
            $table->index('created_at');
            $table->index(['client_name', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fidelity_card_completions');
    }
};
