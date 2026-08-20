<?php

namespace Modules\ListingAiChatbot\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BusinessContentChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $businessId,
        public string $sourceType,
        public ?int $sourceId,
        public string $action
    ) {}
}
