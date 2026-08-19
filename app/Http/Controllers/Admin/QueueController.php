<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class QueueController extends Controller
{
    public function index()
    {
        $jobsCount = (int) DB::table('jobs')->count();
        $failedCount = (int) DB::table('failed_jobs')->count();
        $lastFailed = DB::table('failed_jobs')->orderByDesc('failed_at')->value('failed_at');

        return Inertia::render('Admin/Queues/Index', [
            'stats' => [
                'pending' => $jobsCount,
                'failed' => $failedCount,
                'last_failed_at' => $lastFailed,
            ],
        ]);
    }

    public function failed()
    {
        $items = DB::table('failed_jobs')
            ->orderByDesc('failed_at')
            ->paginate(15)
            ->withQueryString()
            ->through(function ($row) {
                $payload = json_decode($row->payload ?? '{}', true);
                $display = $payload['displayName'] ?? $payload['data']['commandName'] ?? 'Job';
                $exception = $this->firstLine($row->exception ?? '');

                return [
                    'id' => $row->id,
                    'queue' => $row->queue,
                    'job' => $display,
                    'failed_at' => $row->failed_at,
                    'exception' => $exception,
                ];
            });

        return Inertia::render('Admin/Queues/Failed', [
            'failedJobs' => $items,
        ]);
    }

    public function retry(Request $request, string $id, ActivityService $activity)
    {
        Artisan::call('queue:retry', ['id' => $id]);

        $activity->log('queue_failed_job_retry', [
            'user' => $request->user(),
            'actor' => $request->user(),
            'description' => 'Reintento de job fallido',
            'metadata' => [
                'failed_job_id' => $id,
            ],
            'request' => $request,
        ]);

        return back()->with('success', 'Job reintentado correctamente.');
    }

    public function destroy(Request $request, string $id, ActivityService $activity)
    {
        Artisan::call('queue:forget', ['id' => $id]);

        $activity->log('queue_failed_job_deleted', [
            'user' => $request->user(),
            'actor' => $request->user(),
            'description' => 'Job fallido eliminado',
            'metadata' => [
                'failed_job_id' => $id,
            ],
            'request' => $request,
        ]);

        return back()->with('success', 'Job fallido eliminado.');
    }

    public function retryAll(Request $request, ActivityService $activity)
    {
        Artisan::call('queue:retry', ['id' => 'all']);

        $activity->log('queue_failed_jobs_retry_all', [
            'user' => $request->user(),
            'actor' => $request->user(),
            'description' => 'Reintento masivo de jobs fallidos',
            'request' => $request,
        ]);

        return back()->with('success', 'Se enviaron los jobs fallidos a reintento.');
    }

    public function flush(Request $request, ActivityService $activity)
    {
        Artisan::call('queue:flush');

        $activity->log('queue_failed_jobs_flushed', [
            'user' => $request->user(),
            'actor' => $request->user(),
            'description' => 'Limpieza de jobs fallidos',
            'request' => $request,
        ]);

        return back()->with('success', 'Jobs fallidos eliminados correctamente.');
    }

    private function firstLine(string $text): string
    {
        $line = strtok($text, "\n");

        return $line ?: '';
    }
}
