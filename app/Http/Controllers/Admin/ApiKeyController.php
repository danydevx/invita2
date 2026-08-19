<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ApiKeyController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:20'],
        ]);

        $q = trim((string) ($filters['q'] ?? ''));
        $status = $filters['status'] ?? '';

        $query = ApiKey::query()->with(['user:id,name,email']);

        if ($q !== '') {
            $needle = mb_strtolower($q);
            $query->where(function ($qBuilder) use ($needle) {
                $qBuilder->whereRaw('LOWER(name) like ?', ['%'.$needle.'%'])
                    ->orWhereRaw('LOWER(key_prefix) like ?', ['%'.$needle.'%'])
                    ->orWhereHas('user', function ($userQuery) use ($needle) {
                        $userQuery->whereRaw('LOWER(name) like ?', ['%'.$needle.'%'])
                            ->orWhereRaw('LOWER(email) like ?', ['%'.$needle.'%']);
                    });
            });
        }

        if ($status === 'active') {
            $query->where('is_active', true)->whereNull('revoked_at');
        }
        if ($status === 'revoked') {
            $query->whereNotNull('revoked_at');
        }
        if ($status === 'inactive') {
            $query->where('is_active', false)->whereNull('revoked_at');
        }

        $keys = $query
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString()
            ->through(fn ($key) => [
                'id' => $key->id,
                'name' => $key->name,
                'key_prefix' => $key->key_prefix,
                'is_active' => $key->is_active,
                'revoked_at' => $key->revoked_at?->toDateTimeString(),
                'last_used_at' => $key->last_used_at?->toDateTimeString(),
                'created_at' => $key->created_at?->toDateTimeString(),
                'user' => $key->user ? [
                    'id' => $key->user->id,
                    'name' => $key->user->name,
                    'email' => $key->user->email,
                ] : null,
            ]);

        return Inertia::render('Admin/ApiKeys/Index', [
            'apiKeys' => $keys,
            'filters' => [
                'q' => $q,
                'status' => $status,
            ],
        ]);
    }

    public function show(ApiKey $apiKey)
    {
        $apiKey->load(['user:id,name,email']);

        return Inertia::render('Admin/ApiKeys/Show', [
            'apiKey' => [
                'id' => $apiKey->id,
                'name' => $apiKey->name,
                'key_prefix' => $apiKey->key_prefix,
                'is_active' => $apiKey->is_active,
                'revoked_at' => $apiKey->revoked_at?->toDateTimeString(),
                'expires_at' => $apiKey->expires_at?->toDateTimeString(),
                'last_used_at' => $apiKey->last_used_at?->toDateTimeString(),
                'last_used_ip' => $apiKey->last_used_ip,
                'created_at' => $apiKey->created_at?->toDateTimeString(),
                'user' => $apiKey->user ? [
                    'id' => $apiKey->user->id,
                    'name' => $apiKey->user->name,
                    'email' => $apiKey->user->email,
                ] : null,
            ],
        ]);
    }

    public function revoke(Request $request, ApiKey $apiKey, ActivityService $activity)
    {
        if (! $apiKey->revoked_at) {
            $apiKey->update([
                'is_active' => false,
                'revoked_at' => now(),
            ]);

            $activity->log('api_key_revoked', [
                'user' => $apiKey->user,
                'actor' => $request->user(),
                'subject' => $apiKey,
                'description' => 'API key revocada por admin',
                'request' => $request,
            ]);
        }

        return back()->with('success', 'API key revocada correctamente.');
    }
}
