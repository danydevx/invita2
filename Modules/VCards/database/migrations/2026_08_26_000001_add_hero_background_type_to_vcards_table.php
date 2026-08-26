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

            $table->enum('body_background_type', ['solid', 'gradient', 'pattern'])
                  ->default('solid')
                  ->after('hero_image_alpha');

            $table->string('body_primary_color', 7)
                  ->default('#ffffff')
                  ->after('body_background_type');

            $table->string('body_gradient_direction', 20)
                  ->default('135deg')
                  ->after('body_primary_color');

            $table->string('body_pattern_key', 50)
                  ->nullable()
                  ->after('body_gradient_direction');
        });
    }

    public function down(): void
    {
        Schema::table('vcards', function (Blueprint $table) {
            $table->dropColumn([
                'background_type',
                'gradient_direction',
                'pattern_key',
                'hero_image_alpha',
                'body_background_type',
                'body_primary_color',
                'body_gradient_direction',
                'body_pattern_key',
            ]);
        });
    }
};
