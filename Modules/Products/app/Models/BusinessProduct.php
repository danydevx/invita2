<?php

namespace Modules\Products\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BusinessProduct extends Model
{
    protected $fillable = [
        'business_id',
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

    public function business(): BelongsTo
    {
        return $this->belongsTo(\Modules\Businesses\Models\Business::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(\Modules\Locations\Models\BusinessLocation::class, 'business_location_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(BusinessProductImage::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BusinessProductCategory::class, 'category_id');
    }
}
