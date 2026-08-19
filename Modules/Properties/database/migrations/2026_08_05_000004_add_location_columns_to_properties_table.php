<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->string('country', 2)->default('MX')->after('description');
            $table->string('state')->nullable()->after('country');
            $table->string('state_code', 10)->nullable()->after('state');
            $table->string('city')->nullable()->after('state_code');
            $table->string('municipality')->nullable()->after('city');
            $table->string('colony')->nullable()->after('municipality');
            $table->string('postal_code', 10)->nullable()->after('colony');
            $table->string('street')->nullable()->after('postal_code');
            $table->string('exterior_number', 20)->nullable()->after('street');
            $table->string('interior_number', 20)->nullable()->after('exterior_number');
            $table->text('references')->nullable()->after('interior_number');
            $table->decimal('latitude', 10, 7)->nullable()->after('references');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->boolean('show_exact_location')->default(false)->after('longitude');
        });

        Schema::table('properties', function (Blueprint $table) {
            $table->index('city');
            $table->index('state');
            $table->index('state_code');
            $table->index('country');
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropIndex(['city']);
            $table->dropIndex(['state']);
            $table->dropIndex(['state_code']);
            $table->dropIndex(['country']);

            $table->dropColumn([
                'country',
                'state',
                'state_code',
                'city',
                'municipality',
                'colony',
                'postal_code',
                'street',
                'exterior_number',
                'interior_number',
                'references',
                'latitude',
                'longitude',
                'show_exact_location',
            ]);
        });
    }
};
