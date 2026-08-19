<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_heroes', function (Blueprint $table) {
            $table->text('text_aux')->nullable()->after('subtitle');
            $table->string('background_image_path')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('business_heroes', function (Blueprint $table) {
            $table->dropColumn('text_aux');
        });
    }
};
