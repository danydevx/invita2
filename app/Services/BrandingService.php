<?php

namespace App\Services;

class BrandingService
{
    public function forEmail(): array
    {
        $values = app(SettingService::class)->getMany([
            'branding.logo',
            'branding.primary_color',
            'branding.secondary_color',
            'branding.footer_text',
            'app.website',
            'app.email',
        ]);

        return [
            'name' => config('app.name', 'SaaS'),
            'email' => $values['app.email'] ?? config('mail.from.address'),
            'website' => $values['app.website'] ?? config('app.url'),
            'logo' => $values['branding.logo'] ?? null,
            'primary_color' => $values['branding.primary_color'] ?? '#2563eb',
            'secondary_color' => $values['branding.secondary_color'] ?? '#0f172a',
            'footer_text' => $values['branding.footer_text'] ?? 'Gracias por confiar en nosotros.',
        ];
    }
}
