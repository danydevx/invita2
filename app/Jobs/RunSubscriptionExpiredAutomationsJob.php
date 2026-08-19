<?php

namespace App\Jobs;

use App\Models\Automation;
use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RunSubscriptionExpiredAutomationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $automations = Automation::query()
            ->where('event_key', 'subscription.expired')
            ->where('is_active', true)
            ->get();

        foreach ($automations as $automation) {
            $cutoff = now();

            $subscriptions = Subscription::query()
                ->with('plan')
                ->whereNotNull('ends_at')
                ->where('ends_at', '<=', $cutoff)
                ->whereNotIn('status', ['expired', 'canceled'])
                ->get();

            foreach ($subscriptions as $subscription) {
                ExecuteAutomationActionJob::dispatch([
                    'automation_id' => $automation->id,
                    'event_key' => 'subscription.expired',
                    'context' => [
                        'user_id' => $subscription->user_id,
                        'subscription_id' => $subscription->id,
                        'plan_name' => $subscription->plan?->name,
                        'ends_at' => $subscription->ends_at?->toDateTimeString(),
                    ],
                ]);
            }
        }
    }
}
