<?php

namespace App\Services;

use App\Models\SystemError;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Throwable;

class SystemErrorService
{
    public function logException(Throwable $exception, ?Request $request = null, string $type = 'exception', array $context = []): void
    {
        try {
            $data = [
                'user_id' => $request?->user()?->id,
                'type' => $type,
                'message' => $exception->getMessage() ?: 'Error sin mensaje',
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'url' => $request?->fullUrl(),
                'method' => $request?->method(),
                'ip_address' => $request?->ip(),
                'user_agent' => $request?->userAgent(),
                'exception_class' => $exception::class,
                'trace' => $exception->getTraceAsString(),
                'context' => $this->sanitizeContext($context),
                'is_resolved' => false,
            ];

            SystemError::create($data);
        } catch (Throwable $loggingException) {
            // No interrumpir el flujo principal si falla el registro.
        }
    }

    public function logMessage(string $message, ?Request $request = null, string $type = 'system', array $context = []): void
    {
        try {
            SystemError::create([
                'user_id' => $request?->user()?->id,
                'type' => $type,
                'message' => $message,
                'url' => $request?->fullUrl(),
                'method' => $request?->method(),
                'ip_address' => $request?->ip(),
                'user_agent' => $request?->userAgent(),
                'context' => $this->sanitizeContext($context),
                'is_resolved' => false,
            ]);
        } catch (Throwable $loggingException) {
            // No interrumpir el flujo principal si falla el registro.
        }
    }

    private function sanitizeContext(array $context): array
    {
        $blockedKeys = [
            'password',
            'password_confirmation',
            'token',
            'secret',
            'authorization',
            'api_key',
            'stripe',
            'signature',
        ];

        $clean = Arr::dot($context);
        foreach ($clean as $key => $value) {
            foreach ($blockedKeys as $blocked) {
                if (str_contains(strtolower($key), $blocked)) {
                    $clean[$key] = '[redacted]';
                }
            }
        }

        return Arr::undot($clean);
    }
}
