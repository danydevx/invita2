<?php

namespace Modules\ListingAiChatbot\Observers;

use Modules\ListingAiChatbot\Events\BusinessContentChanged;

class AboutObserver
{
    public function created($model): void
    {
        event(new BusinessContentChanged($model->listing_id, 'about', $model->id, 'created'));
    }

    public function updated($model): void
    {
        event(new BusinessContentChanged($model->listing_id, 'about', $model->id, 'updated'));
    }

    public function deleted($model): void
    {
        event(new BusinessContentChanged($model->listing_id, 'about', $model->id, 'deleted'));
    }
}
