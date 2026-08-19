<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_module_definitions', function (Blueprint $table) {
            $table->string('image')->nullable()->after('icon')->comment('Image URL for module illustration');
        });
    }

    public function down(): void
    {
        Schema::table('business_module_definitions', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }
};
