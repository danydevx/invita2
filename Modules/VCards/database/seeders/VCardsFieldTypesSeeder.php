<?php

namespace Modules\VCards\Database\Seeders;

use Illuminate\Database\Seeder;

class VCardsFieldTypesSeeder extends Seeder
{
    public function run(): void
    {
        $fieldTypes = [
            ['key' => 'website', 'name' => 'Website', 'category' => 'other', 'icon' => 'bi-globe', 'sort_order' => 1],
            ['key' => 'link', 'name' => 'Link', 'category' => 'other', 'icon' => 'bi-link', 'sort_order' => 2],
            ['key' => 'email', 'name' => 'Email', 'category' => 'communication', 'icon' => 'bi-envelope', 'sort_order' => 3],
            ['key' => 'phone', 'name' => 'Phone', 'category' => 'communication', 'icon' => 'bi-telephone', 'sort_order' => 4],
            ['key' => 'whatsapp', 'name' => 'WhatsApp', 'category' => 'communication', 'icon' => 'bi-whatsapp', 'sort_order' => 5],
            ['key' => 'instagram', 'name' => 'Instagram', 'category' => 'social', 'icon' => 'bi-instagram', 'sort_order' => 6],
            ['key' => 'facebook', 'name' => 'Facebook', 'category' => 'social', 'icon' => 'bi-facebook', 'sort_order' => 7],
            ['key' => 'linkedin', 'name' => 'LinkedIn', 'category' => 'social', 'icon' => 'bi-linkedin', 'sort_order' => 8],
            ['key' => 'twitter', 'name' => 'X.com', 'category' => 'social', 'icon' => 'bi-twitter-x', 'sort_order' => 9],
            ['key' => 'youtube', 'name' => 'YouTube', 'category' => 'video', 'icon' => 'bi-youtube', 'sort_order' => 10],
            ['key' => 'tiktok', 'name' => 'TikTok', 'category' => 'video', 'icon' => 'bi-tiktok', 'sort_order' => 11],
            ['key' => 'spotify', 'name' => 'Spotify', 'category' => 'music', 'icon' => 'bi-spotify', 'sort_order' => 12],
            ['key' => 'github', 'name' => 'GitHub', 'category' => 'design', 'icon' => 'bi-github', 'sort_order' => 13],
            ['key' => 'telegram', 'name' => 'Telegram', 'category' => 'communication', 'icon' => 'bi-telegram', 'sort_order' => 14],
            ['key' => 'discord', 'name' => 'Discord', 'category' => 'communication', 'icon' => 'bi-discord', 'sort_order' => 15],
            ['key' => 'paypal', 'name' => 'PayPal', 'category' => 'payment', 'icon' => 'bi-paypal', 'sort_order' => 16],
            ['key' => 'venmo', 'name' => 'Venmo', 'category' => 'payment', 'icon' => 'bi-credit-card', 'sort_order' => 17],
            ['key' => 'pdf', 'name' => 'PDF', 'category' => 'other', 'icon' => 'bi-file-pdf', 'sort_order' => 18],
            ['key' => 'address', 'name' => 'Address', 'category' => 'other', 'icon' => 'bi-geo-alt', 'sort_order' => 19],
            ['key' => 'note', 'name' => 'Nota', 'category' => 'other', 'icon' => 'bi-stickies', 'sort_order' => 20],
        ];

        $this->command->info('VCard field types defined in VCardFieldType model (not stored in DB)');
    }
}
