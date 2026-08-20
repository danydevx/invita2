<?php

namespace Modules\ListingAiChatbot\Observers;

use Modules\ListingAiChatbot\Events\BusinessContentChanged;

class AvailabilityObserver
{
    public function created($model): void
    {
        if (!$model->is_active) return;
        $this->reindexAppointments($model->listing_id);
    }

    public function updated($model): void
    {
        $this->reindexAppointments($model->listing_id);
    }

    public function deleted($model): void
    {
        $this->reindexAppointments($model->listing_id);
    }

    private function reindexAppointments(int $businessId): void
    {
        event(new BusinessContentChanged($businessId, 'appointment', $businessId, 'updated'));
    }
}
