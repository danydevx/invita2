<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MinisiteTheme;

class MinisiteThemeSeeder extends Seeder
{
    public function run(): void
    {
        $themes = [
            [
                'name' => 'Moderno',
                'slug' => 'modern',
                'description' => 'Estilo oscuro y sofisticado con acentos azules. Ideal para barberías, gyms y negocios modernos.',
                'preview_image' => null,
                'css_variables' => [
                    'colors' => [
                        'brand_primary' => '#1a1a2e',
                        'brand_secondary' => '#16213e',
                        'brand_tertiary' => '#0f3460',
                        'brand_quaternary' => '#3B82F6',
                        'brand_accent' => '#3B82F6',
                        'brand_hover' => '#374151',
                        'brand_link' => '#60A5FA',
                        'brand_background' => '#ffffff',
                        'brand_text' => '#1a1a2e',
                        'brand_text_light' => '#6B7280',
                        'brand_bgcolor_header' => '#1a1a2e',
                        'brand_bgcolor_footer' => '#16213e',
                    ],
                    'fonts' => [
                        'font_heading' => 'Poppins',
                        'font_body' => 'Open Sans',
                        'font_buttons' => 'Poppins',
                    ],
                    'buttons_style' => 'rounded',
                    'buttons_uppercase' => false,
                ],
                'layout_config' => [
                    'page_style' => 'dark',
                    'section_style' => 'spacious',
                    'cards_per_row' => 3,
                    'hero_style' => 'fullwidth',
                    'animations' => [
                        'page_transition' => 'fade-in 0.5s ease-out',
                        'cards_hover' => 'lift',
                        'buttons_hover' => 'scale(1.05)',
                        'sections' => 'fade-in-up 0.6s ease-out',
                    ],
                ],
                'section_config' => [
                    'section_variants' => [
                        'services' => 'cards',
                        'locations' => 'cards',
                        'gallery' => 'grid',
                        'products' => 'cards',
                        'packages' => 'cards',
                    ],
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Elegante',
                'slug' => 'elegant',
                'description' => 'Estilo refinado con tonos dorados y tipografía serif. Perfecto para spas, salones de belleza y estudios de tatuajes premium.',
                'preview_image' => null,
                'css_variables' => [
                    'colors' => [
                        'brand_primary' => '#8B7355',
                        'brand_secondary' => '#C9A86C',
                        'brand_tertiary' => '#D4AF37',
                        'brand_quaternary' => '#E5D4B3',
                        'brand_accent' => '#D4AF37',
                        'brand_hover' => '#6B5344',
                        'brand_link' => '#8B7355',
                        'brand_background' => '#FDF8F3',
                        'brand_text' => '#2D2D2D',
                        'brand_text_light' => '#7A7A7A',
                        'brand_bgcolor_header' => '#FDF8F3',
                        'brand_bgcolor_footer' => '#8B7355',
                    ],
                    'fonts' => [
                        'font_heading' => 'Playfair Display',
                        'font_body' => 'Lato',
                        'font_buttons' => 'Playfair Display',
                    ],
                    'buttons_style' => 'square',
                    'buttons_uppercase' => true,
                ],
                'layout_config' => [
                    'page_style' => 'light',
                    'section_style' => 'classic',
                    'cards_per_row' => 2,
                    'hero_style' => 'centered',
                    'animations' => [
                        'page_transition' => 'fade-in-up 0.6s ease-out',
                        'cards_hover' => 'glow',
                        'buttons_hover' => 'none',
                        'sections' => 'fade-in 0.8s ease-out',
                    ],
                ],
                'section_config' => [
                    'section_variants' => [
                        'services' => 'list',
                        'locations' => 'cards',
                        'gallery' => 'grid',
                        'products' => 'cards',
                    ],
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Profesional',
                'slug' => 'professional',
                'description' => 'Diseño limpio y confiable con tonos verdes y azules. Ideal para profesionales de la salud, dentistas y consultorios.',
                'preview_image' => null,
                'css_variables' => [
                    'colors' => [
                        'brand_primary' => '#0D7377',
                        'brand_secondary' => '#14A085',
                        'brand_tertiary' => '#32E0C4',
                        'brand_quaternary' => '#18994B',
                        'brand_accent' => '#32E0C4',
                        'brand_hover' => '#095449',
                        'brand_link' => '#0D7377',
                        'brand_background' => '#F8FFFE',
                        'brand_text' => '#212121',
                        'brand_text_light' => '#757575',
                        'brand_bgcolor_header' => '#ffffff',
                        'brand_bgcolor_footer' => '#0D7377',
                    ],
                    'fonts' => [
                        'font_heading' => 'Poppins',
                        'font_body' => 'Open Sans',
                        'font_buttons' => 'Poppins',
                    ],
                    'buttons_style' => 'round',
                    'buttons_uppercase' => false,
                ],
                'layout_config' => [
                    'page_style' => 'clean',
                    'section_style' => 'balanced',
                    'cards_per_row' => 3,
                    'hero_style' => 'split',
                    'animations' => [
                        'page_transition' => 'slide-in 0.4s ease-out',
                        'cards_hover' => 'border-expand',
                        'buttons_hover' => 'color-shift',
                        'sections' => 'slide-up 0.5s ease-out',
                    ],
                ],
                'section_config' => [
                    'section_variants' => [
                        'services' => 'cards',
                        'locations' => 'list',
                        'gallery' => 'carousel',
                        'products' => 'list',
                    ],
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Vintage',
                'slug' => 'vintage',
                'description' => 'Estilo rústico y acogedor con tonos café. Ideal para restaurantes, fondas y negocios con personalidad tradicional.',
                'preview_image' => null,
                'css_variables' => [
                    'colors' => [
                        'brand_primary' => '#5D4E37',
                        'brand_secondary' => '#8B7355',
                        'brand_tertiary' => '#C4A77D',
                        'brand_quaternary' => '#A69070',
                        'brand_accent' => '#C4A77D',
                        'brand_hover' => '#3D3225',
                        'brand_link' => '#8B7355',
                        'brand_background' => '#F5F0E8',
                        'brand_text' => '#3D3D3D',
                        'brand_text_light' => '#6B6B6B',
                        'brand_bgcolor_header' => '#5D4E37',
                        'brand_bgcolor_footer' => '#3D3225',
                    ],
                    'fonts' => [
                        'font_heading' => 'Playfair Display',
                        'font_body' => 'Merriweather',
                        'font_buttons' => 'Oswald',
                    ],
                    'buttons_style' => 'square',
                    'buttons_uppercase' => true,
                ],
                'layout_config' => [
                    'page_style' => 'warm',
                    'section_style' => 'cozy',
                    'cards_per_row' => 2,
                    'hero_style' => 'boxed',
                    'animations' => [
                        'page_transition' => 'fade-in 0.8s ease-out',
                        'cards_hover' => 'paper-lift',
                        'buttons_hover' => 'border-expand',
                        'sections' => 'fade-in 1s ease-out',
                    ],
                ],
                'section_config' => [
                    'section_variants' => [
                        'services' => 'list',
                        'locations' => 'list',
                        'gallery' => 'grid',
                        'products' => 'cards',
                    ],
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Friendly',
                'slug' => 'friendly',
                'description' => 'Diseño suave y amigable con tonos verdes. Perfecto para veterinarias y negocios orientado a mascotas.',
                'preview_image' => null,
                'css_variables' => [
                    'colors' => [
                        'brand_primary' => '#2E7D32',
                        'brand_secondary' => '#81C784',
                        'brand_tertiary' => '#A5D6A7',
                        'brand_quaternary' => '#4CAF50',
                        'brand_accent' => '#A5D6A7',
                        'brand_hover' => '#1B5E20',
                        'brand_link' => '#2E7D32',
                        'brand_background' => '#F1F8E9',
                        'brand_text' => '#1B5E20',
                        'brand_text_light' => '#4CAF50',
                        'brand_bgcolor_header' => '#ffffff',
                        'brand_bgcolor_footer' => '#2E7D32',
                    ],
                    'fonts' => [
                        'font_heading' => 'Nunito',
                        'font_body' => 'Quicksand',
                        'font_buttons' => 'Nunito',
                    ],
                    'buttons_style' => 'rounded',
                    'buttons_uppercase' => false,
                ],
                'layout_config' => [
                    'page_style' => 'fresh',
                    'section_style' => 'rounded',
                    'cards_per_row' => 4,
                    'hero_style' => 'friendly',
                    'animations' => [
                        'page_transition' => 'bounce-in 0.5s ease-out',
                        'cards_hover' => 'bounce',
                        'buttons_hover' => 'wiggle',
                        'sections' => 'bounce-in 0.6s ease-out',
                    ],
                ],
                'section_config' => [
                    'section_variants' => [
                        'services' => 'cards',
                        'locations' => 'cards',
                        'gallery' => 'carousel',
                        'products' => 'cards',
                    ],
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Bold',
                'slug' => 'bold',
                'description' => 'Diseño impactante con colores fuertes y alto contraste. Para negocios que quieren destacar con personalidad única.',
                'preview_image' => null,
                'css_variables' => [
                    'colors' => [
                        'brand_primary' => '#FF4500',
                        'brand_secondary' => '#FF6B35',
                        'brand_tertiary' => '#FFD700',
                        'brand_quaternary' => '#FF8C00',
                        'brand_accent' => '#FFD700',
                        'brand_hover' => '#CC3700',
                        'brand_link' => '#FF6B35',
                        'brand_background' => '#1A1A1A',
                        'brand_text' => '#FFFFFF',
                        'brand_text_light' => '#B0B0B0',
                        'brand_bgcolor_header' => '#1A1A1A',
                        'brand_bgcolor_footer' => '#FF4500',
                    ],
                    'fonts' => [
                        'font_heading' => 'Oswald',
                        'font_body' => 'Roboto Condensed',
                        'font_buttons' => 'Oswald',
                    ],
                    'buttons_style' => 'square',
                    'buttons_uppercase' => true,
                ],
                'layout_config' => [
                    'page_style' => 'dark',
                    'section_style' => 'dramatic',
                    'cards_per_row' => 3,
                    'hero_style' => 'fullbleed',
                    'animations' => [
                        'page_transition' => 'zoom-in 0.4s ease-out',
                        'cards_hover' => 'neon-glow: box-shadow 0 0 30px rgba(255, 69, 0, 0.5)',
                        'buttons_hover' => 'fill-in: background-color transition',
                        'sections' => 'zoom-in 0.5s ease-out',
                    ],
                ],
                'section_config' => [
                    'section_variants' => [
                        'services' => 'cards',
                        'locations' => 'cards',
                        'gallery' => 'grid',
                        'products' => 'cards',
                    ],
                    'section_order' => ['hero', 'services', 'gallery', 'products', 'appointments', 'contact', 'reviews', 'locations', 'promotions', 'footer'],
                    'section_props' => [
                        'hero' => [
                            'layout' => 'fullbleed',
                            'alignment' => 'left',
                            'showLogo' => true,
                            'showContactInfo' => true,
                            'showSocialLinks' => false,
                            'overlayOpacity' => 0.7,
                            'backgroundType' => 'gradient',
                            'gradientStart' => '#FF4500',
                            'gradientEnd' => '#FF6B35',
                        ],
                        'services' => [
                            'showTitle' => true,
                            'title' => 'Nuestros Servicios',
                            'subtitle' => '',
                            'showBookingButton' => true,
                            'showWhatsApp' => true,
                            'titleColor' => '#1a1a2e',
                            'subtitleColor' => '#6B7280',
                            'sectionBgColor' => '#ffffff',
                            'cardStyle' => 'light',
                            'buttonStyle' => 'primary',
                        ],
                        'contact' => [
                            'showTitle' => true,
                            'title' => 'Contáctanos',
                            'subtitle' => '',
                            'titleColor' => '#1a1a2e',
                            'subtitleColor' => '#6B7280',
                            'sectionBgColor' => '#f8f9fa',
                            'showSocial' => true,
                            'showContactInfo' => true,
                            'showLocations' => true,
                            'accentColor' => '#FF4500',
                        ],
                        'footer' => [
                            'showBrand' => true,
                            'showContact' => true,
                            'showSocial' => true,
                            'showLinks' => false,
                            'backgroundColor' => '#1a1a2e',
                            'textColor' => '#ffffff',
                            'accentColor' => '#FF4500',
                            'linkColor' => '#b0b0b0',
                        ],
                    ],
                ],
                'is_active' => true,
            ],
        ];

        foreach ($themes as $theme) {
            MinisiteTheme::updateOrCreate(
                ['slug' => $theme['slug']],
                $theme
            );
        }
    }
}
