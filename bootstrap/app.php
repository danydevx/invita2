<?php

use App\Http\Middleware\ApiKeyAuth;
use App\Http\Middleware\CheckMaintenanceMode;
use App\Http\Middleware\CheckModuleEnabled;
use App\Http\Middleware\EnsureAdminOrUser;
use App\Http\Middleware\EnsureLegalDocumentsAccepted;
use App\Http\Middleware\EnsurePermissionOrUser;
use App\Http\Middleware\EnsureUserActive;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\MinisiteTemplate;
use App\Services\SystemErrorService;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            CheckMaintenanceMode::class,
            MinisiteTemplate::class,
            HandleInertiaRequests::class,
            EnsureLegalDocumentsAccepted::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'stripe/webhook',
            'm/*/ai-chatbot/chat',
        ]);

        $middleware->alias([
            'active' => EnsureUserActive::class,
            'admin_or_user' => EnsureAdminOrUser::class,
            'permission_or_user' => EnsurePermissionOrUser::class,
            'module' => CheckModuleEnabled::class,
            'verified' => EnsureEmailIsVerified::class,
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
            'api_key' => ApiKeyAuth::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->reportable(function (Throwable $exception) {
            if ($exception instanceof ValidationException) {
                return;
            }

            if ($exception instanceof HttpExceptionInterface && $exception->getStatusCode() < 500) {
                return;
            }

            $request = app()->has(Request::class) ? app(Request::class) : null;
            $type = 'exception';

            app(SystemErrorService::class)->logException($exception, $request, $type);
        });
    })->create();
