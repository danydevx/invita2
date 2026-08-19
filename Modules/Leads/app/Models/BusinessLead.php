<?php

namespace Modules\Leads\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Leads\Enums\LeadStatus;
use Modules\Leads\Enums\LeadSource;

class BusinessLead extends Model
{
    protected $fillable = [
        'business_id',
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

    public function business(): BelongsTo
    {
        return $this->belongsTo(\Modules\Businesses\Models\Business::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(\Modules\Locations\Models\BusinessLocation::class, 'business_location_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function contactForm(): BelongsTo
    {
        return $this->belongsTo(\Modules\ContactForm\Models\BusinessContactForm::class, 'business_contact_form_id');
    }
}
