<?php

namespace Modules\RestaurantMenu\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class MenuProduct extends Model
{
    protected $fillable = [
        'business_id',
        'category_id',
        'image',
        'title',
        'slug',
        'description',
        'base_price',
        'show_price',
        'featured',
        'active',
        'sort_order',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'show_price' => 'boolean',
        'featured' => 'boolean',
        'active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = static::generateUniqueSlug($product->business_id, $product->title, $product->id);
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(MenuCategory::class, 'category_id');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(MenuProductVariant::class, 'product_id')->orderBy('sort_order');
    }

    public function activeVariants(): HasMany
    {
        return $this->variants()->where('active', true)->orderBy('sort_order');
    }

    public function images(): HasMany
    {
        return $this->hasMany(MenuProductImage::class, 'product_id')->orderBy('sort_order');
    }

    public function getPriceAttribute()
    {
        $firstVariant = $this->activeVariants->first();
        return $firstVariant ? $firstVariant->price : $this->base_price;
    }

    public function getDisplayPriceAttribute(): ?string
    {
        if (!$this->show_price) {
            return null;
        }

        $price = $this->price;
        return $price ? number_format($price, 2) : null;
    }
}