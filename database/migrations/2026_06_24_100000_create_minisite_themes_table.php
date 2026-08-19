<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('minisite_themes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('preview_image')->nullable();
            $table->json('css_variables');
            $table->json('layout_config');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('businesses', function (Blueprint $table) {
            $table->foreignId('minisite_theme_id')->nullable()->after('settings')->constrained('minisite_themes')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropForeign(['minisite_theme_id']);
            $table->dropColumn('minisite_theme_id');
        });

        Schema::dropIfExists('minisite_themes');
    }
};
