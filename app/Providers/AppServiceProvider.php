<?php

namespace App\Providers;

use App\Console\Commands\CreateSuperAdmin;
use App\Console\Commands\InstallSaas;
use App\Models\ApiKey;
use App\Models\MediaFile;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\SupportTicket;
use App\Models\User;
use App\Models\WebhookEndpoint;
use App\Policies\ApiKeyPolicy;
use App\Policies\ListingAboutPolicy;
use App\Policies\ListingAppointmentPolicy;
use App\Policies\ListingAppointmentSlotPolicy;
use App\Policies\ListingAvailabilityPolicy;
use App\Policies\ListingBrandingSettingPolicy;
use App\Policies\ListingClientPolicy;
use App\Policies\ListingFaqCategoryPolicy;
use App\Policies\ListingFaqPolicy;
use App\Policies\ListingFeaturePolicy;
use App\Policies\ListingGalleryImagePolicy;
use App\Policies\ListingGalleryPolicy;
use App\Policies\ListingHeroPolicy;
use App\Policies\ListingLeadPolicy;
use App\Policies\ListingLocationPolicy;
use App\Policies\ListingModulePolicy;
use App\Policies\BusinessPolicy;
use App\Policies\ListingProductCategoryPolicy;
use App\Policies\ListingProductPolicy;
use App\Policies\ListingPromotionPolicy;
use App\Policies\ListingReviewPolicy;
use App\Policies\ListingSeoSettingPolicy;
use App\Policies\ListingServicePolicy;
use App\Policies\ListingSocialNetworkPolicy;
use App\Policies\ListingTeamMemberPolicy;
use App\Policies\ListingPackagePolicy;
use App\Policies\MediaFilePolicy;
use App\Policies\VCardPolicy;
use App\Policies\VCardTeamPolicy;
use App\Policies\PaymentPolicy;
use App\Policies\SubscriptionPolicy;
use App\Policies\SupportTicketPolicy;
use App\Policies\UserPolicy;
use App\Policies\WebhookEndpointPolicy;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Modules\Listings\Models\Listing;
use Modules\ListingMinisite\Models\ListingMinisiteSection;
use Modules\ListingMinisite\Models\ListingMinisiteSetting;
use Modules\ListingMinisite\Policies\ListingMinisitePolicy;
use Modules\ListingOfficeHours\Models\ListingSchedule;
use Modules\ListingOfficeHours\Policies\ListingSchedulePolicy;
use Modules\Properties\Models\PropertyType;
use Modules\Properties\Observers\PropertyTypeObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->router->bind('listing', function ($value) {
            return Listing::findOrFail($value);
        });

        $this->app->router->bind('listingSlug', function ($value) {
            return Listing::where('slug', $value)->firstOrFail();
        });

        $this->app->router->bind('location', function ($value) {
            return \Modules\ListingLocations\Models\ListingLocation::findOrFail($value);
        });

        $this->app->router->bind('schedule', function ($value) {
            return \Modules\ListingOfficeHours\Models\ListingSchedule::findOrFail($value);
        });

        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateSuperAdmin::class,
                InstallSaas::class,
                \Modules\Properties\Console\Commands\AssignLockedSections::class,
            ]);
        }

        Gate::before(function ($user) {
            return (int) $user->id === 1 ? true : null;
        });

        RedirectIfAuthenticated::redirectUsing(function (Request $request) {
            $user = Auth::user();

            if ($user && $user->hasAnyRole(['admin', 'superadmin'])) {
                return '/admin/dashboard';
            }

            if ($user && $user->hasRole('member')) {
                return '/member';
            }

            return '/';
        });

        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(ApiKey::class, ApiKeyPolicy::class);
        Gate::policy(WebhookEndpoint::class, WebhookEndpointPolicy::class);
        Gate::policy(SupportTicket::class, SupportTicketPolicy::class);
        Gate::policy(MediaFile::class, MediaFilePolicy::class);
        Gate::policy(Payment::class, PaymentPolicy::class);
        Gate::policy(Subscription::class, SubscriptionPolicy::class);
        Gate::policy(Listing::class, BusinessPolicy::class);
        Gate::policy(\Modules\ListingModules\Models\ListingModule::class, ListingModulePolicy::class);
        Gate::policy(\Modules\ListingLocations\Models\ListingLocation::class, ListingLocationPolicy::class);
        Gate::policy(\Modules\ListingGallery\Models\ListingGalleryImage::class, ListingGalleryImagePolicy::class);
        Gate::policy(\Modules\ListingHero\Models\ListingHero::class, ListingHeroPolicy::class);
        Gate::policy(\Modules\ListingAbout\Models\ListingAbout::class, ListingAboutPolicy::class);
        Gate::policy(\Modules\ListingProducts\Models\ListingProduct::class, ListingProductPolicy::class);
        Gate::policy(\Modules\ListingProducts\Models\ListingProductCategory::class, ListingProductCategoryPolicy::class);
        Gate::policy(\Modules\ListingServices\Models\ListingService::class, ListingServicePolicy::class);
        Gate::policy(\Modules\ListingPromotions\Models\ListingPromotion::class, ListingPromotionPolicy::class);
        Gate::policy(\Modules\ListingLeads\Models\ListingLead::class, ListingLeadPolicy::class);
        Gate::policy(\Modules\ListingAppointments\Models\ListingAppointment::class, ListingAppointmentPolicy::class);
        Gate::policy(\Modules\ListingClients\Models\ListingClient::class, ListingClientPolicy::class);
        Gate::policy(\Modules\ListingGallery\Models\ListingGallery::class, ListingGalleryPolicy::class);
        Gate::policy(\Modules\ListingAppointments\Models\ListingAppointmentSlot::class, ListingAppointmentSlotPolicy::class);
        Gate::policy(\Modules\ListingAppointments\Models\ListingAvailability::class, ListingAvailabilityPolicy::class);
        Gate::policy(\Modules\ListingSocialMedia\Models\ListingSocialNetwork::class, ListingSocialNetworkPolicy::class);
        Gate::policy(\Modules\ListingReviews\Models\ListingReview::class, ListingReviewPolicy::class);
        Gate::policy(\Modules\ListingFaqs\Models\ListingFaq::class, ListingFaqPolicy::class);
        Gate::policy(\Modules\ListingFaqs\Models\ListingFaqCategory::class, ListingFaqCategoryPolicy::class);
        Gate::policy(\Modules\ListingFeatures\Models\ListingFeature::class, ListingFeaturePolicy::class);
        Gate::policy(\Modules\ListingSeo\Models\ListingSeoSetting::class, ListingSeoSettingPolicy::class);
        Gate::policy(\Modules\ListingBranding\Models\ListingBrandingSetting::class, ListingBrandingSettingPolicy::class);
        Gate::policy(\Modules\ListingTasks\Models\ListingTask::class, \App\Policies\ListingTaskPolicy::class);
        Gate::policy(ListingMinisiteSetting::class, ListingMinisitePolicy::class);
        Gate::policy(ListingMinisiteSection::class, ListingMinisitePolicy::class);
        Gate::policy(Listing::class, ListingMinisitePolicy::class);
        Gate::policy(\Modules\Properties\Models\Property::class, \App\Policies\PropertyPolicy::class);
        Gate::policy(\Modules\Properties\Models\PropertyType::class, \App\Policies\PropertyTypePolicy::class);
        Gate::policy(ListingSchedule::class, ListingSchedulePolicy::class);
        Gate::policy(\Modules\ListingTeamMembers\Models\ListingTeamMember::class, ListingTeamMemberPolicy::class);
        Gate::policy(\Modules\ListingTeamMembers\Models\TeamMemberPosition::class, ListingTeamMemberPolicy::class);
        Gate::policy(\Modules\ListingPackages\Models\ListingPackage::class, ListingPackagePolicy::class);
        Gate::policy(\Modules\VCards\Models\VCard::class, VCardPolicy::class);
        Gate::policy(\Modules\VCards\Models\VCardTeam::class, VCardTeamPolicy::class);
        Gate::policy(\Modules\VCards\Models\VCardSeoSetting::class, VCardSeoSettingPolicy::class);
        Gate::policy(\Modules\VCards\Models\VCardPackage::class, VCardPackagePolicy::class);

        PropertyType::observe(PropertyTypeObserver::class);

        RateLimiter::for('login', function (Request $request) {
            $email = mb_strtolower((string) $request->input('email', ''));
            $key = $email !== '' ? $email.'|'.$request->ip() : $request->ip();

            return Limit::perMinute(5)->by($key)->response(function () use ($request) {
                return $this->throttleResponse($request, 'Demasiados intentos. Intente de nuevo en unos minutos.', 'email', false, 'login');
            });
        });

        RateLimiter::for('register', function (Request $request) {
            return Limit::perMinutes(10, 5)->by($request->ip())->response(function () use ($request) {
                return $this->throttleResponse($request, 'Demasiados intentos. Intente de nuevo en unos minutos.', 'email');
            });
        });

        RateLimiter::for('password-email', function (Request $request) {
            $email = mb_strtolower((string) $request->input('email', ''));
            $key = $email !== '' ? $email.'|'.$request->ip() : $request->ip();

            return Limit::perMinutes(15, 3)->by($key)->response(function () use ($request) {
                return $this->throttleResponse($request, 'Demasiados intentos. Intente de nuevo en unos minutos.', 'email');
            });
        });

        RateLimiter::for('password-verify', function (Request $request) {
            $token = (string) $request->route('token');
            $key = ($token !== '' ? $token.'|' : '').$request->ip();

            return Limit::perMinutes(10, 5)->by($key)->response(function () use ($request) {
                return $this->throttleResponse($request, 'Demasiados intentos. Intente de nuevo en unos minutos.', 'code');
            });
        });

        RateLimiter::for('password-reset', function (Request $request) {
            $token = (string) $request->route('token');
            $key = ($token !== '' ? $token.'|' : '').$request->ip();

            return Limit::perMinutes(10, 5)->by($key)->response(function () use ($request) {
                return $this->throttleResponse($request, 'Demasiados intentos. Intente de nuevo en unos minutos.', 'password');
            });
        });

        RateLimiter::for('verification-resend', function (Request $request) {
            $userId = $request->user()?->id;
            $key = $userId ? 'user|'.$userId : $request->ip();

            return Limit::perMinutes(15, 3)->by($key)->response(function () use ($request) {
                return $this->throttleResponse($request, 'Demasiados intentos. Intente de nuevo en unos minutos.', null, true);
            });
        });

        RateLimiter::for('email-verify', function (Request $request) {
            $userId = (string) $request->route('id');
            $key = ($userId !== '' ? $userId.'|' : '').$request->ip();

            return Limit::perMinutes(10, 6)->by($key)->response(function () use ($request) {
                return $this->throttleResponse($request, 'Demasiados intentos. Intente de nuevo en unos minutos.', null, true);
            });
        });

        RateLimiter::for('ticket-create', function (Request $request) {
            $userId = $request->user()?->id;
            $key = $userId ? 'user|'.$userId : $request->ip();

            return Limit::perHour(5)->by($key)->response(function () use ($request) {
                return $this->throttleResponse($request, 'Has excedido el numero de solicitudes permitidas.', 'message');
            });
        });

        RateLimiter::for('ticket-reply', function (Request $request) {
            $userId = $request->user()?->id;
            $key = $userId ? 'user|'.$userId : $request->ip();

            return Limit::perHour(10)->by($key)->response(function () use ($request) {
                return $this->throttleResponse($request, 'Has excedido el numero de solicitudes permitidas.', 'message');
            });
        });

        RateLimiter::for('billing-portal', function (Request $request) {
            $userId = $request->user()?->id;
            $key = $userId ? 'user|'.$userId : $request->ip();

            return Limit::perMinutes(10, 5)->by($key)->response(function () use ($request) {
                return $this->throttleResponse($request, 'Demasiadas solicitudes. Intente de nuevo en unos minutos.', null, true);
            });
        });

        RateLimiter::for('checkout-create', function (Request $request) {
            $userId = $request->user()?->id;
            $key = $userId ? 'user|'.$userId : $request->ip();

            return Limit::perMinutes(10, 5)->by($key)->response(function () use ($request) {
                return $this->throttleResponse($request, 'Demasiadas solicitudes. Intente de nuevo en unos minutos.', null, true);
            });
        });

        RateLimiter::for('checkout-coupon', function (Request $request) {
            $userId = $request->user()?->id;
            $key = $userId ? 'user|'.$userId : $request->ip();

            return Limit::perMinute(10)->by($key)->response(function () use ($request) {
                return $this->throttleResponse($request, 'Has excedido el numero de solicitudes permitidas.', null, true);
            });
        });

        RateLimiter::for('api-key', function (Request $request) {
            $keyId = $request->attributes->get('api_key_id');
            $identifier = $keyId ? 'key|'.$keyId : 'ip|'.$request->ip();

            return Limit::perMinute(60)->by($identifier)->response(function () {
                return response()->json([
                    'message' => 'Demasiadas solicitudes. Intente de nuevo en unos minutos.',
                ], 429);
            });
        });

        RateLimiter::for('api-keys-create', function (Request $request) {
            $userId = $request->user()?->id;
            $key = $userId ? 'user|'.$userId : $request->ip();

            return Limit::perMinutes(10, 5)->by($key)->response(function () use ($request) {
                return $this->throttleResponse(
                    $request,
                    'Has excedido el numero de solicitudes permitidas.',
                    'name'
                );
            });
        });
    }

    private function throttleResponse(Request $request, string $message, ?string $field = null, bool $useFlash = false, ?string $action = null)
    {
        if ($action === 'login') {
            app(\App\Services\SecurityService::class)->log('rate_limit_triggered', null, $request, 'Rate limit en login', [
                'action' => 'login',
                'email' => strtolower((string) $request->input('email', '')),
            ]);
        }

        if ($useFlash) {
            return back()->with('error', $message)->setStatusCode(429);
        }

        $payload = $field ? [$field => $message] : ['rate_limit' => $message];

        return back()
            ->withErrors($payload)
            ->withInput($request->except(['password', 'password_confirmation']))
            ->setStatusCode(429);
    }
}
