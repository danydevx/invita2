<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_availability_exceptions', function (Blueprint $table) {
            $table->unsignedInteger('slots_per_slot')->nullable()->after('end_time');
        });
    }

    public function down(): void
    {
        Schema::table('business_availability_exceptions', function (Blueprint $table) {
            $table->dropColumn('slots_per_slot');
        });
    }
};