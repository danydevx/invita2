<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Throwable;

class HealthController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $status = 'ok';
        $database = $this->checkDatabase();
        $cache = $this->checkCache();
        $queue = $this->checkQueue();

        if (in_array('fail', [$database, $cache, $queue], true)) {
            $status = 'degraded';
        }

        return response()->json([
            'status' => $status,
            'database' => $database,
            'queue' => $queue,
            'cache' => $cache,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    private function checkDatabase(): string
    {
        try {
            DB::connection()->getPdo();

            return 'ok';
        } catch (Throwable $e) {
            return 'fail';
        }
    }

    private function checkCache(): string
    {
        try {
            Cache::put('health_check', 'ok', 5);

            return Cache::get('health_check') === 'ok' ? 'ok' : 'fail';
        } catch (Throwable $e) {
            return 'fail';
        }
    }

    private function checkQueue(): string
    {
        try {
            Queue::connection()->getQueue(null);

            return 'ok';
        } catch (Throwable $e) {
            return 'fail';
        }
    }
}
