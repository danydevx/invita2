<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemError;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SystemErrorController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'type' => ['nullable', 'string', 'max:50'],
            'is_resolved' => ['nullable'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'q' => ['nullable', 'string', 'max:255'],
        ]);

        $query = SystemError::query()->with(['user:id,name,email']);

        $type = $filters['type'] ?? '';
        $resolvedFilter = $filters['is_resolved'] ?? null;
        $dateFrom = $filters['date_from'] ?? '';
        $dateTo = $filters['date_to'] ?? '';
        $q = trim((string) ($filters['q'] ?? ''));

        if ($type !== '') {
            $query->where('type', $type);
        }

        $resolved = $this->parseBooleanFilter($resolvedFilter);
        if ($resolved !== null) {
            $query->where('is_resolved', $resolved);
        }

        if ($q !== '') {
            $needle = mb_strtolower($q);
            $query->where(function ($qBuilder) use ($needle) {
                $qBuilder->whereRaw('LOWER(message) like ?', ['%'.$needle.'%'])
                    ->orWhereRaw('LOWER(exception_class) like ?', ['%'.$needle.'%']);
            });
        }

        if ($dateFrom !== '') {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo !== '') {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $errors = $query
            ->latest()
            ->paginate(15)
            ->withQueryString()
            ->through(fn ($error) => [
                'id' => $error->id,
                'type' => $error->type,
                'message' => $error->message,
                'url' => $error->url,
                'is_resolved' => $error->is_resolved,
                'created_at' => $error->created_at?->toDateTimeString(),
                'user' => $error->user ? [
                    'id' => $error->user->id,
                    'name' => $error->user->name,
                    'email' => $error->user->email,
                ] : null,
            ]);

        $types = SystemError::query()
            ->select('type')
            ->distinct()
            ->orderBy('type')
            ->pluck('type')
            ->values();

        return Inertia::render('Admin/SystemErrors/Index', [
            'errors' => $errors,
            'types' => $types,
            'filters' => [
                'type' => $type,
                'is_resolved' => $resolvedFilter,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'q' => $q,
            ],
        ]);
    }

    public function show(SystemError $error)
    {
        $error->load(['user:id,name,email']);

        return Inertia::render('Admin/SystemErrors/Show', [
            'error' => [
                'id' => $error->id,
                'type' => $error->type,
                'message' => $error->message,
                'exception_class' => $error->exception_class,
                'file' => $error->file,
                'line' => $error->line,
                'url' => $error->url,
                'method' => $error->method,
                'ip_address' => $error->ip_address,
                'user_agent' => $error->user_agent,
                'trace' => $error->trace,
                'context' => $error->context,
                'is_resolved' => $error->is_resolved,
                'resolved_at' => $error->resolved_at?->toDateTimeString(),
                'created_at' => $error->created_at?->toDateTimeString(),
                'user' => $error->user ? [
                    'id' => $error->user->id,
                    'name' => $error->user->name,
                    'email' => $error->user->email,
                ] : null,
            ],
        ]);
    }

    public function resolve(Request $request, SystemError $error, ActivityService $activity)
    {
        if (! $error->is_resolved) {
            $error->update([
                'is_resolved' => true,
                'resolved_at' => now(),
            ]);

            $activity->log('system_error_resolved', [
                'user' => $request->user(),
                'actor' => $request->user(),
                'subject' => $error,
                'description' => 'Error marcado como resuelto',
                'request' => $request,
            ]);
        }

        return redirect()->route('admin.system-errors.show', $error)->with('success', 'Error marcado como resuelto.');
    }

    private function parseBooleanFilter($value): ?bool
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_bool($value)) {
            return $value;
        }

        if (is_string($value)) {
            $normalized = strtolower($value);
            if (in_array($normalized, ['1', 'true', 'yes'], true)) {
                return true;
            }
            if (in_array($normalized, ['0', 'false', 'no'], true)) {
                return false;
            }
        }

        return null;
    }
}
