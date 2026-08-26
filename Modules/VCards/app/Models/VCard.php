<?php

namespace Modules\VCards\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Modules\Listings\Models\Listing;
use Modules\VCards\Enums\VCardDesign;
use Modules\VCards\Enums\VCardPronouns;
use Modules\VCards\Enums\VCardType;

class VCard extends Model
{
    protected $table = 'vcards';

    protected $fillable = [
        'listing_id',
        'vcard_team_id',
        'type',
        'name',
        'slug',
        'active',
        'design',
        'primary_color',
        'font',
        'profile_photo',
        'hero_background_image',
        'logo',
        'badge',
        'prefix',
        'first_name',
        'middle_name',
        'last_name',
        'accreditations',
        'preferred_name',
        'pronouns',
        'title',
        'department',
        'company',
        'headline',
        'views',
        'shape',
        'image_x',
        'image_y',
        'background_type',
        'gradient_direction',
        'pattern_key',
        'hero_image_alpha',
        'body_background_type',
        'body_primary_color',
        'body_gradient_direction',
        'body_pattern_key',
        'latitude',
        'longitude',
        'address',
        'city',
        'state',
        'country',
        'zip',
    ];

    protected $casts = [
        'type' => VCardType::class,
        'design' => VCardDesign::class,
        'pronouns' => VCardPronouns::class,
        'active' => 'boolean',
        'views' => 'integer',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($vcard) {
            if (empty($vcard->slug)) {
                $vcard->slug = static::generateUniqueSlug($vcard->name, $vcard->listing_id);
            }
        });

        static::updating(function ($vcard) {
            if ($vcard->isDirty('name') && !$vcard->isDirty('slug')) {
                $vcard->slug = static::generateUniqueSlug($vcard->name, $vcard->listing_id, $vcard->id);
            }
        });
    }

    public static function generateUniqueSlug(string $name, int $listingId, ?int $excludeId = null): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        $query = static::where('listing_id', $listingId)->where('slug', $slug);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        while ($query->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
            $query = static::where('listing_id', $listingId)->where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
        }

        return $slug;
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(VCardTeam::class, 'vcard_team_id');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(VCardContact::class, 'vcard_id')->orderBy('sort_order');
    }

    public function fields(): HasMany
    {
        return $this->hasMany(VCardField::class, 'vcard_id')->orderBy('sort_order');
    }

    public function activeFields(): HasMany
    {
        return $this->hasMany(VCardField::class, 'vcard_id')->where('active', true)->orderBy('sort_order');
    }

    public function getFullNameAttribute(): string
    {
        $parts = array_filter([
            $this->prefix,
            $this->first_name,
            $this->middle_name,
            $this->last_name,
        ]);

        return implode(' ', $parts);
    }

    public function getPublicUrlAttribute(): string
    {
        return url('/v/' . $this->slug);
    }

    public function getQrCodeUrlAttribute(): string
    {
        $encoded = urlencode($this->public_url);
        return "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={$encoded}";
    }

    public function incrementViews(): void
    {
        $this->increment('views');
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeForListing($query, int $listingId)
    {
        return $query->where('listing_id', $listingId);
    }

    public function toVCardString(): string
    {
        $lines = [
            'BEGIN:VCARD',
            'VERSION:3.0',
        ];

        $fullName = $this->full_name;
        if ($fullName) {
            $lines[] = "FN:{$fullName}";
            $nameParts = array_filter([$this->last_name, $this->first_name, $this->middle_name]);
            $lines[] = "N:" . implode(';', array_pad($nameParts, 3, ''));
        }

        if ($this->company) {
            $lines[] = "ORG:{$this->company}";
        }

        if ($this->title) {
            $lines[] = "TITLE:{$this->title}";
        }

        if ($this->department) {
            $lines[] = "ROLE:{$this->department}";
        }

        foreach ($this->contacts as $contact) {
            $type = strtoupper($contact->type->value);
            $value = $contact->value;
            if ($contact->type->value === 'whatsapp' || $contact->type->value === 'phone') {
                $prefix = $contact->country_code ? "+{$contact->country_code}" : '';
                $value = $prefix . $contact->value;
                if ($contact->extension) {
                    $value .= ";ext={$contact->extension}";
                }
            }
            $lines[] = "TEL;TYPE={$type}:{$value}";
        }

        foreach ($this->activeFields as $field) {
            if ($field->field_type_key === 'email' && !empty($field->config['email'])) {
                $lines[] = "EMAIL:{$field->config['email']}";
            } elseif ($field->field_type_key === 'website' && !empty($field->config['url'])) {
                $lines[] = "URL:{$field->config['url']}";
            }
        }

        $lines[] = 'END:VCARD';

        return implode("\r\n", $lines);
    }
}
