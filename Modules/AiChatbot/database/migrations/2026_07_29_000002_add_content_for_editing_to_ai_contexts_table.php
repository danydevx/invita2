<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_contexts', function (Blueprint $table) {
            $table->text('content_for_editing')->nullable()->after('content');
        });
    }

    public function down(): void
    {
        Schema::table('ai_contexts', function (Blueprint $table) {
            $table->dropColumn('content_for_editing');
        });
    }
};
