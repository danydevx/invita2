<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_embeddings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->onDelete('cascade');
            $table->string('source_type', 50);
            $table->unsignedBigInteger('source_id');
            $table->text('chunk_text');
            $table->longText('embedding');
            $table->timestamps();

            $table->index(['listing_id', 'source_type']);
            $table->index(['listing_id', 'source_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_embeddings');
    }
};
