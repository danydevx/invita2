<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_module_definitions', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->comment('Unique identifier: locations, gallery, appointments, etc.');
            $table->string('name')->comment('Display name in Spanish');
            $table->text('description')->nullable();
            $table->string('icon')->nullable()->comment('Bootstrap icon class');
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('has_settings')->default(false)->comment('Whether module has custom settings');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_module_definitions');
    }
};
