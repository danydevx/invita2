<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_seo_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->unique()->constrained()->cascadeOnDelete();

            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->string('focus_keyword')->nullable();

            $table->boolean('allow_indexing')->default(true);
            $table->boolean('follow_links')->default(true);
            $table->boolean('include_in_sitemap')->default(true);
            $table->string('canonical_url')->nullable();

            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image')->nullable();
            $table->string('og_image_alt')->nullable();

            $table->boolean('schema_enabled')->default(true);
            $table->string('schema_type')->default('LocalBusiness');

            $table->json('settings')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_seo_settings');
    }
};
