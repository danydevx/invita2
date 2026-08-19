<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'features.api_enabled' => '1',
            'features.webhooks_enabled' => '1',
            'features.support_enabled' => '1',
            'features.help_center_enabled' => '1',
            'features.exports_enabled' => '1',
            'features.member_billing_enabled' => '1',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
