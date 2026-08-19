<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_social_networks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->enum('platform', [
                'facebook',
                'instagram',
                'twitter',
                'linkedin',
                'youtube',
                'tiktok',
                'whatsapp',
                'telegram',
                'pinterest',
                'snapchat',
                'threads',
                'reddit',
                'discord',
                'spotify',
            ]);
            $table->string('url', 500);
            $table->string('username', 100)->nullable();
            $table->string('icon_class', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('show_on_hero')->default(false);
            $table->boolean('show_on_footer')->default(true);
            $table->boolean('show_on_contact')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['business_id', 'platform']);
            $table->index('business_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_social_networks');
    }
};
