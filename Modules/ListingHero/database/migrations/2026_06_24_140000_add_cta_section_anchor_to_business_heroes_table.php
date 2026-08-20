<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_heroes', function (Blueprint $table) {
            $table->string('cta_section_anchor')->nullable()->after('buttons');
        });
    }

    public function down(): void
    {
        Schema::table('business_heroes', function (Blueprint $table) {
            $table->dropColumn('cta_section_anchor');
        });
    }
};
