<?php

namespace Modules\VCards\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VCardSelectedProduct extends Model
{
    protected $table = 'vcard_selected_products';

    protected $fillable = [
        'vcard_id',
        'product_id',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function vcard(): BelongsTo
    {
        return $this->belongsTo(VCard::class, 'vcard_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(\Modules\ListingProducts\Models\ListingProduct::class, 'product_id');
    }
}
