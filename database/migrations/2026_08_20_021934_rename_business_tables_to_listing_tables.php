<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'business_abouts' => 'listing_abouts',
            'business_ai_settings' => 'listing_ai_settings',
            'business_appointment_slots' => 'listing_appointment_slots',
            'business_appointments' => 'listing_appointments',
            'business_availability' => 'listing_availability',
            'business_availability_exceptions' => 'listing_availability_exceptions',
            'business_branding_settings' => 'listing_branding_settings',
            'business_clients' => 'listing_clients',
            'business_contact_form_fields' => 'listing_contact_form_fields',
            'business_contact_forms' => 'listing_contact_forms',
            'business_faq_categories' => 'listing_faq_categories',
            'business_faqs' => 'listing_faqs',
            'business_features' => 'listing_features',
            'business_galleries' => 'listing_galleries',
            'business_gallery_images' => 'listing_gallery_images',
            'business_guests' => 'listing_guests',
            'business_heroes' => 'listing_heroes',
            'business_leads' => 'listing_leads',
            'business_locations' => 'listing_locations',
            'business_minisite_sections' => 'listing_minisite_sections',
            'business_minisite_settings' => 'listing_minisite_settings',
            'business_module_definitions' => 'listing_module_definitions',
            'business_modules' => 'listing_modules',
            'business_packages' => 'listing_packages',
            'business_product_categories' => 'listing_product_categories',
            'business_product_images' => 'listing_product_images',
            'business_products' => 'listing_products',
            'business_promotion_images' => 'listing_promotion_images',
            'business_promotions' => 'listing_promotions',
            'business_reviews' => 'listing_reviews',
            'business_schedules' => 'listing_schedules',
            'business_seo_settings' => 'listing_seo_settings',
            'business_service_images' => 'listing_service_images',
            'business_services' => 'listing_services',
            'business_social_networks' => 'listing_social_networks',
            'business_tasks' => 'listing_tasks',
            'business_team_members' => 'listing_team_members',
            'businesses' => 'listings',
            'plan_business_modules' => 'plan_listing_modules',
        ];

        foreach ($tables as $old => $new) {
            if (Schema::hasTable($old)) {
                Schema::rename($old, $new);
            }
        }
    }

    public function down(): void
    {
        $tables = [
            'listing_abouts' => 'business_abouts',
            'listing_ai_settings' => 'business_ai_settings',
            'listing_appointment_slots' => 'business_appointment_slots',
            'listing_appointments' => 'business_appointments',
            'listing_availability' => 'business_availability',
            'listing_availability_exceptions' => 'business_availability_exceptions',
            'listing_branding_settings' => 'business_branding_settings',
            'listing_clients' => 'business_clients',
            'listing_contact_form_fields' => 'business_contact_form_fields',
            'listing_contact_forms' => 'business_contact_forms',
            'listing_faq_categories' => 'business_faq_categories',
            'listing_faqs' => 'business_faqs',
            'listing_features' => 'business_features',
            'listing_galleries' => 'business_galleries',
            'listing_gallery_images' => 'business_gallery_images',
            'listing_guests' => 'business_guests',
            'listing_heroes' => 'business_heroes',
            'listing_leads' => 'business_leads',
            'listing_locations' => 'business_locations',
            'listing_minisite_sections' => 'business_minisite_sections',
            'listing_minisite_settings' => 'business_minisite_settings',
            'listing_module_definitions' => 'business_module_definitions',
            'listing_modules' => 'business_modules',
            'listing_packages' => 'business_packages',
            'listing_product_categories' => 'business_product_categories',
            'listing_product_images' => 'business_product_images',
            'listing_products' => 'business_products',
            'listing_promotion_images' => 'business_promotion_images',
            'listing_promotions' => 'business_promotions',
            'listing_reviews' => 'business_reviews',
            'listing_schedules' => 'business_schedules',
            'listing_seo_settings' => 'business_seo_settings',
            'listing_service_images' => 'business_service_images',
            'listing_services' => 'business_services',
            'listing_social_networks' => 'business_social_networks',
            'listing_tasks' => 'business_tasks',
            'listing_team_members' => 'business_team_members',
            'listings' => 'businesses',
            'plan_listing_modules' => 'plan_business_modules',
        ];

        foreach ($tables as $old => $new) {
            if (Schema::hasTable($old)) {
                Schema::rename($old, $new);
            }
        }
    }
};
