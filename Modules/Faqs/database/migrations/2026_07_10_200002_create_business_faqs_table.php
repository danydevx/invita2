<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_faqs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()
                ->constrained('business_faq_categories')
                ->nullOnDelete();
            $table->string('question');
            $table->text('answer');
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('business_id');
            $table->index('category_id');
            $table->index(['business_id', 'is_active']);
            $table->index(['business_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_faqs');
    }
};
