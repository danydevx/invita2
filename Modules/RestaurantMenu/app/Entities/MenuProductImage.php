<?php

namespace Modules\RestaurantMenu\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuProductImage extends Model
{
    protected $table = 'menu_product_images';

    protected $fillable = [
        'product_id',
        'image',
        'sort_order',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(MenuProduct::class, 'product_id');
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->image);
    }
}