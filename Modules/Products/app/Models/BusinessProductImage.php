<?php

namespace Modules\Products\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessProductImage extends Model
{
    protected $fillable = [
        'business_product_id',
        'path',
        'filename',
        'original_name',
        'extension',
        'mime_type',
        'size',
        'sort_order',
        'is_primary',
    ];

    protected $casts = [
        'size' => 'integer',
        'sort_order' => 'integer',
        'is_primary' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(BusinessProduct::class, 'business_product_id');
    }
}
