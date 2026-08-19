<?php

namespace Modules\Businesses\Models;

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
use Modules\Businesses\Enums\BusinessType;
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

class Business extends Model
{
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
        'business_type' => BusinessType::class,
        'settings' => 'array',
        'is_active' => 'boolean',
        'is_published' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($business) {
            $business->syncModulesFromPlan();
            $business->assignMinisiteTheme();
        });

        static::updated(function ($business) {
            if ($business->wasChanged('business_type')) {
                $business->assignMinisiteTheme();
            }
        });
    }

    public function assignMinisiteTheme(): void
    {
        if (! $this->minisite_theme_id) {
            $theme = MinisiteTheme::getByBusinessType($this->business_type->value ?? 'generic');
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
        return $this->hasMany(BusinessLocation::class);
    }

    public function modules(): HasMany
    {
        return $this->hasMany(BusinessModule::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(BusinessProduct::class);
    }

    public function productCategories(): HasMany
    {
        return $this->hasMany(BusinessProductCategory::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(BusinessService::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(BusinessLead::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(BusinessAppointment::class);
    }

    public function appointmentSlots(): HasMany
    {
        return $this->hasMany(BusinessAppointmentSlot::class);
    }

    public function availability(): HasMany
    {
        return $this->hasMany(BusinessAvailability::class);
    }

    public function availabilityExceptions(): HasMany
    {
        return $this->hasMany(BusinessAvailabilityException::class);
    }

public function galleryImages(): HasMany
    {
        return $this->hasMany(\Modules\Gallery\Models\BusinessGalleryImage::class);
    }

    public function galleries(): HasMany
    {
        return $this->hasMany(\Modules\Gallery\Models\BusinessGallery::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(BusinessReview::class);
    }

    public function promotions(): HasMany
    {
        return $this->hasMany(BusinessPromotion::class);
    }

    public function minisiteTheme(): BelongsTo
    {
        return $this->belongsTo(MinisiteTheme::class);
    }

    public function hero(): HasOne
    {
        return $this->hasOne(BusinessHero::class)->where('is_active', true)->orderBy('sort_order');
    }

    public function about(): HasOne
    {
        return $this->hasOne(BusinessAbout::class);
    }

    public function socialNetworks(): HasMany
    {
        return $this->hasMany(\Modules\SocialMedia\Models\BusinessSocialNetwork::class);
    }

    public function features(): HasMany
    {
        return $this->hasMany(Feature::class);
    }

    public function businessFeatures(): HasMany
    {
        return $this->hasMany(BusinessFeature::class);
    }

    public function faqs(): HasMany
    {
        return $this->hasMany(BusinessFaq::class);
    }

    public function faqCategories(): HasMany
    {
        return $this->hasMany(BusinessFaqCategory::class);
    }

    public function seoSetting(): HasOne
    {
        return $this->hasOne(BusinessSeoSetting::class);
    }

    public function contactForms(): HasMany
    {
        return $this->hasMany(BusinessContactForm::class);
    }

    public function contactFormFields(): HasMany
    {
        return $this->hasMany(BusinessContactFormField::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(\Modules\Tasks\Models\BusinessTask::class);
    }

    public function clients(): HasMany
    {
        return $this->hasMany(\Modules\Clients\Models\BusinessClient::class);
    }

    public function teamMembers(): HasMany
    {
        return $this->hasMany(\Modules\TeamMembers\Models\BusinessTeamMember::class);
    }

    public function teamMemberPositions(): HasMany
    {
        return $this->hasMany(\Modules\TeamMembers\Models\TeamMemberPosition::class);
    }

    public function properties(): HasMany
    {
        return $this->hasMany(\Modules\Properties\Models\Property::class);
    }

    public function packages(): HasMany
    {
        return $this->hasMany(\Modules\Packages\Models\BusinessPackage::class);
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
                    'business_id' => $this->id,
                    'module_definition_id' => $definition->id,
                ],
                [
                    'module_key' => $definition->key,
                    'module_name' => $definition->name,
                    'is_enabled' => $isEnabled,
                ]
            );

            $businessModule = BusinessModule::where('business_id', $this->id)
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
        BusinessLocation::where('business_id', $this->id)->delete();
        BusinessService::where('business_id', $this->id)->delete();
        BusinessAppointment::where('business_id', $this->id)->delete();
        BusinessAppointmentSlot::where('business_id', $this->id)->delete();
        BusinessAvailability::where('business_id', $this->id)->delete();
        BusinessAvailabilityException::where('business_id', $this->id)->delete();
        BusinessLead::where('business_id', $this->id)->delete();
        BusinessGalleryImage::where('business_id', $this->id)->delete();
        BusinessAbout::where('business_id', $this->id)->delete();
        BusinessHero::where('business_id', $this->id)->delete();
        BusinessFaq::where('business_id', $this->id)->delete();
        BusinessFaqCategory::where('business_id', $this->id)->delete();
        BusinessFeature::where('business_id', $this->id)->delete();
        BusinessPromotion::where('business_id', $this->id)->delete();
        BusinessReview::where('business_id', $this->id)->delete();
        BusinessSeoSetting::where('business_id', $this->id)->delete();
        BusinessContactForm::where('business_id', $this->id)->delete();
        BusinessContactFormField::where('business_id', $this->id)->delete();
        BusinessProduct::where('business_id', $this->id)->delete();
        BusinessProductCategory::where('business_id', $this->id)->delete();
        BusinessModule::where('business_id', $this->id)->delete();
        BusinessSeoSetting::where('business_id', $this->id)->delete();

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
