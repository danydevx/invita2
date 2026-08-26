<?php

namespace Modules\VCards\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VCardPackage extends Model
{
    protected $table = 'vcard_packages';

    protected $fillable = [
        'vcard_id',
        'name',
        'description',
        'price',
        'currency',
        'duration_days',
        'active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'duration_days' => 'integer',
        'active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function vcard(): BelongsTo
    {
        return $this->belongsTo(VCard::class, 'vcard_id');
    }
}
