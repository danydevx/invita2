<?php

namespace App\Jobs;

use App\Models\SystemError;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CleanupOldSystemErrorsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $cutoff = now()->subDays(60);

        SystemError::query()
            ->where('is_resolved', true)
            ->where('resolved_at', '<', $cutoff)
            ->delete();
    }
}
