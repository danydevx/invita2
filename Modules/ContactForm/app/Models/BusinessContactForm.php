<?php

namespace Modules\ContactForm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Businesses\Models\Business;
use Modules\Leads\Models\BusinessLead;
use Illuminate\Support\Str;

class BusinessContactForm extends Model
{
    protected $fillable = [
        'business_id',
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

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function fields(): HasMany
    {
        return $this->hasMany(BusinessContactFormField::class)->orderBy('order');
    }

    public function activeFields(): HasMany
    {
        return $this->hasMany(BusinessContactFormField::class)
            ->where('is_active', true)
            ->orderBy('order');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(BusinessLead::class, 'business_contact_form_id');
    }

    public static function findByShortcode(string $shortcode): ?self
    {
        return static::where('shortcode', $shortcode)->first();
    }
}
