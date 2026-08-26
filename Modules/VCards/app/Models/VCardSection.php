<?php

namespace Modules\VCards\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VCardSection extends Model
{
    protected $table = 'vcard_sections';

    protected $fillable = [
        'vcard_id',
        'section_key',
        'enabled',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    public function vcard(): BelongsTo
    {
        return $this->belongsTo(VCard::class, 'vcard_id');
    }
}
