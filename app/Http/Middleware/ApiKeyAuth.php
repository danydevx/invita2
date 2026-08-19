<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiKeyAuth
{
    public function handle(Request $request, Closure $next)
    {
        $key = $this->extractKey($request);
        if (! $key) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        $hash = hash('sha256', $key);
        $apiKey = ApiKey::query()
            ->with('user:id,is_active')
            ->where('key_hash', $hash)
            ->first();

        if (! $apiKey || ! $this->isKeyValid($apiKey)) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        Auth::onceUsingId($apiKey->user_id);
        $request->setUserResolver(fn () => $apiKey->user);
        $request->attributes->set('api_key_id', $apiKey->id);

        $apiKey->update([
            'last_used_at' => now(),
            'last_used_ip' => $request->ip(),
        ]);

        return $next($request);
    }

    private function extractKey(Request $request): ?string
    {
        $header = $request->header('Authorization', '');
        if (str_starts_with($header, 'Bearer ')) {
            return trim(substr($header, 7));
        }

        $key = $request->header('X-API-Key');
        if (is_string($key) && $key !== '') {
            return trim($key);
        }

        return null;
    }

    private function isKeyValid(ApiKey $apiKey): bool
    {
        if (! $apiKey->is_active) {
            return false;
        }

        if ($apiKey->revoked_at) {
            return false;
        }

        if ($apiKey->expires_at && $apiKey->expires_at->isPast()) {
            return false;
        }

        if (! $apiKey->user || ! $apiKey->user->is_active) {
            return false;
        }

        return true;
    }
}
