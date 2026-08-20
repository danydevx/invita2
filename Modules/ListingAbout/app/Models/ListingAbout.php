<?php

namespace Modules\ListingAbout\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Listings\Models\Listing;

class ListingAbout extends Model
{
    protected $table = 'listing_abouts';

    protected $fillable = [
        'listing_id',
        'title',
        'subtitle',
        'description',
        'image_path',
        'logo_path',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }
}
