<?php

namespace Modules\ListingAiChatbot\Observers;

use Modules\ListingAiChatbot\Events\BusinessContentChanged;

class RestaurantProductObserver
{
    public function created($model): void
    {
        if (!$model->active) return;
        event(new BusinessContentChanged($model->listing_id, 'restaurant_product', $model->id, 'created'));
    }

    public function updated($model): void
    {
        event(new BusinessContentChanged($model->listing_id, 'restaurant_product', $model->id, 'updated'));
    }

    public function deleted($model): void
    {
        event(new BusinessContentChanged($model->listing_id, 'restaurant_product', $model->id, 'deleted'));
    }
}
