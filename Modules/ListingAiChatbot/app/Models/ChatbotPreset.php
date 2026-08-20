<?php

namespace Modules\ListingAiChatbot\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class ChatbotPreset extends Model
{
    protected $table = 'chatbot_presets';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'listing_type',
        'system_prompt_template',
        'chatbot_name_template',
        'greeting_message',
        'fallback_message',
        'personality',
        'language',
        'configuration',
        'initial_suggestions',
        'context_ids',
        'is_active',
        'is_system',
        'listing_id',
        'created_by',
    ];

    protected $casts = [
        'configuration' => 'array',
        'initial_suggestions' => 'array',
        'context_ids' => 'array',
        'is_active' => 'boolean',
        'is_system' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(\Modules\Listings\Models\Listing::class, 'listing_id');
    }

    public function copiedFrom(): BelongsTo
    {
        return $this->belongsTo(self::class, 'copied_from_id');
    }

    public function scopeGlobal(Builder $query): Builder
    {
        return $query->whereNull('listing_id');
    }

    public function scopeForBusiness(Builder $query, int $businessId): Builder
    {
        return $query->where('listing_id', $businessId);
    }

    public function scopeForBusinessWithGlobal(Builder $query, int $businessId): Builder
    {
        return $query->where(function ($q) use ($businessId) {
            $q->whereNull('listing_id')
              ->orWhere('listing_id', $businessId);
        });
    }

    public function getConfigValue(string $key, mixed $default = null): mixed
    {
        return $this->configuration[$key] ?? $default;
    }

    public static function getActivePresets(?int $businessId = null)
    {
        $query = self::where('is_active', true);

        if ($businessId) {
            $query->forBusinessWithGlobal($businessId);
        }

        return $query->orderBy('is_system', 'desc')
            ->orderBy('name')
            ->get();
    }

    public static function getForListingType(?string $type, ?int $businessId = null)
    {
        $query = self::where('is_active', true)
            ->where(function ($q) use ($type) {
                $q->whereNull('listing_type')
                  ->orWhere('listing_type', $type);
            });

        if ($businessId) {
            $query->forBusinessWithGlobal($businessId);
        }

        return $query->orderBy('is_system', 'desc')
            ->orderBy('name')
            ->get();
    }

    public static function slugExists(string $slug, ?int $exceptId = null, ?int $businessId = null): bool
    {
        $query = self::where('slug', $slug);

        if ($businessId) {
            $query->forBusinessWithGlobal($businessId);
        }

        if ($exceptId) {
            $query->where('id', '!=', $exceptId);
        }

        return $query->exists();
    }

    public static function generateUniqueSlug(string $baseSlug, ?int $exceptId = null, ?int $businessId = null): string
    {
        $slug = $baseSlug;
        $counter = 1;

        while (self::slugExists($slug, $exceptId, $businessId)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
