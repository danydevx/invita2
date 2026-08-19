<?php

namespace Modules\RestaurantMenu\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class MenuCategory extends Model
{
    protected $fillable = [
        'business_id',
        'parent_id',
        'title',
        'description',
        'slug',
        'active',
        'sort_order',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = static::generateUniqueSlug($category->business_id, $category->title, $category->id);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('title') && !$category->isDirty('slug')) {
                $category->slug = static::generateUniqueSlug($category->business_id, $category->title, $category->id);
            }
        });
    }

    public static function generateUniqueSlug(int $businessId, string $title, ?int $excludeId = null): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        $query = static::where('business_id', $businessId)->where('slug', $slug);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        while ($query->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
            $query = static::where('business_id', $businessId)->where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
        }

        return $slug;
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(\Modules\Businesses\Models\Business::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(MenuCategory::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(MenuCategory::class, 'parent_id')->orderBy('sort_order');
    }

    public function products(): HasMany
    {
        return $this->hasMany(MenuProduct::class, 'category_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(MenuCategoryImage::class, 'category_id')->orderBy('sort_order');
    }

    public function activeProducts(): HasMany
    {
        return $this->products()->where('active', true)->orderBy('sort_order');
    }

    public function getNestedTitleAttribute(): string
    {
        if ($this->parent) {
            return $this->parent->nested_title . ' > ' . $this->title;
        }
        return $this->title;
    }
}