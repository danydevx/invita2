<?php

namespace Modules\ListingLeads\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\ListingLeads\Enums\LeadStatus;
use Modules\ListingLeads\Enums\LeadSource;

class ListingLead extends Model
{

    protected $table = 'listing_leads';

    protected $fillable = [
        'listing_id',
        'business_contact_form_id',
        'business_location_id',
        'user_id',
        'name',
        'email',
        'phone',
        'notes',
        'status',
        'source',
        'ip_address',
        'metadata',
    ];

    protected $casts = [
        'status' => LeadStatus::class,
        'source' => LeadSource::class,
        'metadata' => 'array',
    ];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(\Modules\Listings\Models\Listing::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(\Modules\ListingLocations\Models\ListingLocation::class, 'business_location_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function contactForm(): BelongsTo
    {
        return $this->belongsTo(\Modules\ListingContactForm\Models\ListingContactForm::class, 'business_contact_form_id');
    }
}
