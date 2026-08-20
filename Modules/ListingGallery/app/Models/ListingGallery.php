<?php

namespace Modules\ListingGallery\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ListingGallery extends Model
{

    protected $table = 'listing_galleries';

    protected $fillable = [
        'listing_id',
        'name',
        'description',
        'is_primary',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function (self $gallery) {
            if ($gallery->is_primary && $gallery->name !== 'Galería principal') {
                $gallery->name = 'Galería principal';
            }
        });

        static::creating(function (self $gallery) {
            if (! $gallery->is_primary) {
                $existingPrimary = static::where('listing_id', $gallery->listing_id)
                    ->where('is_primary', true)
                    ->exists();

                if (! $existingPrimary) {
                    $gallery->is_primary = true;
                }
            }
        });

        static::updating(function (self $gallery) {
            if ($gallery->isDirty('name') && $gallery->is_primary && $gallery->getOriginal('is_primary')) {
                $gallery->name = $gallery->getOriginal('name');
            }
        });

        static::updated(function (self $gallery) {
            $shouldPromote = false;

            if ($gallery->is_primary === false && $gallery->getOriginal('is_primary')) {
                $shouldPromote = true;
            }

            if ($gallery->is_active === false && $gallery->getOriginal('is_active')) {
                $shouldPromote = true;
            }

            if ($shouldPromote) {
                static::promoteNextActive($gallery->listing_id, $gallery->id);
            }

            if ($gallery->is_primary === true && ! $gallery->getOriginal('is_primary')) {
                static::where('listing_id', $gallery->listing_id)
                    ->where('id', '!=', $gallery->id)
                    ->update(['is_primary' => false]);
            }
        });

        static::deleted(function (self $gallery) {
            if ($gallery->is_primary) {
                static::promoteNextActive($gallery->listing_id, null);
            }
        });
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(\Modules\Listings\Models\Listing::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ListingGalleryImage::class, 'business_gallery_id');
    }

    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function promoteNextActive(int $businessId, ?int $excludeId): void
    {
        $next = static::where('listing_id', $businessId)
            ->where('is_active', true)
            ->where('id', '!=', $excludeId ?? 0)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->first();

        if ($next) {
            static::where('listing_id', $businessId)->update(['is_primary' => false]);
            $next->update(['is_primary' => true]);
        }
    }

    public static function primaryFor(int $businessId): ?self
    {
        return static::where('listing_id', $businessId)
            ->where('is_active', true)
            ->where('is_primary', true)
            ->first();
    }
}
