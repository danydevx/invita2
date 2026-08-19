<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_embeddings', function (Blueprint $table) {
            $table->string('source_id', 100)->change();
        });
    }

    public function down(): void
    {
        Schema::table('ai_embeddings', function (Blueprint $table) {
            $table->unsignedBigInteger('source_id')->change();
        });
    }
};
