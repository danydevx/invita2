<?php

namespace Modules\AiChatbot\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class RateLimitChatbot
{
    public function handle(Request $request, Closure $next): Response
    {
        $key = 'chatbot:' . $request->ip();

        $maxAttempts = 30;
        $decayMinutes = 1;

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);

            return response()->json([
                'success' => false,
                'error' => 'Too many requests',
                'message' => "Demasiadas solicitudes. Por favor espera {$seconds} segundos.",
                'retry_after' => $seconds,
            ], 429);
        }

        RateLimiter::hit($key, $decayMinutes * 60);

        return $next($request);
    }
}
