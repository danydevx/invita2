<?php

namespace Modules\AiChatbot\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Event;
use Modules\AiChatbot\Events\BusinessContentChanged;
use Modules\AiChatbot\Listeners\ReindexOnContentChange;
use Modules\AiChatbot\Observers\ProductObserver;
use Modules\AiChatbot\Observers\ServiceObserver;
use Modules\AiChatbot\Observers\PromotionObserver;
use Modules\AiChatbot\Observers\FaqObserver;
use Modules\AiChatbot\Observers\LocationObserver;
use Modules\AiChatbot\Observers\AboutObserver;
use Modules\AiChatbot\Observers\SocialNetworkObserver;
use Modules\AiChatbot\Observers\RestaurantCategoryObserver;
use Modules\AiChatbot\Observers\RestaurantProductObserver;
use Modules\AiChatbot\Observers\AiContextObserver;
use Modules\AiChatbot\Observers\AvailabilityObserver;

class AiChatbotServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'AiChatbot';

    protected string $nameLower = 'aichatbot';

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
        if (class_exists('\Modules\Products\Models\BusinessProduct')) {
            \Modules\Products\Models\BusinessProduct::observe(ProductObserver::class);
        }

        if (class_exists('\Modules\Services\Models\BusinessService')) {
            \Modules\Services\Models\BusinessService::observe(ServiceObserver::class);
        }

        if (class_exists('\Modules\Promotions\Models\BusinessPromotion')) {
            \Modules\Promotions\Models\BusinessPromotion::observe(PromotionObserver::class);
        }

        if (class_exists('\Modules\Faqs\Models\BusinessFaq')) {
            \Modules\Faqs\Models\BusinessFaq::observe(FaqObserver::class);
        }

        if (class_exists('\Modules\Locations\Models\BusinessLocation')) {
            \Modules\Locations\Models\BusinessLocation::observe(LocationObserver::class);
        }

        if (class_exists('\Modules\About\Models\BusinessAbout')) {
            \Modules\About\Models\BusinessAbout::observe(AboutObserver::class);
        }

        if (class_exists('\Modules\SocialMedia\Models\BusinessSocialNetwork')) {
            \Modules\SocialMedia\Models\BusinessSocialNetwork::observe(SocialNetworkObserver::class);
        }

        if (class_exists('\Modules\RestaurantMenu\Entities\MenuCategory')) {
            \Modules\RestaurantMenu\Entities\MenuCategory::observe(RestaurantCategoryObserver::class);
        }

        if (class_exists('\Modules\RestaurantMenu\Entities\MenuProduct')) {
            \Modules\RestaurantMenu\Entities\MenuProduct::observe(RestaurantProductObserver::class);
        }

        \Modules\AiChatbot\Models\AiContext::observe(AiContextObserver::class);

        if (class_exists('\Modules\Appointments\Models\BusinessAvailability')) {
            \Modules\Appointments\Models\BusinessAvailability::observe(AvailabilityObserver::class);
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
