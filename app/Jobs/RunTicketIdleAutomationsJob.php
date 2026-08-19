<?php

namespace App\Jobs;

use App\Models\Automation;
use App\Models\SupportTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RunTicketIdleAutomationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $automations = Automation::query()
            ->where('event_key', 'support.ticket_idle')
            ->where('is_active', true)
            ->get();

        foreach ($automations as $automation) {
            $idleHours = (int) ($automation->config['idle_hours'] ?? 48);
            if ($idleHours < 1) {
                $idleHours = 1;
            }

            $cutoff = now()->subHours($idleHours);

            $tickets = SupportTicket::query()
                ->where('status', '!=', 'closed')
                ->whereNotNull('last_reply_at')
                ->where('last_reply_at', '<=', $cutoff)
                ->get();

            foreach ($tickets as $ticket) {
                ExecuteAutomationActionJob::dispatch([
                    'automation_id' => $automation->id,
                    'event_key' => 'support.ticket_idle',
                    'context' => [
                        'user_id' => $ticket->user_id,
                        'ticket_id' => $ticket->id,
                        'ticket_subject' => $ticket->subject,
                        'last_reply_at' => $ticket->last_reply_at?->toDateTimeString(),
                    ],
                ]);
            }
        }
    }
}
