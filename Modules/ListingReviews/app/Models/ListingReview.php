<?php

namespace Modules\ListingReviews\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ListingReview extends Model
{

    protected $table = 'listing_reviews';

    protected $fillable = [
        'listing_id',
        'business_location_id',
        'client_name',
        'company',
        'comment',
        'rating',
        'google_link',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(\Modules\Listings\Models\Listing::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(\Modules\ListingLocations\Models\ListingLocation::class, 'business_location_id');
    }
}
