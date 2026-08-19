<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MinisiteTemplate
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('b/*')) {
            $request->attributes->set('use_minisite_template', true);
        }

        return $next($request);
    }
}
