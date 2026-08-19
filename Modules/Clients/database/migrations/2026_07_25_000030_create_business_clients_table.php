<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();

            $table->string('contact_person')->nullable();
            $table->string('company_name')->nullable();
            $table->string('whatsapp', 50)->nullable();
            $table->string('website')->nullable();
            $table->string('rfc', 20)->nullable();
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('neighborhood')->nullable();
            $table->string('postal_code', 20)->nullable();

            $table->string('state_code', 10)->nullable();
            $table->string('municipality')->nullable();

            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->string('customer_phone', 50)->nullable();

            $table->string('status')->default('pending');

            $table->text('notes')->nullable();

            $table->foreign('state_code')
                ->references('code')->on('mx_states')
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index('company_name');
            $table->index('customer_email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_clients');
    }
};
