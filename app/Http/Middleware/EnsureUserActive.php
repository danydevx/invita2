<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->is_active) {
            return redirect('/email/verify')->with('error', 'Debes verificar tu email para activar tu cuenta.');
        }

        return $next($request);
    }
}
