<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_contact_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('shortcode')->unique();
            $table->boolean('is_active')->default(false);
            $table->text('success_message')->nullable();
            $table->boolean('show_phone')->default(true);
            $table->boolean('show_email')->default(true);
            $table->timestamps();

            $table->index('business_id');
            $table->unique(['business_id', 'shortcode']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_contact_forms');
    }
};
