<?php

namespace Modules\ListingProducts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ListingProduct extends Model
{
    protected $table = 'listing_products';

    protected $fillable = [
        'listing_id',
        'business_location_id',
        'category_id',
        'name',
        'slug',
        'description',
        'image',
        'price',
        'show_price',
        'compare_at_price',
        'sku',
        'barcode',
        'quantity',
        'is_active',
        'is_featured',
        'whatsapp_contact',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'compare_at_price' => 'decimal:2',
        'quantity' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'show_price' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(\Modules\Listings\Models\Listing::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(\Modules\ListingLocations\Models\ListingLocation::class, 'business_location_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ListingProductImage::class, 'listing_product_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ListingProductCategory::class, 'category_id');
    }
}
