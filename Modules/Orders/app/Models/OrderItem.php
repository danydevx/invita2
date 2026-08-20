<?php

namespace Modules\Orders\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Orders\Enums\ProductType;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_type',
        'product_id',
        'variant_id',
        'title',
        'quantity',
        'unit_price',
        'options',
        'subtotal',
    ];

    protected $casts = [
        'product_type' => ProductType::class,
        'options' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function getProduct()
    {
        if ($this->product_type === ProductType::MENU_PRODUCT) {
            return \Modules\ListingRestaurantMenu\Entities\MenuProduct::find($this->product_id);
        }
        return \Modules\ListingProducts\Models\ListingProduct::find($this->product_id);
    }

    public function getVariant()
    {
        if (!$this->variant_id) {
            return null;
        }

        if ($this->product_type === ProductType::MENU_PRODUCT) {
            return \Modules\ListingRestaurantMenu\Entities\MenuProductVariant::find($this->variant_id);
        }
        return null;
    }

    protected static function booted(): void
    {
        static::saving(function ($item) {
            $item->subtotal = $item->quantity * $item->unit_price;
        });
    }
}
