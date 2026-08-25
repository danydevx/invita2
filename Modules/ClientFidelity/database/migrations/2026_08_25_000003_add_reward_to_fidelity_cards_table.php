<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_fidelity_cards', function (Blueprint $table) {
            $table->foreignId('fidelity_reward_id')->nullable()->after('listing_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('client_fidelity_cards', function (Blueprint $table) {
            $table->dropForeign(['fidelity_reward_id']);
            $table->dropColumn('fidelity_reward_id');
        });
    }
};
