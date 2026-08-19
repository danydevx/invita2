<?php

namespace Modules\RestaurantMenu\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuProductVariant extends Model
{
    protected $table = 'menu_product_variants';

    protected $fillable = [
        'product_id',
        'title',
        'description',
        'price',
        'image',
        'active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'active' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(MenuProduct::class, 'product_id');
    }

    public function getDisplayPriceAttribute(): string
    {
        return number_format($this->price, 2);
    }
}