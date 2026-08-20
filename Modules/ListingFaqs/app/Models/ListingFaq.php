<?php

namespace Modules\ListingFaqs\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ListingFaq extends Model
{

    protected $table = 'listing_faqs';

    protected $fillable = [
        'listing_id',
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

    public function listing(): BelongsTo
    {
        return $this->belongsTo(\Modules\Listings\Models\Listing::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ListingFaqCategory::class, 'category_id');
    }
}
