<?php

namespace Modules\ListingAiChatbot\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiContext extends Model
{
    protected $table = 'ai_contexts';

    protected $fillable = [
        'listing_id',
        'title',
        'content',
        'content_for_editing',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(\Modules\Listings\Models\Listing::class);
    }
}
