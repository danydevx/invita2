<?php

namespace Modules\Promotions\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessPromotionImage extends Model
{
    protected $fillable = [
        'promotion_id',
        'path',
        'filename',
        'original_name',
        'extension',
        'mime_type',
        'size',
        'sort_order',
    ];

    protected $casts = [
        'size' => 'integer',
        'sort_order' => 'integer',
    ];

    public function promotion(): BelongsTo
    {
        return $this->belongsTo(BusinessPromotion::class, 'promotion_id');
    }
}
