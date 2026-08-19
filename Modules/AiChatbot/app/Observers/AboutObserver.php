<?php

namespace Modules\AiChatbot\Observers;

use Modules\AiChatbot\Events\BusinessContentChanged;

class AboutObserver
{
    public function created($model): void
    {
        event(new BusinessContentChanged($model->business_id, 'about', $model->id, 'created'));
    }

    public function updated($model): void
    {
        event(new BusinessContentChanged($model->business_id, 'about', $model->id, 'updated'));
    }

    public function deleted($model): void
    {
        event(new BusinessContentChanged($model->business_id, 'about', $model->id, 'deleted'));
    }
}
