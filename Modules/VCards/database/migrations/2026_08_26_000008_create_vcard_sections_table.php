<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vcard_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vcard_id')->constrained()->cascadeOnDelete();
            $table->string('section_key', 50);
            $table->boolean('enabled')->default(true);
            $table->timestamps();

            $table->unique(['vcard_id', 'section_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vcard_sections');
    }
};
