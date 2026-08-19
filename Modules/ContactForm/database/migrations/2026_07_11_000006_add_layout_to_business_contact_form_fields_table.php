<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_contact_form_fields', function (Blueprint $table) {
            $table->integer('row')->default(1)->after('order');
            $table->string('width', 20)->default('full')->after('row');
        });
    }

    public function down(): void
    {
        Schema::table('business_contact_form_fields', function (Blueprint $table) {
            $table->dropColumn(['row', 'width']);
        });
    }
};
