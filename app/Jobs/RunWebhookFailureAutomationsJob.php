<?php

namespace App\Jobs;

use App\Models\Automation;
use App\Models\WebhookDelivery;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RunWebhookFailureAutomationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $automations = Automation::query()
            ->where('event_key', 'webhook.failed_many_times')
            ->where('is_active', true)
            ->get();

        foreach ($automations as $automation) {
            $threshold = (int) ($automation->config['threshold'] ?? 3);
            $windowHours = (int) ($automation->config['window_hours'] ?? 24);

            if ($threshold < 1) {
                $threshold = 1;
            }

            if ($windowHours < 1) {
                $windowHours = 1;
            }

            $cutoff = now()->subHours($windowHours);

            $deliveries = WebhookDelivery::query()
                ->with('endpoint')
                ->whereNotNull('failed_at')
                ->where('failed_at', '>=', $cutoff)
                ->where('attempt_count', '>=', $threshold)
                ->get();

            foreach ($deliveries as $delivery) {
                $userId = $delivery->endpoint?->user_id;
                if (! $userId) {
                    continue;
                }

                ExecuteAutomationActionJob::dispatch([
                    'automation_id' => $automation->id,
                    'event_key' => 'webhook.failed_many_times',
                    'context' => [
                        'user_id' => $userId,
                        'webhook_delivery_id' => $delivery->id,
                        'webhook_endpoint_id' => $delivery->webhook_endpoint_id,
                        'webhook_event' => $delivery->event,
                        'attempt_count' => $delivery->attempt_count,
                    ],
                ]);
            }
        }
    }
}
