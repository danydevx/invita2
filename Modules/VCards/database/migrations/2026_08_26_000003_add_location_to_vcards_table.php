<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vcards', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('body_pattern_key');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->string('address', 255)->nullable()->after('longitude');
            $table->string('city', 100)->nullable()->after('address');
            $table->string('state', 100)->nullable()->after('city');
            $table->string('country', 100)->nullable()->after('state');
            $table->string('zip', 20)->nullable()->after('country');
        });
    }

    public function down(): void
    {
        Schema::table('vcards', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'address', 'city', 'state', 'country', 'zip']);
        });
    }
};
