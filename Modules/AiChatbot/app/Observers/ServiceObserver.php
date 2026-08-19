<?php

namespace Modules\AiChatbot\Observers;

use Modules\AiChatbot\Events\BusinessContentChanged;

class ServiceObserver
{
    public function created($model): void
    {
        if (!$model->is_active) return;
        event(new BusinessContentChanged($model->business_id, 'service', $model->id, 'created'));
    }

    public function updated($model): void
    {
        event(new BusinessContentChanged($model->business_id, 'service', $model->id, 'updated'));
    }

    public function deleted($model): void
    {
        event(new BusinessContentChanged($model->business_id, 'service', $model->id, 'deleted'));
    }
}
