<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->string('whatsapp', 20)->nullable()->after('phone');
            $table->string('whatsapp_country', 5)->nullable()->after('whatsapp');
            $table->string('personal_email', 150)->nullable()->after('x');
            $table->string('country', 5)->nullable()->after('personal_email');
        });
    }

    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn(['whatsapp', 'whatsapp_country', 'personal_email', 'country']);
        });
    }
};
