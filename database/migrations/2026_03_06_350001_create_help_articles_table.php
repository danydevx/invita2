<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('help_articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('excerpt', 500)->nullable();
            $table->text('content');
            $table->string('category')->nullable();
            $table->boolean('is_published')->default(false);
            $table->integer('sort_order')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['is_published']);
            $table->index(['category']);
            $table->index(['published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('help_articles');
    }
};
