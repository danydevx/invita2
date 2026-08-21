<?php

namespace Modules\ListingGallery\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ListingGalleryImage extends Model
{

    protected $table = 'listing_gallery_images';

    protected $fillable = [
        'listing_id',
        'business_gallery_id',
        'business_location_id',
        'path',
        'filename',
        'original_name',
        'extension',
        'mime_type',
        'size',
        'title',
        'description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'size' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(\Modules\Listings\Models\Listing::class);
    }

    public function gallery(): BelongsTo
    {
        return $this->belongsTo(ListingGallery::class, 'business_gallery_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(\Modules\ListingLocations\Models\ListingLocation::class, 'business_location_id');
    }
}
