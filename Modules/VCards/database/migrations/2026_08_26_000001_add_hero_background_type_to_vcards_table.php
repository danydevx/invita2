<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vcards', function (Blueprint $table) {
            $table->enum('background_type', ['solid', 'gradient', 'pattern'])
                  ->default('solid')
                  ->after('hero_background_image');

            $table->string('gradient_direction', 20)
                  ->default('135deg')
                  ->after('background_type');

            $table->string('pattern_key', 50)
                  ->nullable()
                  ->after('gradient_direction');

            $table->unsignedTinyInteger('hero_image_alpha')
                  ->default(100)
                  ->after('pattern_key');
        });
    }

    public function down(): void
    {
        Schema::table('vcards', function (Blueprint $table) {
            $table->dropColumn(['background_type', 'gradient_direction', 'pattern_key']);
        });
    }
};
