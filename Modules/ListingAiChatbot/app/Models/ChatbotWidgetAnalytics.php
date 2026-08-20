<?php

namespace Modules\ListingAiChatbot\Models;

use Illuminate\Database\Eloquent\Model;

class ChatbotWidgetAnalytics extends Model
{
    protected $table = 'chatbot_widget_analytics';

    public $timestamps = false;

    protected $fillable = [
        'public_key',
        'domain',
        'event_type',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    public static function track(string $publicKey, string $eventType, array $metadata = []): self
    {
        return self::create([
            'public_key' => $publicKey,
            'domain' => $metadata['domain'] ?? 'unknown',
            'event_type' => $eventType,
            'metadata' => $metadata,
            'created_at' => now(),
        ]);
    }
}
