<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_availability_exceptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->date('exception_date');
            $table->boolean('is_available')->default(false);
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('reason')->nullable();
            $table->timestamps();

            $table->unique(['business_id', 'exception_date'], 'availability_exceptions_business_date_unique');
            $table->index('exception_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_availability_exceptions');
    }
};