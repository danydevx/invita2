<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_embeddings', function (Blueprint $table) {
            $table->string('content_hash', 64)->nullable()->after('chunk_text');
            $table->index(['business_id', 'content_hash']);
        });
    }

    public function down(): void
    {
        Schema::table('ai_embeddings', function (Blueprint $table) {
            $table->dropIndex(['business_id', 'content_hash']);
            $table->dropColumn('content_hash');
        });
    }
};
