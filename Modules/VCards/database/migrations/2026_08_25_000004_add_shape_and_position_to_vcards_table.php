<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vcards', function (Blueprint $table) {
            $table->string('shape', 20)->default('rounded')->after('badge');
            $table->integer('image_x')->default(0)->after('shape');
            $table->integer('image_y')->default(0)->after('image_x');
        });
    }

    public function down(): void
    {
        Schema::table('vcards', function (Blueprint $table) {
            $table->dropColumn(['shape', 'image_x', 'image_y']);
        });
    }
};
