<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_ai_settings', function (Blueprint $table) {
            $table->enum('widget_theme', ['light', 'dark'])->default('light')->after('widget_color');
        });
    }

    public function down(): void
    {
        Schema::table('business_ai_settings', function (Blueprint $table) {
            $table->dropColumn('widget_theme');
        });
    }
};
