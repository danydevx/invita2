<?php

namespace Modules\VCards\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
        'search_engine_indexing',
        'renew',
        'tracking_code',
        'paused',
        'ai_chat_enabled',
        'meta_pixel_id',
        'google_analytics_id',
        'google_webmasters_verification',
        'bing_webmasters_verification',
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
        'search_engine_indexing' => 'boolean',
        'renew' => 'boolean',
        'paused' => 'boolean',
        'ai_chat_enabled' => 'boolean',
        'tracking_code' => 'array',
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

    public function seoSetting(): HasOne
    {
        return $this->hasOne(VCardSeoSetting::class, 'vcard_id');
    }

    public function packages(): HasMany
    {
        return $this->hasMany(VCardPackage::class, 'vcard_id')->where('active', true)->orderBy('sort_order');
    }

    public function sections(): HasMany
    {
        return $this->hasMany(VCardSection::class, 'vcard_id');
    }

    public function selectedServices(): HasMany
    {
        return $this->hasMany(VCardSelectedService::class, 'vcard_id')->orderBy('sort_order');
    }

    public function selectedPackages(): HasMany
    {
        return $this->hasMany(VCardSelectedPackage::class, 'vcard_id')->orderBy('sort_order');
    }

    public function selectedGallery(): HasOne
    {
        return $this->hasOne(VCardSelectedGallery::class, 'vcard_id');
    }

    public function selectedProducts(): HasMany
    {
        return $this->hasMany(VCardSelectedProduct::class, 'vcard_id')->orderBy('sort_order');
    }

    public function selectedTestimonials(): HasMany
    {
        return $this->hasMany(VCardSelectedTestimonial::class, 'vcard_id')->orderBy('sort_order');
    }

    public function businessHours(): HasMany
    {
        return $this->hasMany(VCardBusinessHour::class, 'vcard_id')->orderBy('day_of_week');
    }

    public function selectedMenuCategories(): HasMany
    {
        return $this->hasMany(VCardSelectedMenuCategory::class, 'vcard_id')->orderBy('sort_order');
    }

    public function selectedLocation(): HasOne
    {
        return $this->hasOne(VCardSelectedLocation::class, 'vcard_id');
    }

    public function selectedFeatures(): HasMany
    {
        return $this->hasMany(VCardSelectedFeature::class, 'vcard_id')->orderBy('sort_order');
    }

    public function getServicesAttribute(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->selectedServices->map(fn($s) => $s->service)->filter();
    }

    public function getPackagesAttribute(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->selectedPackages->map(fn($p) => $p->package)->filter();
    }

    public function getGalleryAttribute(): ?\Modules\ListingGallery\Models\ListingGallery
    {
        return $this->selectedGallery?->gallery;
    }

    public function getProductsAttribute(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->selectedProducts->map(fn($p) => $p->product)->filter();
    }

    public function getTestimonialsAttribute(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->selectedTestimonials->map(fn($t) => $t->review)->filter();
    }

    public function getMenuAttribute(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->selectedMenuCategories->map(function ($sc) {
            return [
                'id' => $sc->category?->id,
                'title' => $sc->category?->title,
                'description' => $sc->category?->description,
                'products' => $sc->products->map(fn($p) => [
                    'id' => $p->id,
                    'title' => $p->title,
                    'description' => $p->description,
                    'price' => $p->price,
                    'image' => $p->image,
                ])->values(),
            ];
        })->filter()->values();
    }

    public function getLocationAttribute(): ?array
    {
        $loc = $this->selectedLocation?->location;
        if (!$loc) {
            return null;
        }
        return [
            'id' => $loc->id,
            'name' => $loc->name,
            'address_line_1' => $loc->address_line_1,
            'address_line_2' => $loc->address_line_2,
            'city' => $loc->city,
            'state' => $loc->state,
            'postal_code' => $loc->postal_code,
            'country' => $loc->country,
            'phone' => $loc->phone,
            'email' => $loc->email,
            'latitude' => $loc->latitude,
            'longitude' => $loc->longitude,
            'directions_url' => $loc->directions_url,
        ];
    }

    public function getFeaturesAttribute(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->selectedFeatures->map(fn($sf) => $sf->feature)->filter();
    }

    public function getAboutAttribute(): ?array
    {
        $about = $this->listing?->about;
        if (!$about) {
            return null;
        }
        return [
            'title' => $about->title,
            'subtitle' => $about->subtitle,
            'description' => $about->description,
            'image_path' => $about->image_path,
        ];
    }

    public function getSectionsConfigAttribute(): array
    {
        $defaults = [
            'appointments' => false,
            'services' => false,
            'packages' => false,
            'gallery' => false,
            'products' => false,
            'testimonials' => false,
            'business_hours' => false,
            'menu' => false,
            'contact_form' => false,
            'location' => false,
            'features' => false,
            'about' => false,
        ];

        $saved = $this->sections->pluck('enabled', 'section_key')->toArray();

        return array_merge($defaults, $saved);
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

        // Name
        $fullName = $this->full_name;
        if ($fullName) {
            $lines[] = "FN:{$fullName}";
            $nameParts = array_filter([
                $this->last_name ?? '',
                $this->first_name ?? '',
                $this->middle_name ?? '',
                $this->prefix ?? '',
            ]);
            $lines[] = "N:" . implode(';', array_pad($nameParts, 4, ''));
        }

        // Preferred name
        if ($this->preferred_name) {
            $lines[] = "NICKNAME:{$this->preferred_name}";
        }

        // Profile photo
        if ($this->profile_photo) {
            $photoUrl = url("/storage/{$this->profile_photo}");
            $lines[] = "PHOTO;VALUE=URI:{$photoUrl}";
        }

        // Company and organization
        if ($this->company) {
            $lines[] = "ORG:{$this->company}";
        }

        if ($this->title) {
            $lines[] = "TITLE:{$this->title}";
        }

        if ($this->department) {
            $lines[] = "ROLE:{$this->department}";
        }

        // Headline / Description
        if ($this->headline) {
            $lines[] = "NOTE:{$this->headline}";
        }

        // Address
        if ($this->address || $this->city || $this->state || $this->zip || $this->country) {
            $addressParts = [
                '', // PO Box
                '', // Extended address
                $this->address ?? '',
                $this->city ?? '',
                $this->state ?? '',
                $this->zip ?? '',
                $this->country ?? '',
            ];
            $lines[] = "ADR;TYPE=WORK:" . implode(';', $addressParts);
        }

        // Contacts (phones, whatsapp, etc.)
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

        // Fields (email, website, social)
        foreach ($this->activeFields as $field) {
            if ($field->field_type_key === 'email' && !empty($field->config['email'])) {
                $lines[] = "EMAIL;TYPE=WORK:{$field->config['email']}";
            } elseif ($field->field_type_key === 'website' && !empty($field->config['url'])) {
                $lines[] = "URL;TYPE=WORK:{$field->config['url']}";
            } elseif ($field->field_type_key === 'linkedin' && !empty($field->config['url'])) {
                $lines[] = "X-SOCIALPROFILE;TYPE=linkedin:{$field->config['url']}";
            } elseif ($field->field_type_key === 'facebook' && !empty($field->config['url'])) {
                $lines[] = "X-SOCIALPROFILE;TYPE=facebook:{$field->config['url']}";
            } elseif ($field->field_type_key === 'twitter' && !empty($field->config['url'])) {
                $lines[] = "X-SOCIALPROFILE;TYPE=twitter:{$field->config['url']}";
            } elseif ($field->field_type_key === 'instagram' && !empty($field->config['url'])) {
                $lines[] = "X-SOCIALPROFILE;TYPE=instagram:{$field->config['url']}";
            } elseif ($field->field_type_key === 'tiktok' && !empty($field->config['url'])) {
                $lines[] = "X-SOCIALPROFILE;TYPE=tiktok:{$field->config['url']}";
            }
        }

        // Logo
        if ($this->logo) {
            $logoUrl = url("/storage/{$this->logo}");
            $lines[] = "LOGO;VALUE=URI:{$logoUrl}";
        }

        $lines[] = 'END:VCARD';

        return implode("\r\n", $lines);
    }
}
