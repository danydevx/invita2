<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE listings DROP FOREIGN KEY businesses_industry_id_foreign');
        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn('industry_id');
        });

        DB::statement('ALTER TABLE users DROP FOREIGN KEY users_industry_id_foreign');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('industry_id');
        });

        Schema::dropIfExists('industry_modules');
        Schema::dropIfExists('industries');
    }

    public function down(): void
    {
        Schema::create('industries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('industry_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('industry_id')->constrained()->cascadeOnDelete();
            $table->foreignId('module_definition_id')->constrained('listing_module_definitions')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['industry_id', 'module_definition_id']);
        });

        Schema::table('listings', function (Blueprint $table) {
            $table->foreignId('industry_id')->nullable()->constrained()->nullOnDelete();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('industry_id')->nullable()->constrained()->nullOnDelete();
        });
    }
};
