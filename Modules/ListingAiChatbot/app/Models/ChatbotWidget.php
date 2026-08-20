<?php

namespace Modules\ListingAiChatbot\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ChatbotWidget extends Model
{
    protected $table = 'chatbot_widgets';

    protected $fillable = [
        'listing_id',
        'public_key',
        'allowed_domain',
        'is_enabled',
        'version',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(\Modules\Listings\Models\Listing::class);
    }

    public function analytics(): HasMany
    {
        return $this->hasMany(ChatbotWidgetAnalytics::class, 'public_key', 'public_key');
    }

    public function scopeWherePublicKey($query, string $key)
    {
        return $query->where('public_key', $key);
    }

    public function isDomainAllowed(?string $domain): bool
    {
        if (empty($this->allowed_domain)) {
            return true;
        }

        if (!$domain) {
            return false;
        }

        $host = parse_url($domain, PHP_URL_HOST);

        if (str_starts_with($this->allowed_domain, '*.')) {
            $baseDomain = substr($this->allowed_domain, 2);
            return $host === $baseDomain || str_ends_with($host, '.' . $baseDomain);
        }

        return $host === $this->allowed_domain;
    }

    public function regeneratePublicKey(): void
    {
        $this->public_key = (string) Str::uuid();
        $this->save();
    }

    public static function generateForBusiness($business): self
    {
        return self::firstOrCreate(
            ['listing_id' => $business->id],
            [
                'public_key' => (string) Str::uuid(),
                'is_enabled' => false,
                'version' => '1.0.0',
            ]
        );
    }

    public function getAiSetting()
    {
        return ListingAiSetting::where('listing_id', $this->listing_id)->first();
    }
}
