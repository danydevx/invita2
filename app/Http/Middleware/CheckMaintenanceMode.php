<?php

namespace App\Http\Middleware;

use App\Services\SettingService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    public function handle(Request $request, Closure $next): Response
    {
        $settings = app(SettingService::class);
        $enabled = $this->toBool($settings->get('system.maintenance_mode'));

        if (! $enabled) {
            return $next($request);
        }

        if ($this->isAllowed($request)) {
            return $next($request);
        }

        return redirect()->route('maintenance');
    }

    private function isAllowed(Request $request): bool
    {
        if ($request->user() && $request->user()->hasAnyRole(['admin', 'superadmin'])) {
            return true;
        }

        $path = '/'.ltrim($request->path(), '/');
        $allowlist = [
            '/maintenance',
            '/health',
            '/up',
            '/login',
            '/logout',
            '/stripe/webhook',
        ];

        return in_array($path, $allowlist, true);
    }

    private function toBool($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }
        if ($value === null) {
            return false;
        }

        return in_array((string) $value, ['1', 'true', 'on', 'yes'], true);
    }
}
