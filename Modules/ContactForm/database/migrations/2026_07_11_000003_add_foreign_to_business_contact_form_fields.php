<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_contact_form_fields', function (Blueprint $table) {
            $table->foreignId('business_contact_form_id')
                ->nullable()
                ->after('listing_id')
                ->constrained('business_contact_forms')
                ->nullOnDelete();

            $table->dropUnique(['listing_id', 'field_name']);
            $table->unique(['business_contact_form_id', 'field_name'], 'business_contact_form_fields_unique');
        });
    }

    public function down(): void
    {
        Schema::table('business_contact_form_fields', function (Blueprint $table) {
            $table->dropForeign(['business_contact_form_id']);
            $table->dropColumn('business_contact_form_id');
            $table->unique(['listing_id', 'field_name']);
        });
    }
};
