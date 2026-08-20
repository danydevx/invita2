<?php

namespace Modules\ListingAiChatbot\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Event;
use Modules\ListingAiChatbot\Events\BusinessContentChanged;
use Modules\ListingAiChatbot\Listeners\ReindexOnContentChange;
use Modules\ListingAiChatbot\Observers\ProductObserver;
use Modules\ListingAiChatbot\Observers\ServiceObserver;
use Modules\ListingAiChatbot\Observers\PromotionObserver;
use Modules\ListingAiChatbot\Observers\FaqObserver;
use Modules\ListingAiChatbot\Observers\LocationObserver;
use Modules\ListingAiChatbot\Observers\AboutObserver;
use Modules\ListingAiChatbot\Observers\SocialNetworkObserver;
use Modules\ListingAiChatbot\Observers\RestaurantCategoryObserver;
use Modules\ListingAiChatbot\Observers\RestaurantProductObserver;
use Modules\ListingAiChatbot\Observers\AiContextObserver;
use Modules\ListingAiChatbot\Observers\AvailabilityObserver;

class ListingAiChatbotServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'ListingAiChatbot';

    protected string $nameLower = 'listingaichatbot';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    public function boot(): void
    {
        parent::boot();

        $this->registerObservers();
        $this->registerEvents();
    }

    protected function registerObservers(): void
    {
        if (class_exists('\Modules\ListingProducts\Models\ListingProduct')) {
            \Modules\ListingProducts\Models\ListingProduct::observe(ProductObserver::class);
        }

        if (class_exists('\Modules\ListingServices\Models\ListingService')) {
            \Modules\ListingServices\Models\ListingService::observe(ServiceObserver::class);
        }

        if (class_exists('\Modules\ListingPromotions\Models\ListingPromotion')) {
            \Modules\ListingPromotions\Models\ListingPromotion::observe(PromotionObserver::class);
        }

        if (class_exists('\Modules\ListingFaqs\Models\ListingFaq')) {
            \Modules\ListingFaqs\Models\ListingFaq::observe(FaqObserver::class);
        }

        if (class_exists('\Modules\ListingLocations\Models\ListingLocation')) {
            \Modules\ListingLocations\Models\ListingLocation::observe(LocationObserver::class);
        }

        if (class_exists('\Modules\ListingAbout\Models\ListingAbout')) {
            \Modules\ListingAbout\Models\ListingAbout::observe(AboutObserver::class);
        }

        if (class_exists('\Modules\ListingSocialMedia\Models\ListingSocialNetwork')) {
            \Modules\ListingSocialMedia\Models\ListingSocialNetwork::observe(SocialNetworkObserver::class);
        }

        if (class_exists('\Modules\ListingRestaurantMenu\Entities\MenuCategory')) {
            \Modules\ListingRestaurantMenu\Entities\MenuCategory::observe(RestaurantCategoryObserver::class);
        }

        if (class_exists('\Modules\ListingRestaurantMenu\Entities\MenuProduct')) {
            \Modules\ListingRestaurantMenu\Entities\MenuProduct::observe(RestaurantProductObserver::class);
        }

        \Modules\ListingAiChatbot\Models\AiContext::observe(AiContextObserver::class);

        if (class_exists('\Modules\ListingAppointments\Models\ListingAvailability')) {
            \Modules\ListingAppointments\Models\ListingAvailability::observe(AvailabilityObserver::class);
        }
    }

    protected function registerEvents(): void
    {
        Event::listen(
            BusinessContentChanged::class,
            ReindexOnContentChange::class
        );
    }

    public function register(): void
    {
        parent::register();
    }
}
