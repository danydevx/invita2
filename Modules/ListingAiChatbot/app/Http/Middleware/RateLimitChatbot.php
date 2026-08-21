<?php

namespace Modules\ListingAiChatbot\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class RateLimitChatbot
{
    public function handle(Request $request, Closure $next): Response
    {
        $slug = $request->route('slug');
        $key = 'chatbot:' . $slug;

        $maxAttempts = 30;
        $decayMinutes = 1;

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'error' => 'Demasiadas solicitudes. Intenta de nuevo en ' . $seconds . ' segundos.',
            ], 429);
        }

        RateLimiter::hit($key, $decayMinutes * 60);

        return $next($request);
    }
}
