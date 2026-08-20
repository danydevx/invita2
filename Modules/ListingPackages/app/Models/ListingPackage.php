<?php

namespace Modules\ListingPackages\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ListingPackage extends Model
{
    protected $table = 'listing_packages';

    protected $fillable = [
        'listing_id',
        'title',
        'short_description',
        'long_description',
        'image',
        'price',
        'promo_price',
        'whatsapp',
        'whatsapp_message',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'promo_price' => 'decimal:2',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(\Modules\Listings\Models\Listing::class, 'listing_id');
    }

    public function business(): BelongsTo
    {
        return $this->listing();
    }

    public function features(): HasMany
    {
        return $this->hasMany(PackageFeature::class, 'package_id')->orderBy('sort_order');
    }
}
