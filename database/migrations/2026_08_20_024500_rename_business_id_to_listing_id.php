<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'ai_contexts',
            'ai_conversations',
            'ai_embeddings',
            'business_checkins',
            'chatbot_analytics',
            'chatbot_presets',
            'chatbot_top_questions',
            'chatbot_widgets',
            'client_fidelity_cards',
            'features',
            'listing_abouts',
            'listing_ai_settings',
            'listing_appointment_slots',
            'listing_appointments',
            'listing_availability',
            'listing_availability_exceptions',
            'listing_branding_settings',
            'listing_clients',
            'listing_contact_form_fields',
            'listing_contact_forms',
            'listing_faq_categories',
            'listing_faqs',
            'listing_features',
            'listing_galleries',
            'listing_gallery_images',
            'listing_guests',
            'listing_heroes',
            'listing_leads',
            'listing_locations',
            'listing_minisite_sections',
            'listing_minisite_settings',
            'listing_modules',
            'listing_packages',
            'listing_product_categories',
            'listing_products',
            'listing_promotions',
            'listing_reviews',
            'listing_schedules',
            'listing_seo_settings',
            'listing_services',
            'listing_social_networks',
            'listing_tasks',
            'listing_team_members',
            'menu_categories',
            'menu_products',
            'order_settings',
            'orders',
            'properties',
            'property_types',
            'team_member_positions',
        ];

        DB::statement("SET FOREIGN_KEY_CHECKS=0");

        foreach ($tables as $table) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            if (!Schema::hasColumn($table, 'business_id')) {
                continue;
            }

            $this->renameColumn($table);
        }

        DB::statement("SET FOREIGN_KEY_CHECKS=1");
    }

    public function down(): void
    {
        $tables = [
            'ai_contexts',
            'ai_conversations',
            'ai_embeddings',
            'business_checkins',
            'chatbot_analytics',
            'chatbot_presets',
            'chatbot_top_questions',
            'chatbot_widgets',
            'client_fidelity_cards',
            'features',
            'listing_abouts',
            'listing_ai_settings',
            'listing_appointment_slots',
            'listing_appointments',
            'listing_availability',
            'listing_availability_exceptions',
            'listing_branding_settings',
            'listing_clients',
            'listing_contact_form_fields',
            'listing_contact_forms',
            'listing_faq_categories',
            'listing_faqs',
            'listing_features',
            'listing_galleries',
            'listing_gallery_images',
            'listing_guests',
            'listing_heroes',
            'listing_leads',
            'listing_locations',
            'listing_minisite_sections',
            'listing_minisite_settings',
            'listing_modules',
            'listing_packages',
            'listing_product_categories',
            'listing_products',
            'listing_promotions',
            'listing_reviews',
            'listing_schedules',
            'listing_seo_settings',
            'listing_services',
            'listing_social_networks',
            'listing_tasks',
            'listing_team_members',
            'menu_categories',
            'menu_products',
            'order_settings',
            'orders',
            'properties',
            'property_types',
            'team_member_positions',
        ];

        DB::statement("SET FOREIGN_KEY_CHECKS=0");

        foreach ($tables as $table) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            if (!Schema::hasColumn($table, 'listing_id')) {
                continue;
            }

            $this->renameColumnBack($table);
        }

        DB::statement("SET FOREIGN_KEY_CHECKS=1");
    }

    private function renameColumn(string $table): void
    {
        try {
            DB::statement("ALTER TABLE `$table` CHANGE COLUMN `business_id` `listing_id` BIGINT UNSIGNED NOT NULL");
            echo "Renamed business_id -> listing_id in $table\n";
        } catch (\Exception $e) {
            echo "Error renaming in $table: " . $e->getMessage() . "\n";
        }
    }

    private function renameColumnBack(string $table): void
    {
        try {
            DB::statement("ALTER TABLE `$table` CHANGE COLUMN `listing_id` `business_id` BIGINT UNSIGNED NOT NULL");
            echo "Renamed listing_id -> business_id in $table\n";
        } catch (\Exception $e) {
            echo "Error renaming in $table: " . $e->getMessage() . "\n";
        }
    }
};
