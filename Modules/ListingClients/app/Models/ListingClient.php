<?php

namespace Modules\ListingClients\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ListingClient extends Model
{

    protected $table = 'listing_clients';

    use SoftDeletes;

    protected $fillable = [
        'listing_id',
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

    public function listing(): BelongsTo
    {
        return $this->belongsTo(\Modules\Listings\Models\Listing::class);
    }
}