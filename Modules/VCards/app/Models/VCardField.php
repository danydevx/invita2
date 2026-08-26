<?php

namespace Modules\VCards\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VCardField extends Model
{
    protected $table = 'vcard_fields';

    protected $fillable = [
        'vcard_id',
        'field_type_key',
        'label',
        'config',
        'sort_order',
        'active',
    ];

    protected $casts = [
        'config' => 'array',
        'active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function vcard(): BelongsTo
    {
        return $this->belongsTo(VCard::class, 'vcard_id');
    }

    public function getFieldTypeDefinitionAttribute(): ?array
    {
        return VCardFieldType::getDefinition($this->field_type_key);
    }

    public function getDisplayValueAttribute(): ?string
    {
        $config = $this->config;

        return match ($this->field_type_key) {
            'website', 'link' => $config['url'] ?? null,
            'instagram', 'twitter', 'facebook', 'linkedin', 'tiktok', 'youtube', 'github' => $config['username'] ?? null,
            'phone', 'whatsapp' => $config['phone'] ?? null,
            'email' => $config['email'] ?? null,
            'address' => $this->formatAddress($config),
            'note' => $config['text'] ?? null,
            'pdf' => $config['label'] ?? null,
            default => is_array($config) ? implode(', ', $config) : $config,
        };
    }

    protected function formatAddress(array $config): string
    {
        $parts = array_filter([
            $config['street'] ?? null,
            $config['city'] ?? null,
            $config['state'] ?? null,
            $config['postal_code'] ?? null,
            $config['country'] ?? null,
        ]);

        return implode(', ', $parts);
    }

    public function getActionUrlAttribute(): ?string
    {
        $config = $this->config;

        return match ($this->field_type_key) {
            'website' => $config['url'] ?? null,
            'link' => $config['url'] ?? null,
            'instagram' => 'https://instagram.com/' . ($config['username'] ?? ''),
            'twitter' => 'https://x.com/' . ($config['username'] ?? ''),
            'facebook' => 'https://facebook.com/' . ($config['username'] ?? ''),
            'linkedin' => 'https://linkedin.com/in/' . ($config['username'] ?? ''),
            'tiktok' => 'https://tiktok.com/@' . ($config['username'] ?? ''),
            'youtube' => $config['url'] ?? null,
            'github' => 'https://github.com/' . ($config['username'] ?? ''),
            'phone' => 'tel:' . ($config['phone'] ?? ''),
            'whatsapp' => 'https://wa.me/' . ($config['phone'] ?? ''),
            'email' => 'mailto:' . ($config['email'] ?? ''),
            'telegram' => 'https://t.me/' . ($config['username'] ?? ''),
            'discord' => $config['invite_url'] ?? null,
            default => null,
        };
    }
}
