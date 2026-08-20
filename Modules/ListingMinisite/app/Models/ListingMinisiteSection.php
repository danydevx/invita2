<?php

namespace Modules\ListingMinisite\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ListingMinisiteSection extends Model
{

    protected $table = 'listing_minisite_sections';

    protected $fillable = [
        'listing_id',
        'section_type',
        'section_key',
        'title',
        'subtitle',
        'description',
        'config',
        'buttons',
        'sort_order',
        'is_active',
        'show_social_links',
    ];

    protected $casts = [
        'config' => 'array',
        'buttons' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'show_social_links' => 'boolean',
    ];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(\Modules\Listings\Models\Listing::class);
    }

    public static function getSectionTypes(): array
    {
        return [
            'hero' => 'Encabezado (Hero)',
            'services' => 'Servicios',
            'gallery' => 'Galería',
            'promotions' => 'Promociones',
            'contact_form' => 'Formulario de Contacto',
            'locations' => 'Ubicaciones',
            'about' => 'Nosotros',
            'features' => 'Características',
            'faqs' => 'Preguntas Frecuentes',
            'products' => 'Productos',
            'footer' => 'Pie de Página (Footer)',
            'appointments' => 'Citas y Reservas',
            'reviews' => 'Reseñas',
            'availability' => 'Horario de Atención',
            'restaurant_menu' => 'Menú Restaurante',
            'properties' => 'Propiedades',
            'packages' => 'Paquetes',
        ];
    }

    public static function getDefaultConfig(string $type): array
    {
        return match ($type) {
            'services' => [
                'view_mode' => 'carousel',
                'show_image' => true,
                'show_price' => true,
                'show_description' => false,
                'service_ids' => [],
                'max_items' => 12,
                'min_items' => 3,
            ],
            'gallery' => [
                'gallery_id' => null,
                'images_limit' => 10,
                'max_items' => 12,
                'min_items' => 3,
            ],
            'promotions' => [
                'show_all' => true,
                'promotion_ids' => [],
                'max_items' => 12,
                'min_items' => 3,
            ],
            'contact_form' => [
                'form_id' => null,
            ],
            'locations' => [
                'show_all' => true,
                'location_ids' => [],
                'show_address' => true,
                'show_phone' => true,
                'show_email' => true,
                'show_hours' => true,
                'max_items' => 12,
                'min_items' => 3,
            ],
            'about' => [
                'show_image' => true,
                'show_description' => true,
            ],
            'features' => [
                'show_all' => true,
                'feature_ids' => [],
                'show_icon' => true,
                'show_title' => true,
                'show_description' => true,
                'max_items' => 12,
                'min_items' => 3,
            ],
            'faqs' => [
                'show_all' => true,
                'faq_ids' => [],
                'category_id' => null,
                'show_questions' => true,
                'max_items' => 12,
                'min_items' => 3,
            ],
            'products' => [
                'show_all' => true,
                'product_ids' => [],
                'show_image' => true,
                'show_price' => true,
                'show_compare_price' => true,
                'show_add_to_cart' => false,
                'view_mode' => 'grid',
                'max_items' => 12,
                'min_items' => 3,
            ],
            'hero' => [
                'show' => true,
            ],
            'footer' => [
                'show' => true,
            ],
            'appointments' => [
                'show_service_selector' => true,
                'show_location_selector' => true,
                'default_location_id' => null,
            ],
            'reviews' => [
                'show_all' => true,
                'review_ids' => [],
                'show_rating' => true,
                'show_comment' => true,
                'show_client_name' => true,
                'max_items' => 12,
                'min_items' => 3,
            ],
            'availability' => [
                'show_legend' => true,
            ],
            'restaurant_menu' => [
                'show_all' => true,
                'category_ids' => [],
                'show_images' => true,
                'show_prices' => true,
                'max_items' => 12,
                'min_items' => 3,
            ],
            'properties' => [
                'show_all' => true,
                'property_ids' => [],
                'show_image' => true,
                'show_price' => true,
                'show_location' => true,
                'show_description' => true,
                'view_mode' => 'grid',
                'max_items' => 12,
                'min_items' => 3,
            ],
            'packages' => [
                'show_all' => true,
                'package_ids' => [],
                'show_image' => true,
                'show_price' => true,
                'show_features' => true,
                'show_whatsapp' => true,
                'view_mode' => 'grid',
                'max_items' => 12,
                'min_items' => 3,
            ],
            default => [],
        };
    }
}