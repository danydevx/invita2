<?php

namespace Modules\Clients\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessClient extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'business_id',
        'contact_person',
        'company_name',
        'whatsapp',
        'website',
        'rfc',
        'address_line_1',
        'address_line_2',
        'neighborhood',
        'postal_code',
        'state_code',
        'municipality',
        'customer_name',
        'customer_email',
        'customer_phone',
        'status',
        'notes',
    ];

    protected $casts = [
        //
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(\Modules\Businesses\Models\Business::class);
    }
}
