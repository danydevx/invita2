<?php

namespace Modules\VCards\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VCardSelectedLocation extends Model
{
    protected $table = 'vcard_selected_location';

    protected $fillable = [
        'vcard_id',
        'location_id',
    ];

    public function vcard(): BelongsTo
    {
        return $this->belongsTo(VCard::class, 'vcard_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(\Modules\ListingLocations\Models\ListingLocation::class, 'location_id');
    }
}
