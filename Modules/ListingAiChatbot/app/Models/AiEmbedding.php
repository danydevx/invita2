<?php

namespace Modules\ListingAiChatbot\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiEmbedding extends Model
{
    protected $table = 'ai_embeddings';

    protected $fillable = [
        'listing_id',
        'source_type',
        'source_id',
        'chunk_text',
        'embedding',
    ];

    protected $casts = [
        'source_id' => 'integer',
    ];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(\Modules\Listings\Models\Listing::class);
    }

    public function getEmbeddingArray(): array
    {
        if (is_string($this->embedding)) {
            return json_decode($this->embedding, true) ?? [];
        }
        return $this->embedding ?? [];
    }

    public function setEmbeddingArray(array $embedding): void
    {
        $this->embedding = json_encode($embedding);
    }
}
