<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('property_field_sections', function (Blueprint $table) {
            $table->boolean('is_general')->default(false)->after('description');
            $table->foreignId('general_field_section_id')->nullable()->after('is_general')->constrained()->nullOnDelete();
        });

        Schema::table('property_fields', function (Blueprint $table) {
            $table->foreignId('general_field_id')->nullable()->after('property_type_id')->constrained()->nullOnDelete();
            $table->boolean('is_inherited')->default(false)->after('general_field_id');
        });
    }

    public function down(): void
    {
        Schema::table('property_fields', function (Blueprint $table) {
            $table->dropForeign(['general_field_id']);
            $table->dropColumn(['general_field_id', 'is_inherited']);
        });

        Schema::table('property_field_sections', function (Blueprint $table) {
            $table->dropForeign(['general_field_section_id']);
            $table->dropColumn(['is_general', 'general_field_section_id']);
        });
    }
};
