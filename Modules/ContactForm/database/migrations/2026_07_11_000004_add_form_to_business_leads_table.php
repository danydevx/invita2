<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_leads', function (Blueprint $table) {
            $table->foreignId('business_contact_form_id')
                ->nullable()
                ->after('business_id')
                ->constrained('business_contact_forms')
                ->nullOnDelete();

            $table->index('business_contact_form_id');
        });
    }

    public function down(): void
    {
        Schema::table('business_leads', function (Blueprint $table) {
            $table->dropForeign(['business_contact_form_id']);
            $table->dropColumn('business_contact_form_id');
        });
    }
};
