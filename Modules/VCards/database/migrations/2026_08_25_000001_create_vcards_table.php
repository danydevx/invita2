<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vcards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vcard_team_id')->nullable()->constrained()->nullOnDelete();

            $table->enum('type', ['single', 'team'])->default('single');
            $table->string('name');
            $table->string('slug');
            $table->boolean('active')->default(true);

            $table->enum('design', ['classic', 'flat', 'modern', 'sleek', 'blend'])->default('classic');
            $table->string('primary_color', 7)->default('#2563EB');
            $table->string('font')->default('Inter');
            $table->string('profile_photo')->nullable();
            $table->string('logo')->nullable();
            $table->string('badge')->nullable();

            $table->string('prefix')->nullable();
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('accreditations')->nullable();
            $table->string('preferred_name')->nullable();
            $table->enum('pronouns', ['he', 'she', 'they'])->nullable();

            $table->string('title')->nullable();
            $table->string('department')->nullable();
            $table->string('company')->nullable();
            $table->text('headline')->nullable();

            $table->unsignedInteger('views')->default(0);
            $table->timestamps();

            $table->unique(['listing_id', 'slug']);
            $table->index('listing_id');
            $table->index('slug');
            $table->index(['listing_id', 'active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vcards');
    }
};
