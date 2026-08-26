<?php

namespace Modules\VCards\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VCardSelectedService extends Model
{
    protected $table = 'vcard_selected_services';

    protected $fillable = [
        'vcard_id',
        'service_id',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function vcard(): BelongsTo
    {
        return $this->belongsTo(VCard::class, 'vcard_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(\Modules\ListingServices\Models\ListingService::class, 'service_id');
    }
}
