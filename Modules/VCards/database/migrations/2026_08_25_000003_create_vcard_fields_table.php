<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vcard_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vcard_id')->constrained()->cascadeOnDelete();
            $table->string('field_type_key');
            $table->string('label')->nullable();
            $table->json('config');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->index('vcard_id');
            $table->index(['vcard_id', 'active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vcard_fields');
    }
};
