<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chatbot_presets', function (Blueprint $table) {
            $table->foreignId('listing_id')->nullable()->after('is_system')->constrained('businesses')->nullOnDelete();
            $table->index(['listing_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::table('chatbot_presets', function (Blueprint $table) {
            $table->dropForeign(['listing_id']);
            $table->dropColumn('listing_id');
        });
    }
};
