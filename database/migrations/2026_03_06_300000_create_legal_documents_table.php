<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('legal_documents', function (Blueprint $table) {
            $table->id();
            $table->string('key', 150)->unique();
            $table->string('title', 200);
            $table->longText('content');
            $table->unsignedInteger('version')->default(1);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_required')->default(true);
            $table->boolean('requires_reaccept')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('legal_documents');
    }
};
