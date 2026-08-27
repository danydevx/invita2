<?php

namespace Modules\VCards\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VCardSelectedTestimonial extends Model
{
    protected $table = 'vcard_selected_testimonials';

    protected $fillable = [
        'vcard_id',
        'review_id',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function vcard(): BelongsTo
    {
        return $this->belongsTo(VCard::class, 'vcard_id');
    }

    public function review(): BelongsTo
    {
        return $this->belongsTo(\Modules\ListingReviews\Models\ListingReview::class, 'review_id');
    }
}
