<?php

namespace App\Jobs;

use App\Models\Automation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RunProfileIncompleteAutomationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $automations = Automation::query()
            ->where('event_key', 'user.profile_incomplete')
            ->where('is_active', true)
            ->get();

        foreach ($automations as $automation) {
            $days = (int) ($automation->config['days_since_signup'] ?? 7);
            if ($days < 0) {
                $days = 0;
            }

            $cutoff = now()->subDays($days);

            $users = User::query()
                ->where('created_at', '<=', $cutoff)
                ->whereHas('roles', fn ($query) => $query->where('name', 'member'))
                ->where(function ($query) {
                    $query->whereDoesntHave('profile')
                        ->orWhereHas('profile', function ($profileQuery) {
                            $profileQuery->whereNull('phone')
                                ->orWhere('phone', '');
                        });
                })
                ->get();

            foreach ($users as $user) {
                ExecuteAutomationActionJob::dispatch([
                    'automation_id' => $automation->id,
                    'event_key' => 'user.profile_incomplete',
                    'context' => [
                        'user_id' => $user->id,
                        'created_at' => $user->created_at?->toDateTimeString(),
                    ],
                ]);
            }
        }
    }
}
