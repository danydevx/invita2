<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string('provider')->nullable()->after('billing_period');
            $table->string('provider_reference')->nullable()->after('provider');
            $table->string('provider_customer_id')->nullable()->after('provider_reference');
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn(['provider', 'provider_reference', 'provider_customer_id']);
        });
    }
};
