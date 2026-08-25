<?php

namespace Modules\ClientFidelity\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FidelityReward extends Model
{
    use HasFactory;

    protected $table = 'fidelity_rewards';

    protected $fillable = [
        'listing_id',
        'title',
        'description',
        'image',
        'max_visits',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'max_visits' => 'integer',
        'sort_order' => 'integer',
    ];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(\Modules\Listings\Models\Listing::class);
    }

    public function cards(): HasMany
    {
        return $this->hasMany(ClientFidelityCard::class, 'fidelity_reward_id');
    }

    public function completions(): HasMany
    {
        return $this->hasMany(FidelityCardCompletion::class);
    }

    public function getCardsCountAttribute(): int
    {
        return $this->cards()->count();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSorted($query)
    {
        return $query->orderBy('sort_order')->orderBy('title');
    }
}
