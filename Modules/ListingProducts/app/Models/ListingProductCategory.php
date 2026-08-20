<?php

namespace Modules\ListingProducts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ListingProductCategory extends Model
{

    protected $table = 'listing_product_categories';

    protected $fillable = [
        'listing_id',
        'parent_id',
        'name',
        'slug',
        'description',
        'image',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = static::generateUniqueSlug($category->listing_id, $category->name, $category->id);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && !$category->isDirty('slug')) {
                $category->slug = static::generateUniqueSlug($category->listing_id, $category->name, $category->id);
            }
        });
    }

    public static function generateUniqueSlug(int $businessId, string $name, ?int $excludeId = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        $query = static::where('listing_id', $businessId)->where('slug', $slug);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        while ($query->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
            $query = static::where('listing_id', $businessId)->where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
        }

        return $slug;
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(\Modules\Listings\Models\Listing::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ListingProductCategory::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ListingProductCategory::class, 'parent_id')->orderBy('sort_order');
    }

    public function products(): HasMany
    {
        return $this->hasMany(ListingProduct::class, 'category_id');
    }

    public function activeProducts(): HasMany
    {
        return $this->products()->where('is_active', true)->orderBy('sort_order');
    }
}
