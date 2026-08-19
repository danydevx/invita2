<?php

namespace Modules\SocialMedia\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessSocialNetwork extends Model
{
    protected $table = 'business_social_networks';

    protected $fillable = [
        'business_id',
        'platform',
        'url',
        'username',
        'icon_class',
        'is_active',
        'show_on_hero',
        'show_on_footer',
        'show_on_contact',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'show_on_hero' => 'boolean',
        'show_on_footer' => 'boolean',
        'show_on_contact' => 'boolean',
        'sort_order' => 'integer',
    ];

    public static $platforms = [
        'facebook' => [
            'name' => 'Facebook',
            'icon' => 'bi bi-facebook',
            'color' => '#1877F2',
        ],
        'instagram' => [
            'name' => 'Instagram',
            'icon' => 'bi bi-instagram',
            'color' => '#E4405F',
        ],
        'twitter' => [
            'name' => 'Twitter / X',
            'icon' => 'bi bi-twitter-x',
            'color' => '#000000',
        ],
        'linkedin' => [
            'name' => 'LinkedIn',
            'icon' => 'bi bi-linkedin',
            'color' => '#0A66C2',
        ],
        'youtube' => [
            'name' => 'YouTube',
            'icon' => 'bi bi-youtube',
            'color' => '#FF0000',
        ],
        'tiktok' => [
            'name' => 'TikTok',
            'icon' => 'bi bi-tiktok',
            'color' => '#000000',
        ],
        'whatsapp' => [
            'name' => 'WhatsApp',
            'icon' => 'bi bi-whatsapp',
            'color' => '#25D366',
        ],
        'telegram' => [
            'name' => 'Telegram',
            'icon' => 'bi bi-telegram',
            'color' => '#26A5E4',
        ],
        'pinterest' => [
            'name' => 'Pinterest',
            'icon' => 'bi bi-pinterest',
            'color' => '#E60023',
        ],
        'snapchat' => [
            'name' => 'Snapchat',
            'icon' => 'bi bi-snapchat',
            'color' => '#FFFC00',
        ],
        'threads' => [
            'name' => 'Threads',
            'icon' => 'bi bi-threads',
            'color' => '#000000',
        ],
        'reddit' => [
            'name' => 'Reddit',
            'icon' => 'bi bi-reddit',
            'color' => '#FF4500',
        ],
        'discord' => [
            'name' => 'Discord',
            'icon' => 'bi bi-discord',
            'color' => '#5865F2',
        ],
        'spotify' => [
            'name' => 'Spotify',
            'icon' => 'bi bi-spotify',
            'color' => '#1DB954',
        ],
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(\Modules\Businesses\Models\Business::class);
    }

    public function getIconAttribute(): string
    {
        return self::$platforms[$this->platform]['icon'] ?? 'bi bi-globe';
    }

    public function getNameAttribute(): string
    {
        return self::$platforms[$this->platform]['name'] ?? $this->platform;
    }

    public function getColorAttribute(): string
    {
        return self::$platforms[$this->platform]['color'] ?? '#666666';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForHero($query)
    {
        return $query->where('show_on_hero', true);
    }

    public function scopeForFooter($query)
    {
        return $query->where('show_on_footer', true);
    }

    public function scopeForContact($query)
    {
        return $query->where('show_on_contact', true);
    }
}
