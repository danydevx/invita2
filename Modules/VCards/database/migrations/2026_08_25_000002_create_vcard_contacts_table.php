<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vcard_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vcard_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['phone', 'email', 'whatsapp']);
            $table->enum('contact_type', ['personal', 'work', 'home'])->default('personal');
            $table->string('country_code', 10)->nullable();
            $table->string('value');
            $table->string('extension', 20)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('vcard_id');
            $table->index(['vcard_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vcard_contacts');
    }
};
