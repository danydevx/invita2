<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payment_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('subscription_id')->nullable()->constrained()->nullOnDelete();
            $table->string('number')->nullable();
            $table->string('type')->default('receipt');
            $table->string('status');
            $table->decimal('amount', 12, 2)->nullable();
            $table->string('currency', 10)->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('due_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_url', 500)->nullable();
            $table->string('provider_reference', 150)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['status']);
            $table->index(['type']);
            $table->index(['issued_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
