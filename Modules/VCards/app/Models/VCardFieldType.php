<?php

namespace Modules\VCards\Models;

class VCardFieldType
{
    protected static array $types = [];

    public static function getAll(): array
    {
        if (empty(self::$types)) {
            self::$types = [
                [
                    'key' => 'website',
                    'name' => 'Website',
                    'category' => 'other',
                    'icon' => 'bi-globe',
                    'description' => 'Sitio web personal o de empresa',
                    'schema' => [
                        ['name' => 'url', 'type' => 'url', 'required' => true, 'label' => 'URL'],
                    ],
                ],
                [
                    'key' => 'link',
                    'name' => 'Link',
                    'category' => 'other',
                    'icon' => 'bi-link',
                    'description' => 'Enlace a cualquier recurso',
                    'schema' => [
                        ['name' => 'url', 'type' => 'url', 'required' => true, 'label' => 'URL'],
                        ['name' => 'label', 'type' => 'text', 'required' => false, 'label' => 'Etiqueta'],
                    ],
                ],
                [
                    'key' => 'email',
                    'name' => 'Email',
                    'category' => 'communication',
                    'icon' => 'bi-envelope',
                    'description' => 'Dirección de correo electrónico',
                    'schema' => [
                        ['name' => 'email', 'type' => 'email', 'required' => true, 'label' => 'Email'],
                    ],
                ],
                [
                    'key' => 'phone',
                    'name' => 'Phone',
                    'category' => 'communication',
                    'icon' => 'bi-telephone',
                    'description' => 'Número de teléfono',
                    'schema' => [
                        ['name' => 'country_code', 'type' => 'text', 'required' => false, 'label' => 'Código país'],
                        ['name' => 'phone', 'type' => 'tel', 'required' => true, 'label' => 'Teléfono'],
                        ['name' => 'extension', 'type' => 'text', 'required' => false, 'label' => 'Extensión'],
                    ],
                ],
                [
                    'key' => 'whatsapp',
                    'name' => 'WhatsApp',
                    'category' => 'communication',
                    'icon' => 'bi-whatsapp',
                    'description' => 'Número de WhatsApp',
                    'schema' => [
                        ['name' => 'country_code', 'type' => 'text', 'required' => false, 'label' => 'Código país'],
                        ['name' => 'phone', 'type' => 'tel', 'required' => true, 'label' => 'Teléfono'],
                    ],
                ],
                [
                    'key' => 'instagram',
                    'name' => 'Instagram',
                    'category' => 'social',
                    'icon' => 'bi-instagram',
                    'description' => 'Perfil de Instagram',
                    'schema' => [
                        ['name' => 'username', 'type' => 'text', 'required' => true, 'label' => 'Usuario'],
                    ],
                ],
                [
                    'key' => 'facebook',
                    'name' => 'Facebook',
                    'category' => 'social',
                    'icon' => 'bi-facebook',
                    'description' => 'Perfil o página de Facebook',
                    'schema' => [
                        ['name' => 'username', 'type' => 'text', 'required' => true, 'label' => 'Usuario o URL'],
                    ],
                ],
                [
                    'key' => 'linkedin',
                    'name' => 'LinkedIn',
                    'category' => 'social',
                    'icon' => 'bi-linkedin',
                    'description' => 'Perfil de LinkedIn',
                    'schema' => [
                        ['name' => 'username', 'type' => 'text', 'required' => true, 'label' => 'Usuario'],
                    ],
                ],
                [
                    'key' => 'twitter',
                    'name' => 'X.com',
                    'category' => 'social',
                    'icon' => 'bi-twitter-x',
                    'description' => 'Perfil de X (Twitter)',
                    'schema' => [
                        ['name' => 'username', 'type' => 'text', 'required' => true, 'label' => 'Usuario'],
                    ],
                ],
                [
                    'key' => 'youtube',
                    'name' => 'YouTube',
                    'category' => 'video',
                    'icon' => 'bi-youtube',
                    'description' => 'Canal o video de YouTube',
                    'schema' => [
                        ['name' => 'url', 'type' => 'url', 'required' => true, 'label' => 'URL del canal o video'],
                    ],
                ],
                [
                    'key' => 'tiktok',
                    'name' => 'TikTok',
                    'category' => 'video',
                    'icon' => 'bi-tiktok',
                    'description' => 'Perfil de TikTok',
                    'schema' => [
                        ['name' => 'username', 'type' => 'text', 'required' => true, 'label' => 'Usuario'],
                    ],
                ],
                [
                    'key' => 'spotify',
                    'name' => 'Spotify',
                    'category' => 'music',
                    'icon' => 'bi-spotify',
                    'description' => 'Perfil o playlist de Spotify',
                    'schema' => [
                        ['name' => 'url', 'type' => 'url', 'required' => true, 'label' => 'URL'],
                    ],
                ],
                [
                    'key' => 'github',
                    'name' => 'GitHub',
                    'category' => 'design',
                    'icon' => 'bi-github',
                    'description' => 'Perfil de GitHub',
                    'schema' => [
                        ['name' => 'username', 'type' => 'text', 'required' => true, 'label' => 'Usuario'],
                    ],
                ],
                [
                    'key' => 'telegram',
                    'name' => 'Telegram',
                    'category' => 'communication',
                    'icon' => 'bi-telegram',
                    'description' => 'Usuario de Telegram',
                    'schema' => [
                        ['name' => 'username', 'type' => 'text', 'required' => true, 'label' => 'Usuario'],
                    ],
                ],
                [
                    'key' => 'discord',
                    'name' => 'Discord',
                    'category' => 'communication',
                    'icon' => 'bi-discord',
                    'description' => 'Servidor o usuario de Discord',
                    'schema' => [
                        ['name' => 'invite_url', 'type' => 'url', 'required' => true, 'label' => 'URL de invitación'],
                    ],
                ],
                [
                    'key' => 'paypal',
                    'name' => 'PayPal',
                    'category' => 'payment',
                    'icon' => 'bi-paypal',
                    'description' => 'Link de PayPal para pagos',
                    'schema' => [
                        ['name' => 'url', 'type' => 'url', 'required' => true, 'label' => 'URL de PayPal'],
                    ],
                ],
                [
                    'key' => 'venmo',
                    'name' => 'Venmo',
                    'category' => 'payment',
                    'icon' => 'bi-credit-card',
                    'description' => 'Usuario de Venmo',
                    'schema' => [
                        ['name' => 'username', 'type' => 'text', 'required' => true, 'label' => 'Usuario'],
                    ],
                ],
                [
                    'key' => 'pdf',
                    'name' => 'PDF',
                    'category' => 'other',
                    'icon' => 'bi-file-pdf',
                    'description' => 'Documento PDF',
                    'schema' => [
                        ['name' => 'file', 'type' => 'file', 'required' => true, 'label' => 'Archivo PDF'],
                        ['name' => 'label', 'type' => 'text', 'required' => false, 'label' => 'Etiqueta'],
                    ],
                ],
                [
                    'key' => 'address',
                    'name' => 'Address',
                    'category' => 'other',
                    'icon' => 'bi-geo-alt',
                    'description' => 'Dirección física',
                    'schema' => [
                        ['name' => 'street', 'type' => 'text', 'required' => false, 'label' => 'Calle y número'],
                        ['name' => 'city', 'type' => 'text', 'required' => false, 'label' => 'Ciudad'],
                        ['name' => 'state', 'type' => 'text', 'required' => false, 'label' => 'Estado'],
                        ['name' => 'postal_code', 'type' => 'text', 'required' => false, 'label' => 'Código postal'],
                        ['name' => 'country', 'type' => 'text', 'required' => false, 'label' => 'País'],
                    ],
                ],
                [
                    'key' => 'note',
                    'name' => 'Nota',
                    'category' => 'other',
                    'icon' => 'bi-stickies',
                    'description' => 'Nota o texto libre',
                    'schema' => [
                        ['name' => 'text', 'type' => 'textarea', 'required' => true, 'label' => 'Texto'],
                    ],
                ],
            ];
        }

        return self::$types;
    }

    public static function getByCategory(string $category): array
    {
        return array_filter(self::getAll(), fn($type) => $type['category'] === $category);
    }

    public static function getDefinition(string $key): ?array
    {
        $types = self::getAll();
        foreach ($types as $type) {
            if ($type['key'] === $key) {
                return $type;
            }
        }
        return null;
    }

    public static function getCategories(): array
    {
        return [
            'social' => 'Social',
            'communication' => 'Comunicación',
            'video' => 'Video',
            'music' => 'Música',
            'payment' => 'Pago',
            'design' => 'Diseño',
            'other' => 'Otros',
        ];
    }

    public static function getMostPopular(): array
    {
        $popular = ['website', 'link', 'instagram', 'email', 'phone', 'linkedin', 'facebook', 'whatsapp'];
        $all = self::getAll();

        return array_values(array_filter($all, fn($type) => in_array($type['key'], $popular)));
    }

    public static function getGrouped(): array
    {
        $all = self::getAll();
        $grouped = [];

        foreach ($all as $type) {
            $category = $type['category'];
            if (!isset($grouped[$category])) {
                $grouped[$category] = [];
            }
            $grouped[$category][] = $type;
        }

        return $grouped;
    }
}
