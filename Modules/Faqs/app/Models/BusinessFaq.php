<?php

namespace Modules\Faqs\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessFaq extends Model
{
    protected $fillable = [
        'business_id',
        'category_id',
        'question',
        'answer',
        'image',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(\Modules\Businesses\Models\Business::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BusinessFaqCategory::class, 'category_id');
    }
}
