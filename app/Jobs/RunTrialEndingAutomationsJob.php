<?php

namespace App\Jobs;

use App\Models\Automation;
use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RunTrialEndingAutomationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $automations = Automation::query()
            ->where('event_key', 'billing.trial_ending')
            ->where('is_active', true)
            ->get();

        foreach ($automations as $automation) {
            $daysBefore = (int) ($automation->config['days_before'] ?? 3);
            if ($daysBefore < 0) {
                $daysBefore = 0;
            }

            $from = now();
            $to = now()->addDays($daysBefore);

            $subscriptions = Subscription::query()
                ->with('plan')
                ->whereNotNull('trial_ends_at')
                ->whereBetween('trial_ends_at', [$from, $to])
                ->whereIn('status', ['trial', 'active'])
                ->get();

            foreach ($subscriptions as $subscription) {
                ExecuteAutomationActionJob::dispatch([
                    'automation_id' => $automation->id,
                    'event_key' => 'billing.trial_ending',
                    'context' => [
                        'user_id' => $subscription->user_id,
                        'subscription_id' => $subscription->id,
                        'plan_name' => $subscription->plan?->name,
                        'trial_ends_at' => $subscription->trial_ends_at?->toDateTimeString(),
                    ],
                ]);
            }
        }
    }
}
