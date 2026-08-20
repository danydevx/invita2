<?php

namespace Modules\ListingContactForm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Listings\Models\Listing;
use Modules\ListingLeads\Models\ListingLead;
use Illuminate\Support\Str;

class ListingContactForm extends Model
{

    protected $table = 'listing_contact_forms';

    protected $fillable = [
        'listing_id',
        'name',
        'description',
        'shortcode',
        'is_active',
        'success_message',
        'show_phone',
        'show_email',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'show_phone' => 'boolean',
        'show_email' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($form) {
            if (empty($form->shortcode)) {
                $form->shortcode = 'cf_' . Str::ulid()->toBase32();
            }
            if (empty($form->success_message)) {
                $form->success_message = 'Mensaje enviado correctamente. Nos pondremos en contacto pronto.';
            }
        });
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function fields(): HasMany
    {
        return $this->hasMany(ListingContactFormField::class, 'business_contact_form_id')->orderBy('order');
    }

    public function activeFields(): HasMany
    {
        return $this->hasMany(ListingContactFormField::class, 'business_contact_form_id')
            ->where('is_active', true)
            ->orderBy('order');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(ListingLead::class, 'business_contact_form_id');
    }

    public static function findByShortcode(string $shortcode): ?self
    {
        return static::where('shortcode', $shortcode)->first();
    }
}
