<?php

namespace App\Services;

use App\Models\SecurityEvent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Throwable;

class SecurityService
{
    public function log(string $eventType, ?User $user = null, ?Request $request = null, ?string $description = null, array $metadata = []): void
    {
        try {
            SecurityEvent::create([
                'user_id' => $user?->id,
                'event_type' => $eventType,
                'description' => $description,
                'ip_address' => $request?->ip(),
                'user_agent' => $request?->userAgent(),
                'metadata' => $this->sanitize($metadata),
            ]);
        } catch (Throwable $e) {
            // Evitar fallos secundarios.
        }
    }

    private function sanitize(array $metadata): array
    {
        $blocked = ['password', 'token', 'secret', 'authorization', 'api_key', 'signature'];
        $flat = Arr::dot($metadata);

        foreach ($flat as $key => $value) {
            foreach ($blocked as $blockedKey) {
                if (str_contains(strtolower($key), $blockedKey)) {
                    $flat[$key] = '[redacted]';
                }
            }
        }

        return Arr::undot($flat);
    }
}
