<?php

namespace Modules\ListingAiChatbot\Observers;

use Modules\ListingAiChatbot\Events\BusinessContentChanged;

class LocationObserver
{
    public function created($model): void
    {
        if (!$model->is_active) return;
        event(new BusinessContentChanged($model->listing_id, 'location', $model->id, 'created'));
    }

    public function updated($model): void
    {
        event(new BusinessContentChanged($model->listing_id, 'location', $model->id, 'updated'));
    }

    public function deleted($model): void
    {
        event(new BusinessContentChanged($model->listing_id, 'location', $model->id, 'deleted'));
    }
}
