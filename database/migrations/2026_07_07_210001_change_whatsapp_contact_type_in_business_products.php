<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_products', function (Blueprint $table) {
            $table->string('whatsapp_contact', 50)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('business_products', function (Blueprint $table) {
            $table->boolean('whatsapp_contact')->default(false)->change();
        });
    }
};
