<?php

namespace Modules\VCards\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Collection;

class VCardSelectedMenuCategory extends Model
{
    protected $table = 'vcard_selected_menu_categories';

    protected $fillable = [
        'vcard_id',
        'category_id',
        'product_ids',
        'sort_order',
    ];

    protected $casts = [
        'product_ids' => 'array',
        'sort_order' => 'integer',
    ];

    public function vcard(): BelongsTo
    {
        return $this->belongsTo(VCard::class, 'vcard_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(\Modules\ListingRestaurantMenu\Entities\MenuCategory::class, 'category_id');
    }

    public function getProductsAttribute(): Collection
    {
        if (empty($this->product_ids) || !is_array($this->product_ids)) {
            return new Collection();
        }

        return \Modules\ListingRestaurantMenu\Entities\MenuProduct::whereIn('id', $this->product_ids)
            ->orderByRaw("FIELD(id, " . implode(',', $this->product_ids) . ")")
            ->get();
    }
}
