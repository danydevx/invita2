<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title inertia>{{ config('app.name', 'Mi SaaS') }}</title>

    @php($adminTheme = $page['props']['theme']['css_variables']['fonts'] ?? [])
    @php($headingFont = $adminTheme['font_heading'] ?? null)
    @php($bodyFont = $adminTheme['font_body'] ?? null)
    @php($buttonsFont = $adminTheme['font_buttons'] ?? null)
    @php($uniqueFonts = array_unique(array_filter([$headingFont, $bodyFont, $buttonsFont])))

    @if(!empty($uniqueFonts))
      @php($fontFamilies = collect($uniqueFonts)->map(fn($f) => urlencode($f) . ':wght@400;500;600;700')->join('&family='))
      <link href="https://fonts.googleapis.com/css2?family={{ $fontFamilies }}&display=swap" rel="stylesheet">
    @endif

    @vite(['resources/less/admin.less', 'resources/js/app.js'])
    @inertiaHead
</head>
<body>
    @inertia
</body>
</html>
