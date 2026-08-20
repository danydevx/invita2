<?php

namespace Modules\Listings\Models;

use App\Models\BusinessModuleDefinition;
use App\Models\MinisiteTheme;
use App\Models\PlanBusinessModule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\About\Models\BusinessAbout;
use Modules\Appointments\Models\BusinessAppointment;
use Modules\Appointments\Models\BusinessAppointmentSlot;
use Modules\Appointments\Models\BusinessAvailability;
use Modules\Appointments\Models\BusinessAvailabilityException;
use Modules\Listings\Enums\ListingType;
use Modules\BusinessModules\Models\BusinessModule;
use Modules\ContactForm\Models\BusinessContactForm;
use Modules\ContactForm\Models\BusinessContactFormField;
use Modules\Faqs\Models\BusinessFaq;
use Modules\Faqs\Models\BusinessFaqCategory;
use Modules\Features\Models\BusinessFeature;
use Modules\Features\Models\Feature;
use Modules\Gallery\Models\BusinessGalleryImage;
use Modules\Hero\Models\BusinessHero;
use Modules\Leads\Models\BusinessLead;
use Modules\Locations\Models\BusinessLocation;
use Modules\Products\Models\BusinessProduct;
use Modules\Products\Models\BusinessProductCategory;
use Modules\Promotions\Models\BusinessPromotion;
use Modules\Reviews\Models\BusinessReview;
use Modules\Seo\Models\BusinessSeoSetting;
use Modules\Services\Models\BusinessService;

class Listing extends Model
{
    protected $table = 'listings';

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'business_type',
        'industry_id',
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
        'business_type' => ListingType::class,
        'settings' => 'array',
        'is_active' => 'boolean',
        'is_published' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($listing) {
            $listing->syncModulesFromPlan();
            $listing->assignMinisiteTheme();
        });

        static::updated(function ($listing) {
            if ($listing->wasChanged('business_type')) {
                $listing->assignMinisiteTheme();
            }
        });
    }

    public function assignMinisiteTheme(): void
    {
        if (! $this->minisite_theme_id) {
            $theme = MinisiteTheme::getByListingType($this->business_type->value ?? 'generic');
            if ($theme) {
                $this->update(['minisite_theme_id' => $theme->id]);
            }
        }
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function industry(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Industry::class);
    }

    public function locations(): HasMany
    {
        return $this->hasMany(BusinessLocation::class, 'listing_id');
    }

    public function modules(): HasMany
    {
        return $this->hasMany(BusinessModule::class, 'listing_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(BusinessProduct::class, 'listing_id');
    }

    public function productCategories(): HasMany
    {
        return $this->hasMany(BusinessProductCategory::class, 'listing_id');
    }

    public function services(): HasMany
    {
        return $this->hasMany(BusinessService::class, 'listing_id');
    }

    public function leads(): HasMany
    {
        return $this->hasMany(BusinessLead::class, 'listing_id');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(BusinessAppointment::class, 'listing_id');
    }

    public function appointmentSlots(): HasMany
    {
        return $this->hasMany(BusinessAppointmentSlot::class, 'listing_id');
    }

    public function availability(): HasMany
    {
        return $this->hasMany(BusinessAvailability::class, 'listing_id');
    }

    public function availabilityExceptions(): HasMany
    {
        return $this->hasMany(BusinessAvailabilityException::class, 'listing_id');
    }

public function galleryImages(): HasMany
    {
        return $this->hasMany(\Modules\Gallery\Models\BusinessGalleryImage::class, 'listing_id');
    }

    public function galleries(): HasMany
    {
        return $this->hasMany(\Modules\Gallery\Models\BusinessGallery::class, 'listing_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(BusinessReview::class, 'listing_id');
    }

    public function promotions(): HasMany
    {
        return $this->hasMany(BusinessPromotion::class, 'listing_id');
    }

    public function minisiteTheme(): BelongsTo
    {
        return $this->belongsTo(MinisiteTheme::class);
    }

    public function hero(): HasOne
    {
        return $this->hasOne(BusinessHero::class, 'listing_id')->where('is_active', true)->orderBy('sort_order');
    }

    public function about(): HasOne
    {
        return $this->hasOne(BusinessAbout::class, 'listing_id');
    }

    public function socialNetworks(): HasMany
    {
        return $this->hasMany(\Modules\SocialMedia\Models\BusinessSocialNetwork::class, 'listing_id');
    }

    public function features(): HasMany
    {
        return $this->hasMany(Feature::class, 'listing_id');
    }

    public function businessFeatures(): HasMany
    {
        return $this->hasMany(BusinessFeature::class, 'listing_id');
    }

    public function faqs(): HasMany
    {
        return $this->hasMany(BusinessFaq::class, 'listing_id');
    }

    public function faqCategories(): HasMany
    {
        return $this->hasMany(BusinessFaqCategory::class, 'listing_id');
    }

    public function seoSetting(): HasOne
    {
        return $this->hasOne(BusinessSeoSetting::class, 'listing_id');
    }

    public function contactForms(): HasMany
    {
        return $this->hasMany(BusinessContactForm::class, 'listing_id');
    }

    public function contactFormFields(): HasMany
    {
        return $this->hasMany(BusinessContactFormField::class, 'listing_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(\Modules\Tasks\Models\BusinessTask::class, 'listing_id');
    }

    public function clients(): HasMany
    {
        return $this->hasMany(\Modules\Clients\Models\BusinessClient::class, 'listing_id');
    }

    public function teamMembers(): HasMany
    {
        return $this->hasMany(\Modules\TeamMembers\Models\BusinessTeamMember::class, 'listing_id');
    }

    public function teamMemberPositions(): HasMany
    {
        return $this->hasMany(\Modules\TeamMembers\Models\TeamMemberPosition::class, 'listing_id');
    }

    public function properties(): HasMany
    {
        return $this->hasMany(\Modules\Properties\Models\Property::class, 'listing_id');
    }

    public function packages(): HasMany
    {
        return $this->hasMany(\Modules\Packages\Models\BusinessPackage::class, 'listing_id');
    }

    public function getEnabledModules(): array
    {
        return $this->modules()->where('is_enabled', true)->pluck('module_key')->toArray();
    }

    public function syncModulesFromPlan(): void
    {
        $planModules = $this->getPlanModules();

        $definitions = BusinessModuleDefinition::where('is_active', true)->get();

        foreach ($definitions as $definition) {
            $isEnabled = $planModules[$definition->key] ?? false;

            BusinessModule::updateOrCreate(
                [
                    'listing_id' => $this->id,
                    'module_definition_id' => $definition->id,
                ],
                [
                    'module_key' => $definition->key,
                    'module_name' => $definition->name,
                    'is_enabled' => $isEnabled,
                ]
            );

            $businessModule = BusinessModule::where('listing_id', $this->id)
                ->where('module_definition_id', $definition->id)
                ->first();

            if ($businessModule) {
                $businessModule->update([
                    'show_in_menu' => $definition->show_in_menu,
                    'menu_title' => $definition->menu_title,
                ]);
            }
        }
    }

    public function forceDeleteWithRelations(): void
    {
        BusinessLocation::where('listing_id', $this->id)->delete();
        BusinessService::where('listing_id', $this->id)->delete();
        BusinessAppointment::where('listing_id', $this->id)->delete();
        BusinessAppointmentSlot::where('listing_id', $this->id)->delete();
        BusinessAvailability::where('listing_id', $this->id)->delete();
        BusinessAvailabilityException::where('listing_id', $this->id)->delete();
        BusinessLead::where('listing_id', $this->id)->delete();
        BusinessGalleryImage::where('listing_id', $this->id)->delete();
        BusinessAbout::where('listing_id', $this->id)->delete();
        BusinessHero::where('listing_id', $this->id)->delete();
        BusinessFaq::where('listing_id', $this->id)->delete();
        BusinessFaqCategory::where('listing_id', $this->id)->delete();
        BusinessFeature::where('listing_id', $this->id)->delete();
        BusinessPromotion::where('listing_id', $this->id)->delete();
        BusinessReview::where('listing_id', $this->id)->delete();
        BusinessSeoSetting::where('listing_id', $this->id)->delete();
        BusinessContactForm::where('listing_id', $this->id)->delete();
        BusinessContactFormField::where('listing_id', $this->id)->delete();
        BusinessProduct::where('listing_id', $this->id)->delete();
        BusinessProductCategory::where('listing_id', $this->id)->delete();
        BusinessModule::where('listing_id', $this->id)->delete();
        BusinessSeoSetting::where('listing_id', $this->id)->delete();

        $this->forceDelete();
    }

    protected function getPlanModules(): array
    {
        $user = $this->user;

        if (! $user) {
            return $this->getDefaultFreeModules();
        }

        $subscription = $user->subscriptions()
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->orWhere(function ($query) use ($user) {
                $query->where('status', 'active')
                    ->whereNull('ends_at')
                    ->where('user_id', $user->id);
            })
            ->latest()
            ->first();

        if (! $subscription) {
            return $this->getDefaultFreeModules();
        }

        $planModules = PlanBusinessModule::where('plan_id', $subscription->plan_id)
            ->where('is_enabled', true)
            ->whereHas('moduleDefinition', fn ($q) => $q->where('is_active', true))
            ->with('moduleDefinition')
            ->get()
            ->pluck('moduleDefinition.key')
            ->flip()
            ->map(fn () => true)
            ->toArray();

        return $planModules;
    }

    protected function getDefaultFreeModules(): array
    {
        $plan = \App\Models\Plan::where('slug', 'free')->first();

        if (! $plan) {
            return [];
        }

        return PlanBusinessModule::where('plan_id', $plan->id)
            ->where('is_enabled', true)
            ->whereHas('moduleDefinition', fn ($q) => $q->where('is_active', true))
            ->with('moduleDefinition')
            ->get()
            ->pluck('moduleDefinition.key')
            ->flip()
            ->map(fn () => true)
            ->toArray();
    }
}
