<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $settings = [
            ['key' => 'billing.stripe_enabled', 'value' => '0', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'billing.stripe_key', 'value' => '', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'billing.stripe_secret', 'value' => '', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'billing.stripe_mode', 'value' => 'sandbox', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'billing.paypal_enabled', 'value' => '0', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'billing.paypal_client_id', 'value' => '', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'billing.paypal_secret', 'value' => '', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'billing.paypal_mode', 'value' => 'sandbox', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'billing.mercadopago_enabled', 'value' => '0', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'billing.mercadopago_public_key', 'value' => '', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'billing.mercadopago_access_token', 'value' => '', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'billing.mercadopago_mode', 'value' => 'sandbox', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'billing.manual_enabled', 'value' => '0', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'billing.manual_payment_guide', 'value' => '', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('settings')->insert($settings);
    }

    public function down(): void
    {
        DB::table('settings')->whereIn('key', [
            'billing.stripe_enabled',
            'billing.stripe_key',
            'billing.stripe_secret',
            'billing.stripe_mode',
            'billing.paypal_enabled',
            'billing.paypal_client_id',
            'billing.paypal_secret',
            'billing.paypal_mode',
            'billing.mercadopago_enabled',
            'billing.mercadopago_public_key',
            'billing.mercadopago_access_token',
            'billing.mercadopago_mode',
            'billing.manual_enabled',
            'billing.manual_payment_guide',
        ])->delete();
    }
};
