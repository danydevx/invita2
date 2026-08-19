<?php

namespace Modules\Branding\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessBrandingSetting extends Model
{
    protected $fillable = [
        'business_id',
        'colors',
        'fonts',
        'custom_font_url',
        'dark_mode',
        'buttons_style',
        'buttons_uppercase',
        'generated_css',
        'section_variants',
        'page_style',
        'section_style',
        'hero_style',
        'animations',
    ];

    protected function casts(): array
    {
        return [
            'colors' => 'array',
            'fonts' => 'array',
            'dark_mode' => 'boolean',
            'buttons_uppercase' => 'boolean',
            'section_variants' => 'array',
            'page_style' => 'array',
            'section_style' => 'array',
            'hero_style' => 'array',
            'animations' => 'array',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(\Modules\Businesses\Models\Business::class);
    }

    public static function getDefaultColors(): array
    {
        return [
            'brand_primary' => ['light' => '#3B82F6', 'dark' => '#60A5FA'],
            'brand_secondary' => ['light' => '#8B5CF6', 'dark' => '#A78BFA'],
            'brand_tertiary' => ['light' => '#EC4899', 'dark' => '#F472B6'],
            'brand_quaternary' => ['light' => '#10B981', 'dark' => '#34D399'],
            'brand_accent' => ['light' => '#F59E0B', 'dark' => '#FBBF24'],
            'brand_hover' => ['light' => '#1F2937', 'dark' => '#374151'],
            'brand_link' => ['light' => '#3B82F6', 'dark' => '#60A5FA'],
            'brand_bgcolor_header' => ['light' => '#FFFFFF', 'dark' => '#1A1A2E'],
            'brand_bgcolor_footer' => ['light' => '#F8F9FA', 'dark' => '#16213E'],
            'brand_background' => ['light' => '#FFFFFF', 'dark' => '#1A1A2E'],
            'brand_text' => ['light' => '#1a1a2e', 'dark' => '#ffffff'],
            'brand_text_light' => ['light' => '#6B7280', 'dark' => '#B0B0B0'],
        ];
    }

    public static function getDefaultFonts(): array
    {
        return [
            'heading' => 'Poppins',
            'body' => 'Open Sans',
            'buttons' => 'Poppins',
        ];
    }

    public function getColorsWithDefaults(): array
    {
        $defaults = self::getDefaultColors();
        $colors = $this->colors ?? [];

        foreach ($defaults as $key => $value) {
            if (!isset($colors[$key])) {
                $colors[$key] = $value;
            }
        }

        return $colors;
    }

    public function getFontsWithDefaults(): array
    {
        $defaults = self::getDefaultFonts();
        $fonts = $this->fonts ?? [];

        foreach ($defaults as $key => $value) {
            if (!isset($fonts[$key]) || empty($fonts[$key])) {
                $fonts[$key] = $value;
            }
        }

        return $fonts;
    }

    public function generateCss(): string
    {
        $colors = $this->getColorsWithDefaults();
        $fonts = $this->getFontsWithDefaults();
        $mode = $this->dark_mode ? 'dark' : 'light';

        $primary = $colors['brand_primary'][$mode] ?? '#3B82F6';
        $secondary = $colors['brand_secondary'][$mode] ?? '#8B5CF6';
        $tertiary = $colors['brand_tertiary'][$mode] ?? '#EC4899';
        $quaternary = $colors['brand_quaternary'][$mode] ?? '#10B981';
        $accent = $colors['brand_accent'][$mode] ?? '#F59E0B';
        $hover = $colors['brand_hover'][$mode] ?? '#1F2937';
        $brandLink = $colors['brand_link'][$mode] ?? '#3B82F6';
        $bgcolorHeader = $colors['brand_bgcolor_header'][$mode] ?? '#FFFFFF';
        $bgcolorFooter = $colors['brand_bgcolor_footer'][$mode] ?? '#F8F9FA';
        $bgcolorBackground = $colors['brand_background'][$mode] ?? '#FFFFFF';
        $brandText = $colors['brand_text'][$mode] ?? '#1a1a2e';
        $brandTextLight = $colors['brand_text_light'][$mode] ?? '#6B7280';

        $fontHeading = $fonts['heading'] ?? 'Poppins';
        $fontBody = $fonts['body'] ?? 'Open Sans';
        $fontButtons = $fonts['buttons'] ?? 'Poppins';

        $buttonsStyle = match ($this->buttons_style) {
            'rounded' => '50px',
            'square' => '0px',
            'round' => '8px',
            default => '8px',
        };

        $buttonsUppercase = $this->buttons_uppercase ? 'uppercase' : 'none';

        $pageStyle = $this->page_style ?? 'light';
        $sectionStyle = $this->section_style ?? 'spacious';
        $heroStyle = $this->hero_style ?? 'fullwidth';
        $animations = $this->animations ?? [];

        $darkPrimary = $colors['brand_primary']['dark'] ?? $primary;
        $darkSecondary = $colors['brand_secondary']['dark'] ?? $secondary;
        $darkAccent = $colors['brand_accent']['dark'] ?? $accent;
        $lightPrimary = $colors['brand_primary']['light'] ?? $primary;
        $lightSecondary = $colors['brand_secondary']['light'] ?? $primary;
        $lightAccent = $colors['brand_accent']['light'] ?? $accent;

        $css = ":root {
    --brand-primary: {$primary} !important;
    --brand-secondary: {$secondary} !important;
    --brand-tertiary: {$tertiary} !important;
    --brand-quaternary: {$quaternary} !important;
    --brand-accent: {$accent} !important;
    --brand-hover: {$hover} !important;
    --brand-link: {$brandLink} !important;
    --brand-bgcolor-header: {$bgcolorHeader} !important;
    --brand-bgcolor-footer: {$bgcolorFooter} !important;
    --brand-background: {$bgcolorBackground} !important;
    --brand-text: {$brandText} !important;
    --brand-text-light: {$brandTextLight} !important;
    --heading-font: '{$fontHeading}', sans-serif !important;
    --body-font: '{$fontBody}', sans-serif !important;
    --buttons-font: '{$fontButtons}', sans-serif !important;
    --buttons-border-radius: {$buttonsStyle} !important;
    --buttons-text-transform: {$buttonsUppercase} !important;
    --page-style: {$pageStyle} !important;
    --section-style: {$sectionStyle} !important;
    --hero-style: {$heroStyle} !important;
}

.brand-dark {
    --brand-primary: {$darkPrimary};
    --brand-secondary: {$darkSecondary};
    --brand-accent: {$darkAccent};
    --brand-background: {$bgcolorBackground};
    --brand-text: {$brandText};
    --brand-text-light: {$brandTextLight};
}

.brand-light {
    --brand-primary: {$lightPrimary};
    --brand-secondary: {$lightSecondary};
    --brand-accent: {$lightAccent};
    --brand-background: {$bgcolorBackground};
    --brand-text: {$brandText};
    --brand-text-light: {$brandTextLight};
}";

        $this->generated_css = $css;
        return $css;
    }

    public function getSectionVariant(string $section, string $default = 'cards'): string
    {
        $variants = $this->section_variants ?? [];
        return $variants[$section] ?? $default;
    }
}
