<?php

namespace App\Http\Middleware;

use App\Services\ModuleService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckModuleEnabled
{
    public function handle(Request $request, Closure $next, string $module): Response
    {
        // Bloquea el acceso si el modulo esta desactivado.
        if (! app(ModuleService::class)->isEnabled($module)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Modulo no disponible.'], 403);
            }

            // Redirige segun el area actual para evitar loops.
            $target = str_starts_with($request->path(), 'admin') ? '/dashboard' : '/member';

            return redirect($target)->with('error', 'Este modulo no esta disponible.');
        }

        return $next($request);
    }
}
