<?php

namespace Modules\AiChatbot\Observers;

use Modules\AiChatbot\Events\BusinessContentChanged;

class SocialNetworkObserver
{
    public function created($model): void
    {
        if (!$model->is_active) return;
        event(new BusinessContentChanged($model->listing_id, 'social_network', $model->id, 'created'));
    }

    public function updated($model): void
    {
        event(new BusinessContentChanged($model->listing_id, 'social_network', $model->id, 'updated'));
    }

    public function deleted($model): void
    {
        event(new BusinessContentChanged($model->listing_id, 'social_network', $model->id, 'deleted'));
    }
}
