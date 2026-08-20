<?php

namespace Modules\Listings\Models;

use App\Models\ModuleDefinition;
use App\Models\MinisiteTheme;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\ListingAbout\Models\ListingAbout;
use Modules\ListingAppointments\Models\ListingAppointment;
use Modules\ListingAppointments\Models\ListingAppointmentSlot;
use Modules\ListingAppointments\Models\ListingAvailability;
use Modules\ListingAppointments\Models\ListingAvailabilityException;
use Modules\Listings\Enums\ListingType;
use Modules\ListingModules\Models\ListingModule;
use Modules\ListingContactForm\Models\ListingContactForm;
use Modules\ListingContactForm\Models\ListingContactFormField;
use Modules\ListingFaqs\Models\ListingFaq;
use Modules\ListingFaqs\Models\ListingFaqCategory;
use Modules\ListingFeatures\Models\ListingFeature;
use Modules\ListingFeatures\Models\Feature;
use Modules\ListingGallery\Models\ListingGalleryImage;
use Modules\ListingHero\Models\ListingHero;
use Modules\ListingLeads\Models\ListingLead;
use Modules\ListingLocations\Models\ListingLocation;
use Modules\ListingProducts\Models\ListingProduct;
use Modules\ListingProducts\Models\ListingProductCategory;
use Modules\ListingPromotions\Models\ListingPromotion;
use Modules\ListingReviews\Models\ListingReview;
use Modules\ListingSeo\Models\ListingSeoSetting;
use Modules\ListingServices\Models\ListingService;

class Listing extends Model
{
    protected $table = 'listings';

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'listing_type',
        'description',
        'logo_path',
        'cover_image_path',
        'phone',
        'email',
        'website',
        'timezone',
        'currency',
        'settings',
        'minisite_theme_id',
        'is_active',
        'is_published',
    ];

    protected $casts = [
        'listing_type' => ListingType::class,
        'settings' => 'array',
        'is_active' => 'boolean',
        'is_published' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($listing) {
            $listing->syncAllModules();
            $listing->assignMinisiteTheme();
        });

        static::updated(function ($listing) {
            if ($listing->wasChanged('listing_type')) {
                $listing->assignMinisiteTheme();
            }
        });
    }

    public function assignMinisiteTheme(): void
    {
        if (! $this->minisite_theme_id) {
            $theme = MinisiteTheme::getByListingType($this->listing_type->value ?? 'generic');
            if ($theme) {
                $this->update(['minisite_theme_id' => $theme->id]);
            }
        }
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function locations(): HasMany
    {
        return $this->hasMany(ListingLocation::class, 'listing_id');
    }

    public function modules(): HasMany
    {
        return $this->hasMany(ListingModule::class, 'listing_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(ListingProduct::class, 'listing_id');
    }

    public function productCategories(): HasMany
    {
        return $this->hasMany(ListingProductCategory::class, 'listing_id');
    }

    public function services(): HasMany
    {
        return $this->hasMany(ListingService::class, 'listing_id');
    }

    public function leads(): HasMany
    {
        return $this->hasMany(ListingLead::class, 'listing_id');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(ListingAppointment::class, 'listing_id');
    }

    public function appointmentSlots(): HasMany
    {
        return $this->hasMany(ListingAppointmentSlot::class, 'listing_id');
    }

    public function availability(): HasMany
    {
        return $this->hasMany(ListingAvailability::class, 'listing_id');
    }

    public function availabilityExceptions(): HasMany
    {
        return $this->hasMany(ListingAvailabilityException::class, 'listing_id');
    }

    public function galleryImages(): HasMany
    {
        return $this->hasMany(\Modules\ListingGallery\Models\ListingGalleryImage::class, 'listing_id');
    }

    public function galleries(): HasMany
    {
        return $this->hasMany(\Modules\ListingGallery\Models\ListingGallery::class, 'listing_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ListingReview::class, 'listing_id');
    }

    public function promotions(): HasMany
    {
        return $this->hasMany(ListingPromotion::class, 'listing_id');
    }

    public function minisiteTheme(): BelongsTo
    {
        return $this->belongsTo(MinisiteTheme::class);
    }

    public function hero(): HasOne
    {
        return $this->hasOne(ListingHero::class, 'listing_id')->where('is_active', true)->orderBy('sort_order');
    }

    public function about(): HasOne
    {
        return $this->hasOne(ListingAbout::class, 'listing_id');
    }

    public function socialNetworks(): HasMany
    {
        return $this->hasMany(\Modules\ListingSocialMedia\Models\ListingSocialNetwork::class, 'listing_id');
    }

    public function features(): HasMany
    {
        return $this->hasMany(Feature::class, 'listing_id');
    }

    public function listingFeatures(): HasMany
    {
        return $this->hasMany(ListingFeature::class, 'listing_id');
    }

    public function faqs(): HasMany
    {
        return $this->hasMany(ListingFaq::class, 'listing_id');
    }

    public function faqCategories(): HasMany
    {
        return $this->hasMany(ListingFaqCategory::class, 'listing_id');
    }

    public function seoSetting(): HasOne
    {
        return $this->hasOne(ListingSeoSetting::class, 'listing_id');
    }

    public function contactForms(): HasMany
    {
        return $this->hasMany(ListingContactForm::class, 'listing_id');
    }

    public function contactFormFields(): HasMany
    {
        return $this->hasMany(ListingContactFormField::class, 'listing_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(\Modules\ListingTasks\Models\ListingTask::class, 'listing_id');
    }

    public function clients(): HasMany
    {
        return $this->hasMany(\Modules\ListingClients\Models\ListingClient::class, 'listing_id');
    }

    public function teamMembers(): HasMany
    {
        return $this->hasMany(\Modules\ListingTeamMembers\Models\ListingTeamMember::class, 'listing_id');
    }

    public function teamMemberPositions(): HasMany
    {
        return $this->hasMany(\Modules\ListingTeamMembers\Models\TeamMemberPosition::class, 'listing_id');
    }

    public function properties(): HasMany
    {
        return $this->hasMany(\Modules\Properties\Models\Property::class, 'listing_id');
    }

    public function packages(): HasMany
    {
        return $this->hasMany(\Modules\ListingPackages\Models\ListingPackage::class, 'listing_id');
    }

    public function getEnabledModules(): array
    {
        return $this->modules()->where('is_enabled', true)->pluck('module_key')->toArray();
    }

    public function syncAllModules(): void
    {
        $definitions = ModuleDefinition::where('is_active', true)->get();

        foreach ($definitions as $definition) {
            ListingModule::updateOrCreate(
                [
                    'listing_id' => $this->id,
                    'module_definition_id' => $definition->id,
                ],
                [
                    'module_key' => $definition->key,
                    'module_name' => $definition->name,
                    'is_enabled' => true,
                ]
            );

            $listingModule = ListingModule::where('listing_id', $this->id)
                ->where('module_definition_id', $definition->id)
                ->first();

            if ($listingModule) {
                $listingModule->update([
                    'show_in_menu' => $definition->show_in_menu,
                    'menu_title' => $definition->menu_title,
                ]);
            }
        }
    }

    public function forceDeleteWithRelations(): void
    {
        ListingLocation::where('listing_id', $this->id)->delete();
        ListingService::where('listing_id', $this->id)->delete();
        ListingAppointment::where('listing_id', $this->id)->delete();
        ListingAppointmentSlot::where('listing_id', $this->id)->delete();
        ListingAvailability::where('listing_id', $this->id)->delete();
        ListingAvailabilityException::where('listing_id', $this->id)->delete();
        ListingLead::where('listing_id', $this->id)->delete();
        ListingGalleryImage::where('listing_id', $this->id)->delete();
        ListingAbout::where('listing_id', $this->id)->delete();
        ListingHero::where('listing_id', $this->id)->delete();
        ListingFaq::where('listing_id', $this->id)->delete();
        ListingFaqCategory::where('listing_id', $this->id)->delete();
        ListingFeature::where('listing_id', $this->id)->delete();
        ListingPromotion::where('listing_id', $this->id)->delete();
        ListingReview::where('listing_id', $this->id)->delete();
        ListingSeoSetting::where('listing_id', $this->id)->delete();
        ListingContactForm::where('listing_id', $this->id)->delete();
        ListingContactFormField::where('listing_id', $this->id)->delete();
        ListingProduct::where('listing_id', $this->id)->delete();
        ListingProductCategory::where('listing_id', $this->id)->delete();
        ListingModule::where('listing_id', $this->id)->delete();
        ListingSeoSetting::where('listing_id', $this->id)->delete();

        $this->forceDelete();
    }
}
