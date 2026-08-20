<?php

namespace Modules\ListingAiChatbot\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiConversation extends Model
{
    protected $table = 'ai_conversations';

    protected $fillable = [
        'listing_id',
        'session_id',
        'ip_address',
        'user_agent',
        'device_type',
        'country',
        'city',
        'country_code',
        'messages_count',
        'started_at',
        'last_activity_at',
    ];

    protected $casts = [
        'messages_count' => 'integer',
        'started_at' => 'datetime',
        'last_activity_at' => 'datetime',
    ];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(\Modules\Listings\Models\Listing::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(AiMessage::class, 'conversation_id');
    }

    public function incrementMessagesCount(): void
    {
        $this->increment('messages_count');
        $this->update(['last_activity_at' => now()]);
    }
}
