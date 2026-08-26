<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vcard_seo_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vcard_id')->constrained('vcards')->onDelete('cascade');
            $table->string('seo_title', 255)->nullable();
            $table->string('seo_description', 500)->nullable();
            $table->string('focus_keyword', 255)->nullable();
            $table->boolean('allow_indexing')->default(true);
            $table->boolean('follow_links')->default(true);
            $table->boolean('include_in_sitemap')->default(true);
            $table->string('canonical_url', 255)->nullable();
            $table->string('og_title', 255)->nullable();
            $table->string('og_description', 500)->nullable();
            $table->string('og_image', 500)->nullable();
            $table->string('og_image_alt', 255)->nullable();
            $table->boolean('schema_enabled')->default(false);
            $table->string('schema_type', 100)->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();

            $table->unique('vcard_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vcard_seo_settings');
    }
};
