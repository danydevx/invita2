<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminOrUser
{
    public function handle(Request $request, Closure $next, string $userId = '1'): Response
    {
        $user = $request->user();

        if ($user && (string) $user->id === $userId) {
            return $next($request);
        }

        if ($user && $user->hasAnyRole(['admin', 'superadmin'])) {
            return $next($request);
        }

        abort(403);
    }
}
