<?php

namespace Modules\VCards\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VCardSelectedGallery extends Model
{
    protected $table = 'vcard_selected_galleries';

    protected $fillable = [
        'vcard_id',
        'gallery_id',
    ];

    public function vcard(): BelongsTo
    {
        return $this->belongsTo(VCard::class, 'vcard_id');
    }

    public function gallery(): BelongsTo
    {
        return $this->belongsTo(\Modules\ListingGallery\Models\ListingGallery::class, 'gallery_id');
    }
}
