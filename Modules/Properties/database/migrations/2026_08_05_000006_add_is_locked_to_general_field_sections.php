<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Properties\Models\GeneralFieldSection;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('general_field_sections', function (Blueprint $table) {
            $table->boolean('is_locked')->default(false)->after('is_active');
        });

        GeneralFieldSection::whereIn('slug', [
            'informacion-principal',
            'operacion-y-precio',
        ])->update(['is_locked' => true]);
    }

    public function down(): void
    {
        Schema::table('general_field_sections', function (Blueprint $table) {
            $table->dropColumn('is_locked');
        });
    }
};
