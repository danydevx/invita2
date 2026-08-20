<?php

namespace Modules\ListingContactForm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Listings\Models\Listing;

class ListingContactFormField extends Model
{

    protected $table = 'listing_contact_form_fields';

    protected $fillable = [
        'listing_id',
        'business_contact_form_id',
        'field_name',
        'field_type',
        'label',
        'placeholder',
        'options',
        'is_required',
        'is_active',
        'order',
        'row',
        'width',
        'field_config',
    ];

    protected $casts = [
        'options' => 'array',
        'field_config' => 'array',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'row' => 'integer',
    ];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function contactForm(): BelongsTo
    {
        return $this->belongsTo(ListingContactForm::class, 'business_contact_form_id');
    }

    public function getConfig(): array
    {
        return $this->field_config ?? [
            'type' => $this->field_type,
            'label' => $this->label,
            'name' => $this->field_name,
            'description' => '',
            'placeholder' => $this->placeholder ?? '',
            'required' => $this->is_required,
            'className' => 'form-control',
            'maxlength' => 255,
            'value' => '',
            'options' => $this->options ?? [],
            'row' => $this->row ?? 1,
            'width' => $this->width ?? 'full',
        ];
    }
}
