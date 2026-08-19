<?php

namespace App\Jobs;

use App\Models\Automation;
use App\Services\AutomationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ExecuteAutomationActionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public array $payload) {}

    public function handle(AutomationService $service): void
    {
        $automationId = $this->payload['automation_id'] ?? null;
        $eventKey = (string) ($this->payload['event_key'] ?? '');
        $context = $this->payload['context'] ?? [];

        if (! $automationId || $eventKey === '') {
            return;
        }

        $automation = Automation::query()->find($automationId);
        if (! $automation || ! $automation->is_active) {
            return;
        }

        $service->runAction($automation, $eventKey, $context);
    }
}
