<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('minisite_themes', function (Blueprint $table) {
            $table->json('section_config')->nullable()->after('layout_config');
        });
    }

    public function down(): void
    {
        Schema::table('minisite_themes', function (Blueprint $table) {
            $table->dropColumn('section_config');
        });
    }
};
