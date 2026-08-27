<?php

namespace Modules\VCards\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VCardSelectedFeature extends Model
{
    protected $table = 'vcard_selected_features';

    protected $fillable = [
        'vcard_id',
        'feature_id',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function vcard(): BelongsTo
    {
        return $this->belongsTo(VCard::class, 'vcard_id');
    }

    public function feature(): BelongsTo
    {
        return $this->belongsTo(\Modules\ListingFeatures\Models\Feature::class, 'feature_id');
    }
}
