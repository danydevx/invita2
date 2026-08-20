<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_ai_settings', function (Blueprint $table) {
            $table->float('rag_min_similarity', 4, 3)->default(0.250)->after('url_import_max_chars');
            $table->unsignedInteger('rag_max_results')->default(5)->after('rag_min_similarity');
        });
    }

    public function down(): void
    {
        Schema::table('business_ai_settings', function (Blueprint $table) {
            $table->dropColumn(['rag_min_similarity', 'rag_max_results']);
        });
    }
};
