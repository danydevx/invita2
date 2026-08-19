<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    @if(request()->query('preview') === 'mobile')
        <meta name="viewport" content="width=375, initial-scale=1, maximum-scale=1">
    @else
        <meta name="viewport" content="width=device-width, initial-scale=1">
    @endif
    <title inertia>{{ config('app.name', 'Mi SaaS') }}</title>

    @php
    $vars = json_decode($page['props']['theme_css_variables'] ?? '{}', true);
    $schemePalettes = $page['props']['schemePalettes'] ?? [];

    $brandColors = [
        'brand_primary' => $vars['brand_primary'] ?? '#3B82F6',
        'brand_secondary' => $vars['brand_secondary'] ?? '#8B5CF6',
        'brand_accent' => $vars['brand_accent'] ?? '#F59E0B',
        'brand_background' => $vars['brand_background'] ?? '#ffffff',
        'brand_text' => $vars['brand_text'] ?? '#374151',
    ];

    $resolveColor = function($c) use ($brandColors) {
        if (!$c) return '#000000';
        if (str_starts_with($c, '#') || str_starts_with($c, 'rgba') || $c === 'transparent') return $c;
        return $brandColors[$c] ?? $c;
    };

    $schemes = [
        'primary' => [
            'bg' => $resolveColor($schemePalettes['primary']['bg'] ?? '#3B82F6'),
            'text' => $resolveColor($schemePalettes['primary']['text'] ?? '#ffffff'),
            'heading' => $resolveColor($schemePalettes['primary']['heading'] ?? '#ffffff'),
            'link' => $resolveColor($schemePalettes['primary']['link'] ?? '#ffffff'),
            'icon' => $resolveColor($schemePalettes['primary']['icon'] ?? 'rgba(255,255,255,0.8)'),
            'is_gradient' => $schemePalettes['primary']['is_gradient'] ?? false,
            'bg_start' => $resolveColor($schemePalettes['primary']['bg_start'] ?? '#3B82F6'),
            'bg_end' => $resolveColor($schemePalettes['primary']['bg_end'] ?? '#8B5CF6'),
            'card_bg' => $resolveColor($schemePalettes['primary']['card_bg'] ?? '#ffffff'),
            'card_text' => $resolveColor($schemePalettes['primary']['card_text'] ?? '#ffffff'),
            'card_heading' => $resolveColor($schemePalettes['primary']['card_heading'] ?? '#ffffff'),
            'card_border' => $resolveColor($schemePalettes['primary']['card_border'] ?? 'rgba(255,255,255,0.2)'),
            'button_bg' => $resolveColor($schemePalettes['primary']['button_bg'] ?? '#ffffff'),
            'button_text' => $resolveColor($schemePalettes['primary']['button_text'] ?? '#3B82F6'),
            'button_border' => $resolveColor($schemePalettes['primary']['button_border'] ?? 'transparent'),
            'button_hover_bg' => $resolveColor($schemePalettes['primary']['button_hover_bg'] ?? '#3B82F6'),
            'button_hover_text' => $resolveColor($schemePalettes['primary']['button_hover_text'] ?? '#ffffff'),
            'shadow_color' => $schemePalettes['primary']['shadow_color'] ?? 'rgba(0,0,0,0.15)',
            'divider_color' => $schemePalettes['primary']['divider_color'] ?? 'rgba(255,255,255,0.2)',
            'nav_bg' => $resolveColor($schemePalettes['primary']['nav_bg'] ?? '#3B82F6'),
            'nav_text' => $resolveColor($schemePalettes['primary']['nav_text'] ?? '#ffffff'),
            'nav_link' => $resolveColor($schemePalettes['primary']['nav_link'] ?? '#ffffff'),
            'nav_link_hover' => $resolveColor($schemePalettes['primary']['nav_link_hover'] ?? 'rgba(255,255,255,0.8)'),
            'nav_border' => $resolveColor($schemePalettes['primary']['nav_border'] ?? 'rgba(255,255,255,0.2)'),
            'link_hover' => $resolveColor($schemePalettes['primary']['link_hover'] ?? 'rgba(255,255,255,0.8)'),
            'border' => $resolveColor($schemePalettes['primary']['border'] ?? 'rgba(255,255,255,0.2)'),
        ],
        'secondary' => [
            'bg' => $schemePalettes['secondary']['bg'] ?? '#8B5CF6',
            'text' => $schemePalettes['secondary']['text'] ?? '#ffffff',
            'heading' => $schemePalettes['secondary']['heading'] ?? '#ffffff',
            'link' => $schemePalettes['secondary']['link'] ?? '#ffffff',
            'icon' => $schemePalettes['secondary']['icon'] ?? 'rgba(255,255,255,0.8)',
            'is_gradient' => $schemePalettes['secondary']['is_gradient'] ?? false,
            'bg_start' => $schemePalettes['secondary']['bg_start'] ?? '#3B82F6',
            'bg_end' => $schemePalettes['secondary']['bg_end'] ?? '#8B5CF6',
            'card_bg' => $schemePalettes['secondary']['card_bg'] ?? '#ffffff',
            'card_text' => $schemePalettes['secondary']['card_text'] ?? '#ffffff',
            'card_heading' => $schemePalettes['secondary']['card_heading'] ?? '#ffffff',
            'card_border' => $schemePalettes['secondary']['card_border'] ?? 'rgba(255,255,255,0.2)',
            'button_bg' => $schemePalettes['secondary']['button_bg'] ?? '#ffffff',
            'button_text' => $schemePalettes['secondary']['button_text'] ?? '#8B5CF6',
            'button_border' => $schemePalettes['secondary']['button_border'] ?? 'transparent',
            'button_hover_bg' => $schemePalettes['secondary']['button_hover_bg'] ?? '#8B5CF6',
            'button_hover_text' => $schemePalettes['secondary']['button_hover_text'] ?? '#ffffff',
            'shadow_color' => $schemePalettes['secondary']['shadow_color'] ?? 'rgba(0,0,0,0.15)',
            'divider_color' => $schemePalettes['secondary']['divider_color'] ?? 'rgba(255,255,255,0.2)',
            'nav_bg' => $schemePalettes['secondary']['nav_bg'] ?? '#8B5CF6',
            'nav_text' => $schemePalettes['secondary']['nav_text'] ?? '#ffffff',
            'nav_link' => $schemePalettes['secondary']['nav_link'] ?? '#ffffff',
            'nav_link_hover' => $schemePalettes['secondary']['nav_link_hover'] ?? 'rgba(255,255,255,0.8)',
            'nav_border' => $schemePalettes['secondary']['nav_border'] ?? 'rgba(255,255,255,0.2)',
        ],
        'accent' => [
            'bg' => $schemePalettes['accent']['bg'] ?? '#F59E0B',
            'text' => $schemePalettes['accent']['text'] ?? '#ffffff',
            'heading' => $schemePalettes['accent']['heading'] ?? '#ffffff',
            'link' => $schemePalettes['accent']['link'] ?? '#ffffff',
            'icon' => $schemePalettes['accent']['icon'] ?? 'rgba(255,255,255,0.8)',
            'is_gradient' => $schemePalettes['accent']['is_gradient'] ?? false,
            'bg_start' => $schemePalettes['accent']['bg_start'] ?? '#F59E0B',
            'bg_end' => $schemePalettes['accent']['bg_end'] ?? '#EF4444',
            'card_bg' => $schemePalettes['accent']['card_bg'] ?? '#ffffff',
            'card_text' => $schemePalettes['accent']['card_text'] ?? '#374151',
            'card_heading' => $schemePalettes['accent']['card_heading'] ?? '#F59E0B',
            'card_border' => $schemePalettes['accent']['card_border'] ?? 'rgba(245,158,11,0.3)',
            'button_bg' => $schemePalettes['accent']['button_bg'] ?? '#ffffff',
            'button_text' => $schemePalettes['accent']['button_text'] ?? '#F59E0B',
            'button_border' => $schemePalettes['accent']['button_border'] ?? 'transparent',
            'button_hover_bg' => $schemePalettes['accent']['button_hover_bg'] ?? '#F59E0B',
            'button_hover_text' => $schemePalettes['accent']['button_hover_text'] ?? '#ffffff',
            'shadow_color' => $schemePalettes['accent']['shadow_color'] ?? 'rgba(0,0,0,0.15)',
            'divider_color' => $schemePalettes['accent']['divider_color'] ?? 'rgba(255,255,255,0.2)',
            'nav_bg' => $schemePalettes['accent']['nav_bg'] ?? '#F59E0B',
            'nav_text' => $schemePalettes['accent']['nav_text'] ?? '#ffffff',
            'nav_link' => $schemePalettes['accent']['nav_link'] ?? '#ffffff',
            'nav_link_hover' => $schemePalettes['accent']['nav_link_hover'] ?? 'rgba(255,255,255,0.8)',
            'nav_border' => $schemePalettes['accent']['nav_border'] ?? 'rgba(245,158,11,0.3)',
        ],
        'light' => [
            'bg' => $schemePalettes['light']['bg'] ?? '#ffffff',
            'text' => $schemePalettes['light']['text'] ?? '#374151',
            'heading' => $schemePalettes['light']['heading'] ?? '#3B82F6',
            'link' => $schemePalettes['light']['link'] ?? '#3B82F6',
            'icon' => $schemePalettes['light']['icon'] ?? '#3B82F6',
            'is_gradient' => false,
            'bg_start' => null,
            'bg_end' => null,
            'card_bg' => $schemePalettes['light']['card_bg'] ?? '#ffffff',
            'card_text' => $schemePalettes['light']['card_text'] ?? '#374151',
            'card_heading' => $schemePalettes['light']['card_heading'] ?? '#3B82F6',
            'card_border' => $schemePalettes['light']['card_border'] ?? 'rgba(0,0,0,0.1)',
            'button_bg' => $schemePalettes['light']['button_bg'] ?? '#3B82F6',
            'button_text' => $schemePalettes['light']['button_text'] ?? '#ffffff',
            'button_border' => $schemePalettes['light']['button_border'] ?? '#3B82F6',
            'button_hover_bg' => $schemePalettes['light']['button_hover_bg'] ?? '#374151',
            'button_hover_text' => $schemePalettes['light']['button_hover_text'] ?? '#ffffff',
            'shadow_color' => $schemePalettes['light']['shadow_color'] ?? 'rgba(0,0,0,0.1)',
            'divider_color' => $schemePalettes['light']['divider_color'] ?? 'rgba(0,0,0,0.1)',
            'nav_bg' => $schemePalettes['light']['nav_bg'] ?? '#ffffff',
            'nav_text' => $schemePalettes['light']['nav_text'] ?? '#374151',
            'nav_link' => $schemePalettes['light']['nav_link'] ?? '#3B82F6',
            'nav_link_hover' => $schemePalettes['light']['nav_link_hover'] ?? '#F59E0B',
            'nav_border' => $schemePalettes['light']['nav_border'] ?? 'rgba(0,0,0,0.1)',
        ],
        'neutral' => [
            'bg' => $schemePalettes['neutral']['bg'] ?? '#f8f9fa',
            'text' => $schemePalettes['neutral']['text'] ?? '#374151',
            'heading' => $schemePalettes['neutral']['heading'] ?? '#3B82F6',
            'link' => $schemePalettes['neutral']['link'] ?? '#3B82F6',
            'icon' => $schemePalettes['neutral']['icon'] ?? '#3B82F6',
            'is_gradient' => false,
            'bg_start' => null,
            'bg_end' => null,
            'card_bg' => $schemePalettes['neutral']['card_bg'] ?? '#ffffff',
            'card_text' => $schemePalettes['neutral']['card_text'] ?? '#374151',
            'card_heading' => $schemePalettes['neutral']['card_heading'] ?? '#3B82F6',
            'card_border' => $schemePalettes['neutral']['card_border'] ?? 'rgba(0,0,0,0.1)',
            'button_bg' => $schemePalettes['neutral']['button_bg'] ?? '#3B82F6',
            'button_text' => $schemePalettes['neutral']['button_text'] ?? '#ffffff',
            'button_border' => $schemePalettes['neutral']['button_border'] ?? '#3B82F6',
            'button_hover_bg' => $schemePalettes['neutral']['button_hover_bg'] ?? '#374151',
            'button_hover_text' => $schemePalettes['neutral']['button_hover_text'] ?? '#ffffff',
            'shadow_color' => $schemePalettes['neutral']['shadow_color'] ?? 'rgba(0,0,0,0.1)',
            'divider_color' => $schemePalettes['neutral']['divider_color'] ?? 'rgba(0,0,0,0.1)',
            'nav_bg' => $schemePalettes['neutral']['nav_bg'] ?? '#f8f9fa',
            'nav_text' => $schemePalettes['neutral']['nav_text'] ?? '#374151',
            'nav_link' => $schemePalettes['neutral']['nav_link'] ?? '#3B82F6',
            'nav_link_hover' => $schemePalettes['neutral']['nav_link_hover'] ?? '#F59E0B',
            'nav_border' => $schemePalettes['neutral']['nav_border'] ?? 'rgba(0,0,0,0.1)',
        ],
        'dark' => [
            'bg' => $schemePalettes['dark']['bg'] ?? '#1a1a2e',
            'text' => $schemePalettes['dark']['text'] ?? '#ffffff',
            'heading' => $schemePalettes['dark']['heading'] ?? '#ffffff',
            'link' => $schemePalettes['dark']['link'] ?? '#ffffff',
            'icon' => $schemePalettes['dark']['icon'] ?? 'rgba(255,255,255,0.8)',
            'is_gradient' => $schemePalettes['dark']['is_gradient'] ?? false,
            'bg_start' => $schemePalettes['dark']['bg_start'] ?? '#1a1a2e',
            'bg_end' => $schemePalettes['dark']['bg_end'] ?? '#16213e',
            'card_bg' => $schemePalettes['dark']['card_bg'] ?? 'rgba(0,0,0,0.3)',
            'card_text' => $schemePalettes['dark']['card_text'] ?? '#ffffff',
            'card_heading' => $schemePalettes['dark']['card_heading'] ?? '#ffffff',
            'card_border' => $schemePalettes['dark']['card_border'] ?? 'rgba(255,255,255,0.1)',
            'button_bg' => $schemePalettes['dark']['button_bg'] ?? '#ffffff',
            'button_text' => $schemePalettes['dark']['button_text'] ?? '#1a1a2e',
            'button_border' => $schemePalettes['dark']['button_border'] ?? 'transparent',
            'button_hover_bg' => $schemePalettes['dark']['button_hover_bg'] ?? '#ffffff',
            'button_hover_text' => $schemePalettes['dark']['button_hover_text'] ?? '#1a1a2e',
            'shadow_color' => $schemePalettes['dark']['shadow_color'] ?? 'rgba(0,0,0,0.3)',
            'divider_color' => $schemePalettes['dark']['divider_color'] ?? 'rgba(255,255,255,0.2)',
            'nav_bg' => $schemePalettes['dark']['nav_bg'] ?? '#1a1a2e',
            'nav_text' => $schemePalettes['dark']['nav_text'] ?? '#ffffff',
            'nav_link' => $schemePalettes['dark']['nav_link'] ?? '#ffffff',
            'nav_link_hover' => $schemePalettes['dark']['nav_link_hover'] ?? 'rgba(255,255,255,0.8)',
            'nav_border' => $schemePalettes['dark']['nav_border'] ?? 'rgba(255,255,255,0.1)',
        ],
        'transparent' => [
            'bg' => 'transparent',
            'text' => $schemePalettes['transparent']['text'] ?? '#374151',
            'heading' => $schemePalettes['transparent']['heading'] ?? '#3B82F6',
            'link' => $schemePalettes['transparent']['link'] ?? '#3B82F6',
            'icon' => $schemePalettes['transparent']['icon'] ?? '#3B82F6',
            'is_gradient' => false,
            'bg_start' => null,
            'bg_end' => null,
            'card_bg' => $schemePalettes['transparent']['card_bg'] ?? '#ffffff',
            'card_text' => $schemePalettes['transparent']['card_text'] ?? '#374151',
            'card_heading' => $schemePalettes['transparent']['card_heading'] ?? '#3B82F6',
            'card_border' => $schemePalettes['transparent']['card_border'] ?? 'rgba(0,0,0,0.1)',
            'button_bg' => $schemePalettes['transparent']['button_bg'] ?? '#3B82F6',
            'button_text' => $schemePalettes['transparent']['button_text'] ?? '#ffffff',
            'button_border' => $schemePalettes['transparent']['button_border'] ?? '#3B82F6',
            'button_hover_bg' => $schemePalettes['transparent']['button_hover_bg'] ?? '#374151',
            'button_hover_text' => $schemePalettes['transparent']['button_hover_text'] ?? '#ffffff',
            'shadow_color' => $schemePalettes['transparent']['shadow_color'] ?? 'rgba(0,0,0,0.1)',
            'divider_color' => $schemePalettes['transparent']['divider_color'] ?? 'rgba(0,0,0,0.1)',
            'nav_bg' => $schemePalettes['transparent']['nav_bg'] ?? '#ffffff',
            'nav_text' => $schemePalettes['transparent']['nav_text'] ?? '#374151',
            'nav_link' => $schemePalettes['transparent']['nav_link'] ?? '#3B82F6',
            'nav_link_hover' => $schemePalettes['transparent']['nav_link_hover'] ?? '#F59E0B',
            'nav_border' => $schemePalettes['transparent']['nav_border'] ?? 'rgba(0,0,0,0.1)',
        ],
        'gradient' => [
            'bg' => $schemePalettes['gradient']['is_gradient'] ?? false
                ? 'linear-gradient(' . ($schemePalettes['gradient']['bg_start'] ?? '#3B82F6') . ', ' . ($schemePalettes['gradient']['bg_end'] ?? '#8B5CF6') . ')'
                : 'linear-gradient(#3B82F6, #8B5CF6)',
            'text' => $schemePalettes['gradient']['text'] ?? '#ffffff',
            'heading' => $schemePalettes['gradient']['heading'] ?? '#ffffff',
            'link' => $schemePalettes['gradient']['link'] ?? '#ffffff',
            'icon' => $schemePalettes['gradient']['icon'] ?? 'rgba(255,255,255,0.8)',
            'is_gradient' => true,
            'bg_start' => $schemePalettes['gradient']['bg_start'] ?? '#3B82F6',
            'bg_end' => $schemePalettes['gradient']['bg_end'] ?? '#8B5CF6',
            'card_bg' => $schemePalettes['gradient']['card_bg'] ?? '#ffffff',
            'card_text' => $schemePalettes['gradient']['card_text'] ?? '#374151',
            'card_heading' => $schemePalettes['gradient']['card_heading'] ?? '#3B82F6',
            'card_border' => $schemePalettes['gradient']['card_border'] ?? 'rgba(0,0,0,0.1)',
            'button_bg' => $schemePalettes['gradient']['button_bg'] ?? '#ffffff',
            'button_text' => $schemePalettes['gradient']['button_text'] ?? '#3B82F6',
            'button_border' => $schemePalettes['gradient']['button_border'] ?? 'transparent',
            'button_hover_bg' => $schemePalettes['gradient']['button_hover_bg'] ?? '#3B82F6',
            'button_hover_text' => $schemePalettes['gradient']['button_hover_text'] ?? '#ffffff',
            'shadow_color' => $schemePalettes['gradient']['shadow_color'] ?? 'rgba(0,0,0,0.2)',
            'divider_color' => $schemePalettes['gradient']['divider_color'] ?? 'rgba(255,255,255,0.3)',
            'nav_bg' => $schemePalettes['gradient']['nav_bg'] ?? '#3B82F6',
            'nav_text' => $schemePalettes['gradient']['nav_text'] ?? '#ffffff',
            'nav_link' => $schemePalettes['gradient']['nav_link'] ?? '#ffffff',
            'nav_link_hover' => $schemePalettes['gradient']['nav_link_hover'] ?? 'rgba(255,255,255,0.8)',
            'nav_border' => $schemePalettes['gradient']['nav_border'] ?? 'rgba(255,255,255,0.2)',
        ],
        'header' => [
            'bg' => $resolveColor($schemePalettes['header']['bg'] ?? '#ffffff'),
            'text' => $resolveColor($schemePalettes['header']['text'] ?? '#374151'),
            'heading' => $resolveColor($schemePalettes['header']['heading'] ?? '#3B82F6'),
            'link' => $resolveColor($schemePalettes['header']['link'] ?? '#3B82F6'),
            'icon' => $resolveColor($schemePalettes['header']['icon'] ?? '#3B82F6'),
            'is_gradient' => false,
            'bg_start' => null,
            'bg_end' => null,
            'card_bg' => $resolveColor($schemePalettes['header']['card_bg'] ?? '#ffffff'),
            'card_text' => $resolveColor($schemePalettes['header']['card_text'] ?? '#374151'),
            'card_heading' => $resolveColor($schemePalettes['header']['card_heading'] ?? '#3B82F6'),
            'card_border' => $resolveColor($schemePalettes['header']['card_border'] ?? 'rgba(0,0,0,0.1)'),
            'button_bg' => $resolveColor($schemePalettes['header']['button_bg'] ?? '#3B82F6'),
            'button_text' => $resolveColor($schemePalettes['header']['button_text'] ?? '#ffffff'),
            'button_border' => $resolveColor($schemePalettes['header']['button_border'] ?? '#3B82F6'),
            'button_hover_bg' => $resolveColor($schemePalettes['header']['button_hover_bg'] ?? '#374151'),
            'button_hover_text' => $resolveColor($schemePalettes['header']['button_hover_text'] ?? '#ffffff'),
            'shadow_color' => $schemePalettes['header']['shadow_color'] ?? 'rgba(0,0,0,0.1)',
            'divider_color' => $schemePalettes['header']['divider_color'] ?? 'rgba(0,0,0,0.1)',
            'nav_bg' => $resolveColor($schemePalettes['header']['nav_bg'] ?? '#ffffff'),
            'nav_text' => $resolveColor($schemePalettes['header']['nav_text'] ?? '#374151'),
            'nav_link' => $resolveColor($schemePalettes['header']['nav_link'] ?? '#3B82F6'),
            'nav_link_hover' => $resolveColor($schemePalettes['header']['nav_link_hover'] ?? '#F59E0B'),
            'nav_border' => $resolveColor($schemePalettes['header']['nav_border'] ?? 'rgba(0,0,0,0.1)'),
            'link_hover' => $resolveColor($schemePalettes['header']['link_hover'] ?? '#F59E0B'),
            'border' => $resolveColor($schemePalettes['header']['border'] ?? 'rgba(0,0,0,0.1)'),
        ],
        'footer' => [
            'bg' => $resolveColor($schemePalettes['footer']['bg'] ?? '#1a1a2e'),
            'text' => $resolveColor($schemePalettes['footer']['text'] ?? '#ffffff'),
            'heading' => $resolveColor($schemePalettes['footer']['heading'] ?? '#ffffff'),
            'link' => $resolveColor($schemePalettes['footer']['link'] ?? '#ffffff'),
            'icon' => $resolveColor($schemePalettes['footer']['icon'] ?? 'rgba(255,255,255,0.8)'),
            'is_gradient' => false,
            'bg_start' => null,
            'bg_end' => null,
            'card_bg' => $resolveColor($schemePalettes['footer']['card_bg'] ?? 'rgba(0,0,0,0.2)'),
            'card_text' => $resolveColor($schemePalettes['footer']['card_text'] ?? '#ffffff'),
            'card_heading' => $resolveColor($schemePalettes['footer']['card_heading'] ?? '#ffffff'),
            'card_border' => $resolveColor($schemePalettes['footer']['card_border'] ?? 'rgba(255,255,255,0.1)'),
            'button_bg' => $resolveColor($schemePalettes['footer']['button_bg'] ?? '#ffffff'),
            'button_text' => $resolveColor($schemePalettes['footer']['button_text'] ?? '#1a1a2e'),
            'button_border' => $resolveColor($schemePalettes['footer']['button_border'] ?? 'transparent'),
            'button_hover_bg' => $resolveColor($schemePalettes['footer']['button_hover_bg'] ?? '#ffffff'),
            'button_hover_text' => $resolveColor($schemePalettes['footer']['button_hover_text'] ?? '#1a1a2e'),
            'shadow_color' => $schemePalettes['footer']['shadow_color'] ?? 'rgba(0,0,0,0.3)',
            'divider_color' => $schemePalettes['footer']['divider_color'] ?? 'rgba(255,255,255,0.2)',
            'nav_bg' => $resolveColor($schemePalettes['footer']['nav_bg'] ?? '#1a1a2e'),
            'nav_text' => $resolveColor($schemePalettes['footer']['nav_text'] ?? '#ffffff'),
            'nav_link' => $resolveColor($schemePalettes['footer']['nav_link'] ?? '#ffffff'),
            'nav_link_hover' => $resolveColor($schemePalettes['footer']['nav_link_hover'] ?? 'rgba(255,255,255,0.8)'),
            'nav_border' => $resolveColor($schemePalettes['footer']['nav_border'] ?? 'rgba(255,255,255,0.1)'),
            'link_hover' => $resolveColor($schemePalettes['footer']['link_hover'] ?? 'rgba(255,255,255,0.8)'),
            'border' => $resolveColor($schemePalettes['footer']['border'] ?? 'rgba(255,255,255,0.1)'),
        ],
    ];
    @endphp

    @if(!empty($vars['fonts']['font_heading']))
      <link href="https://fonts.googleapis.com/css2?family={{ urlencode($vars['fonts']['font_heading']) }}:wght@400;600;700&display=swap" rel="stylesheet">
    @endif
    @if(!empty($vars['fonts']['font_body']))
      <link href="https://fonts.googleapis.com/css2?family={{ urlencode($vars['fonts']['font_body']) }}:wght@400;500;600&display=swap" rel="stylesheet">
    @endif

    <link rel="stylesheet" href="{{ asset('build/assets/minisite-D0rvqTkn.css') }}">
    @vite(['resources/js/minisite.js'])

    <style>
      :root {
        --brand-primary: {{ $vars['colors']['brand_primary'] ?? '#6B7280' }};
        --brand-secondary: {{ $vars['colors']['brand_secondary'] ?? '#9CA3AF' }};
        --brand-accent: {{ $vars['colors']['brand_accent'] ?? '#60A5FA' }};
        --brand-background: {{ $vars['colors']['brand_background'] ?? '#FFFFFF' }};
        --brand-text: {{ $vars['colors']['brand_text'] ?? '#374151' }};
        --brand-text-light: {{ $vars['colors']['brand_text_light'] ?? '#9CA3AF' }};
        --brand-bgcolor-header: {{ $vars['colors']['brand_bgcolor_header'] ?? '#FFFFFF' }};
        --brand-bgcolor-footer: {{ $vars['colors']['brand_bgcolor_footer'] ?? '#F3F4F6' }};
        --heading-font: '{{ $vars['fonts']['font_heading'] ?? 'Poppins' }}', sans-serif;
        --body-font: '{{ $vars['fonts']['font_body'] ?? 'Open Sans' }}', sans-serif;
        --brand-button-radius: {{ ['rounded' => '50px', 'square' => '0px', 'round' => '8px'][data_get($vars, 'buttons_style', 'round')] ?? '8px' }};
        --brand-card-radius: {{ ['rounded' => '50px', 'square' => '0px', 'round' => '12px'][data_get($vars, 'card_style', 'round')] ?? '12px' }};
        --font-size-h1: 3rem;
        --font-size-h2: 2.5rem;
        --font-size-h3: 2rem;
        --font-size-h4: 1.5rem;
        --font-size-h5: 1.25rem;
        --font-size-h6: 1rem;
        --font-size-base: 1rem;
        --font-size-sm: 0.875rem;
        --font-size-xs: 0.75rem;
        --font-size-btn: 0.875rem;
      }

      .section--primary {
        background: {{ $schemes['primary']['is_gradient'] ? 'linear-gradient(' . $schemes['primary']['bg_start'] . ', ' . $schemes['primary']['bg_end'] . ')' : $schemes['primary']['bg'] }} !important;
        color: {{ $schemes['primary']['text'] }} !important;
      }
      .section--primary h1,.section--primary h2,.section--primary h3,.section--primary h4,.section--primary h5,.section--primary h6 { color: {{ $schemes['primary']['heading'] }} !important; }
      .section--primary a { color: {{ $schemes['primary']['link'] }} !important; }
      .section--primary i { color: {{ $schemes['primary']['icon'] }} !important; }
      .section--primary .card { background: {{ $schemes['primary']['card_bg'] }} !important; color: {{ $schemes['primary']['card_text'] }} !important; border-color: {{ $schemes['primary']['card_border'] }} !important; }
      .section--primary .card h1,.section--primary .card h2,.section--primary .card h3,.section--primary .card h4,.section--primary .card h5,.section--primary .card h6 { color: {{ $schemes['primary']['card_heading'] }} !important; }

      .section--secondary {
        background: {{ $schemes['secondary']['is_gradient'] ? 'linear-gradient(' . $schemes['secondary']['bg_start'] . ', ' . $schemes['secondary']['bg_end'] . ')' : $schemes['secondary']['bg'] }} !important;
        color: {{ $schemes['secondary']['text'] }} !important;
      }
      .section--secondary h1,.section--secondary h2,.section--secondary h3,.section--secondary h4,.section--secondary h5,.section--secondary h6 { color: {{ $schemes['secondary']['heading'] }} !important; }
      .section--secondary a { color: {{ $schemes['secondary']['link'] }} !important; }
      .section--secondary i { color: {{ $schemes['secondary']['icon'] }} !important; }
      .section--secondary .card { background: {{ $schemes['secondary']['card_bg'] }} !important; color: {{ $schemes['secondary']['card_text'] }} !important; border-color: {{ $schemes['secondary']['card_border'] }} !important; }
      .section--secondary .card h1,.section--secondary .card h2,.section--secondary .card h3,.section--secondary .card h4,.section--secondary .card h5,.section--secondary .card h6 { color: {{ $schemes['secondary']['card_heading'] }} !important; }

      .section--accent {
        background: {{ $schemes['accent']['is_gradient'] ? 'linear-gradient(' . $schemes['accent']['bg_start'] . ', ' . $schemes['accent']['bg_end'] . ')' : $schemes['accent']['bg'] }} !important;
        color: {{ $schemes['accent']['text'] }} !important;
      }
      .section--accent h1,.section--accent h2,.section--accent h3,.section--accent h4,.section--accent h5,.section--accent h6 { color: {{ $schemes['accent']['heading'] }} !important; }
      .section--accent a { color: {{ $schemes['accent']['link'] }} !important; }
      .section--accent i { color: {{ $schemes['accent']['icon'] }} !important; }
      .section--accent .card { background: {{ $schemes['accent']['card_bg'] }} !important; color: {{ $schemes['accent']['card_text'] }} !important; border-color: {{ $schemes['accent']['card_border'] }} !important; }
      .section--accent .card h1,.section--accent .card h2,.section--accent .card h3,.section--accent .card h4,.section--accent .card h5,.section--accent .card h6 { color: {{ $schemes['accent']['card_heading'] }} !important; }

      .section--light {
        background: {{ $schemes['light']['bg'] }} !important;
        color: {{ $schemes['light']['text'] }} !important;
      }
      .section--light h1,.section--light h2,.section--light h3,.section--light h4,.section--light h5,.section--light h6 { color: {{ $schemes['light']['heading'] }} !important; }
      .section--light a { color: {{ $schemes['light']['link'] }} !important; }
      .section--light i { color: {{ $schemes['light']['icon'] }} !important; }
      .section--light .card { background: {{ $schemes['light']['card_bg'] }} !important; color: {{ $schemes['light']['card_text'] }} !important; border-color: {{ $schemes['light']['card_border'] }} !important; }
      .section--light .card h1,.section--light .card h2,.section--light .card h3,.section--light .card h4,.section--light .card h5,.section--light .card h6 { color: {{ $schemes['light']['card_heading'] }} !important; }

      .section--dark {
        background: {{ $schemes['dark']['is_gradient'] ? 'linear-gradient(' . $schemes['dark']['bg_start'] . ', ' . $schemes['dark']['bg_end'] . ')' : $schemes['dark']['bg'] }} !important;
        color: {{ $schemes['dark']['text'] }} !important;
      }
      .section--dark h1,.section--dark h2,.section--dark h3,.section--dark h4,.section--dark h5,.section--dark h6 { color: {{ $schemes['dark']['heading'] }} !important; }
      .section--dark a { color: {{ $schemes['dark']['link'] }} !important; }
      .section--dark i { color: {{ $schemes['dark']['icon'] }} !important; }
      .section--dark .card { background: {{ $schemes['dark']['card_bg'] }} !important; color: {{ $schemes['dark']['card_text'] }} !important; border-color: {{ $schemes['dark']['card_border'] }} !important; }
      .section--dark .card h1,.section--dark .card h2,.section--dark .card h3,.section--dark .card h4,.section--dark .card h5,.section--dark .card h6 { color: {{ $schemes['dark']['card_heading'] }} !important; }

      .section--neutral {
        background: {{ $schemes['neutral']['bg'] }} !important;
        color: {{ $schemes['neutral']['text'] }} !important;
      }
      .section--neutral h1,.section--neutral h2,.section--neutral h3,.section--neutral h4,.section--neutral h5,.section--neutral h6 { color: {{ $schemes['neutral']['heading'] }} !important; }
      .section--neutral a { color: {{ $schemes['neutral']['link'] }} !important; }
      .section--neutral i { color: {{ $schemes['neutral']['icon'] }} !important; }
      .section--neutral .card { background: {{ $schemes['neutral']['card_bg'] }} !important; color: {{ $schemes['neutral']['card_text'] }} !important; border-color: {{ $schemes['neutral']['card_border'] }} !important; }
      .section--neutral .card h1,.section--neutral .card h2,.section--neutral .card h3,.section--neutral .card h4,.section--neutral .card h5,.section--neutral .card h6 { color: {{ $schemes['neutral']['card_heading'] }} !important; }

      .section--gradient {
        background: {{ $schemes['gradient']['is_gradient'] ? 'linear-gradient(' . $schemes['gradient']['bg_start'] . ', ' . $schemes['gradient']['bg_end'] . ')' : 'linear-gradient(#3B82F6, #8B5CF6)' }} !important;
        color: {{ $schemes['gradient']['text'] }} !important;
      }
      .section--gradient h1,.section--gradient h2,.section--gradient h3,.section--gradient h4,.section--gradient h5,.section--gradient h6 { color: {{ $schemes['gradient']['heading'] }} !important; }
      .section--gradient a { color: {{ $schemes['gradient']['link'] }} !important; }
      .section--gradient i { color: {{ $schemes['gradient']['icon'] }} !important; }
      .section--gradient .card { background: {{ $schemes['gradient']['card_bg'] }} !important; color: {{ $schemes['gradient']['card_text'] }} !important; border-color: {{ $schemes['gradient']['card_border'] }} !important; }
      .section--gradient .card h1,.section--gradient .card h2,.section--gradient .card h3,.section--gradient .card h4,.section--gradient .card h5,.section--gradient .card h6 { color: {{ $schemes['gradient']['card_heading'] }} !important; }

      .section--transparent {
        background: transparent !important;
        color: {{ $schemes['transparent']['text'] }} !important;
      }
      .section--transparent h1,.section--transparent h2,.section--transparent h3,.section--transparent h4,.section--transparent h5,.section--transparent h6 { color: {{ $schemes['transparent']['heading'] }} !important; }
      .section--transparent a { color: {{ $schemes['transparent']['link'] }} !important; }
      .section--transparent i { color: {{ $schemes['transparent']['icon'] }} !important; }
      .section--transparent .card { background: {{ $schemes['transparent']['card_bg'] }} !important; color: {{ $schemes['transparent']['card_text'] }} !important; border-color: {{ $schemes['transparent']['card_border'] }} !important; }
      .section--transparent .card h1,.section--transparent .card h2,.section--transparent .card h3,.section--transparent .card h4,.section--transparent .card h5,.section--transparent .card h6 { color: {{ $schemes['transparent']['card_heading'] }} !important; }

      /* Button styles for each scheme */
      .section--primary .btn-scheme { background: {{ $schemes['primary']['button_bg'] }}; color: {{ $schemes['primary']['button_text'] }}; border-color: {{ $schemes['primary']['button_border'] }}; }
      .section--primary .btn-scheme:hover { background: {{ $schemes['primary']['button_hover_bg'] }}; color: {{ $schemes['primary']['button_hover_text'] }}; }

      .section--secondary .btn-scheme { background: {{ $schemes['secondary']['button_bg'] }}; color: {{ $schemes['secondary']['button_text'] }}; border-color: {{ $schemes['secondary']['button_border'] }}; }
      .section--secondary .btn-scheme:hover { background: {{ $schemes['secondary']['button_hover_bg'] }}; color: {{ $schemes['secondary']['button_hover_text'] }}; }

      .section--accent .btn-scheme { background: {{ $schemes['accent']['button_bg'] }}; color: {{ $schemes['accent']['button_text'] }}; border-color: {{ $schemes['accent']['button_border'] }}; }
      .section--accent .btn-scheme:hover { background: {{ $schemes['accent']['button_hover_bg'] }}; color: {{ $schemes['accent']['button_hover_text'] }}; }

      .section--light .btn-scheme { background: {{ $schemes['light']['button_bg'] }}; color: {{ $schemes['light']['button_text'] }}; border-color: {{ $schemes['light']['button_border'] }}; }
      .section--light .btn-scheme:hover { background: {{ $schemes['light']['button_hover_bg'] }}; color: {{ $schemes['light']['button_hover_text'] }}; }

      .section--neutral .btn-scheme { background: {{ $schemes['neutral']['button_bg'] }}; color: {{ $schemes['neutral']['button_text'] }}; border-color: {{ $schemes['neutral']['button_border'] }}; }
      .section--neutral .btn-scheme:hover { background: {{ $schemes['neutral']['button_hover_bg'] }}; color: {{ $schemes['neutral']['button_hover_text'] }}; }

      .section--dark .btn-scheme { background: {{ $schemes['dark']['button_bg'] }}; color: {{ $schemes['dark']['button_text'] }}; border-color: {{ $schemes['dark']['button_border'] }}; }
      .section--dark .btn-scheme:hover { background: {{ $schemes['dark']['button_hover_bg'] }}; color: {{ $schemes['dark']['button_hover_text'] }}; }

      .section--gradient .btn-scheme { background: {{ $schemes['gradient']['button_bg'] }}; color: {{ $schemes['gradient']['button_text'] }}; border-color: {{ $schemes['gradient']['button_border'] }}; }
      .section--gradient .btn-scheme:hover { background: {{ $schemes['gradient']['button_hover_bg'] }}; color: {{ $schemes['gradient']['button_hover_text'] }}; }

      .section--transparent .btn-scheme { background: {{ $schemes['transparent']['button_bg'] }}; color: {{ $schemes['transparent']['button_text'] }}; border-color: {{ $schemes['transparent']['button_border'] }}; }
      .section--transparent .btn-scheme:hover { background: {{ $schemes['transparent']['button_hover_bg'] }}; color: {{ $schemes['transparent']['button_hover_text'] }}; }

      /* Header and Footer sections */
      .section--header { background: {{ $schemes['header']['bg'] }} !important; color: {{ $schemes['header']['text'] }} !important; }
      .section--header h1,.section--header h2,.section--header h3,.section--header h4,.section--header h5,.section--header h6 { color: {{ $schemes['header']['heading'] }} !important; }
      .section--header a { color: {{ $schemes['header']['link'] }} !important; }
      .section--header i { color: {{ $schemes['header']['heading'] }} !important; }
      .section--header .card { background: {{ $schemes['header']['bg'] }} !important; color: {{ $schemes['header']['text'] }} !important; border-color: {{ $schemes['header']['border'] }} !important; }
      .section--header .card h1,.section--header .card h2,.section--header .card h3,.section--header .card h4,.section--header .card h5,.section--header .card h6 { color: {{ $schemes['header']['heading'] }} !important; }
      .section--header .btn-scheme { background: {{ $schemes['header']['nav_bg'] }}; color: {{ $schemes['header']['nav_text'] }}; border-color: {{ $schemes['header']['border'] }}; }
      .section--header .btn-scheme:hover { background: {{ $schemes['header']['nav_link_hover'] }}; color: {{ $schemes['header']['nav_bg'] }}; }

      .section--footer { background: {{ $schemes['footer']['bg'] }} !important; color: {{ $schemes['footer']['text'] }} !important; }
      .section--footer h1,.section--footer h2,.section--footer h3,.section--footer h4,.section--footer h5,.section--footer h6 { color: {{ $schemes['footer']['heading'] }} !important; }
      .section--footer a { color: {{ $schemes['footer']['link'] }} !important; }
      .section--footer i { color: {{ $schemes['footer']['heading'] }} !important; }
      .section--footer .card { background: {{ $schemes['footer']['bg'] }} !important; color: {{ $schemes['footer']['text'] }} !important; border-color: {{ $schemes['footer']['border'] }} !important; }
      .section--footer .card h1,.section--footer .card h2,.section--footer .card h3,.section--footer .card h4,.section--footer .card h5,.section--footer .card h6 { color: {{ $schemes['footer']['heading'] }} !important; }
      .section--footer .btn-scheme { background: {{ $schemes['footer']['nav_bg'] }}; color: {{ $schemes['footer']['nav_text'] }}; border-color: {{ $schemes['footer']['border'] }}; }
      .section--footer .btn-scheme:hover { background: {{ $schemes['footer']['nav_link_hover'] }}; color: {{ $schemes['footer']['nav_bg'] }}; }

      /* Shadow and divider colors for each scheme */
      .section--primary { --scheme-shadow: {{ $schemes['primary']['shadow_color'] ?? 'rgba(0,0,0,0.15)' }}; --scheme-divider: {{ $schemes['primary']['divider_color'] ?? 'rgba(255,255,255,0.2)' }}; }
      .section--secondary { --scheme-shadow: {{ $schemes['secondary']['shadow_color'] ?? 'rgba(0,0,0,0.15)' }}; --scheme-divider: {{ $schemes['secondary']['divider_color'] ?? 'rgba(255,255,255,0.2)' }}; }
      .section--accent { --scheme-shadow: {{ $schemes['accent']['shadow_color'] ?? 'rgba(0,0,0,0.15)' }}; --scheme-divider: {{ $schemes['accent']['divider_color'] ?? 'rgba(255,255,255,0.2)' }}; }
      .section--light { --scheme-shadow: {{ $schemes['light']['shadow_color'] ?? 'rgba(0,0,0,0.1)' }}; --scheme-divider: {{ $schemes['light']['divider_color'] ?? 'rgba(0,0,0,0.1)' }}; }
      .section--neutral { --scheme-shadow: {{ $schemes['neutral']['shadow_color'] ?? 'rgba(0,0,0,0.1)' }}; --scheme-divider: {{ $schemes['neutral']['divider_color'] ?? 'rgba(0,0,0,0.1)' }}; }
      .section--dark { --scheme-shadow: {{ $schemes['dark']['shadow_color'] ?? 'rgba(0,0,0,0.3)' }}; --scheme-divider: {{ $schemes['dark']['divider_color'] ?? 'rgba(255,255,255,0.2)' }}; }
      .section--gradient { --scheme-shadow: {{ $schemes['gradient']['shadow_color'] ?? 'rgba(0,0,0,0.2)' }}; --scheme-divider: {{ $schemes['gradient']['divider_color'] ?? 'rgba(255,255,255,0.3)' }}; }
      .section--transparent { --scheme-shadow: {{ $schemes['transparent']['shadow_color'] ?? 'rgba(0,0,0,0.1)' }}; --scheme-divider: {{ $schemes['transparent']['divider_color'] ?? 'rgba(0,0,0,0.1)' }}; }
      .section--header { --scheme-shadow: {{ $schemes['header']['shadow_color'] ?? 'rgba(0,0,0,0.1)' }}; --scheme-divider: {{ $schemes['header']['divider_color'] ?? 'rgba(0,0,0,0.1)' }}; }
      .section--footer { --scheme-shadow: {{ $schemes['footer']['shadow_color'] ?? 'rgba(0,0,0,0.3)' }}; --scheme-divider: {{ $schemes['footer']['divider_color'] ?? 'rgba(255,255,255,0.2)' }}; }

      /* Navbar styles using header scheme colors */
      .navbar { background-color: {{ $schemes['header']['nav_bg'] ?? '#ffffff' }} !important; border-bottom: 1px solid {{ $schemes['header']['nav_border'] ?? 'rgba(0,0,0,0.1)' }}; }
      .navbar .navbar-brand { color: {{ $schemes['header']['nav_text'] ?? '#374151' }} !important; }
      .navbar .nav-link { color: {{ $schemes['header']['nav_link'] ?? '#3B82F6' }} !important; }
      .navbar .nav-link:hover { color: {{ $schemes['header']['nav_link_hover'] ?? '#F59E0B' }} !important; }
      .navbar .navbar-toggler { border-color: {{ $schemes['header']['nav_border'] ?? 'rgba(0,0,0,0.1)' }}; }
      .navbar .navbar-toggler-icon { filter: brightness(0) invert(1); }

      /* Header styles */
      .minisite-header { background-color: {{ $schemes['header']['bg'] ?? '#ffffff' }}; color: {{ $schemes['header']['text'] ?? '#374151' }}; border-bottom: 1px solid {{ $schemes['header']['border'] ?? 'rgba(0,0,0,0.1)' }}; }
      .minisite-header a { color: {{ $schemes['header']['link'] ?? '#3B82F6' }}; }
      .minisite-header a:hover { color: {{ $schemes['header']['link_hover'] ?? '#F59E0B' }}; }

      /* Footer styles */
      .minisite-footer { background-color: {{ $schemes['footer']['bg'] ?? '#1a1a2e' }}; color: {{ $schemes['footer']['text'] ?? '#ffffff' }}; border-top: 1px solid {{ $schemes['footer']['border'] ?? 'rgba(255,255,255,0.1)' }}; }
      .minisite-footer a { color: {{ $schemes['footer']['link'] ?? '#ffffff' }}; }
      .minisite-footer a:hover { color: {{ $schemes['footer']['link_hover'] ?? 'rgba(255,255,255,0.8)' }}; }
    </style>

    @inertiaHead
</head>
<body>
    @inertia
</body>
</html>
