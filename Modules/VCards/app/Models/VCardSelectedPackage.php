<?php

namespace Modules\VCards\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VCardSelectedPackage extends Model
{
    protected $table = 'vcard_selected_packages';

    protected $fillable = [
        'vcard_id',
        'package_id',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function vcard(): BelongsTo
    {
        return $this->belongsTo(VCard::class, 'vcard_id');
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(\Modules\ListingPackages\Models\ListingPackage::class, 'package_id');
    }
}
