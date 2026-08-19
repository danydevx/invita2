<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebhookDelivery;
use App\Models\WebhookEndpoint;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WebhookController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:20'],
        ]);

        $q = trim((string) ($filters['q'] ?? ''));
        $status = $filters['status'] ?? '';

        $query = WebhookEndpoint::query()->with(['user:id,name,email']);

        if ($q !== '') {
            $needle = mb_strtolower($q);
            $query->where(function ($qBuilder) use ($needle) {
                $qBuilder->whereRaw('LOWER(name) like ?', ['%'.$needle.'%'])
                    ->orWhereRaw('LOWER(url) like ?', ['%'.$needle.'%'])
                    ->orWhereHas('user', function ($userQuery) use ($needle) {
                        $userQuery->whereRaw('LOWER(name) like ?', ['%'.$needle.'%'])
                            ->orWhereRaw('LOWER(email) like ?', ['%'.$needle.'%']);
                    });
            });
        }

        if ($status === 'active') {
            $query->where('is_active', true);
        }
        if ($status === 'inactive') {
            $query->where('is_active', false);
        }

        $endpoints = $query
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString()
            ->through(fn ($endpoint) => [
                'id' => $endpoint->id,
                'name' => $endpoint->name,
                'url' => $endpoint->url,
                'is_active' => $endpoint->is_active,
                'last_used_at' => $endpoint->last_used_at?->toDateTimeString(),
                'failure_count' => $endpoint->failure_count,
                'created_at' => $endpoint->created_at?->toDateTimeString(),
                'user' => $endpoint->user ? [
                    'id' => $endpoint->user->id,
                    'name' => $endpoint->user->name,
                    'email' => $endpoint->user->email,
                ] : null,
            ]);

        return Inertia::render('Admin/Webhooks/Index', [
            'endpoints' => $endpoints,
            'filters' => [
                'q' => $q,
                'status' => $status,
            ],
        ]);
    }

    public function show(WebhookEndpoint $webhook)
    {
        $webhook->load(['user:id,name,email']);

        return Inertia::render('Admin/Webhooks/Show', [
            'webhook' => [
                'id' => $webhook->id,
                'name' => $webhook->name,
                'url' => $webhook->url,
                'events' => $webhook->events ?? [],
                'is_active' => $webhook->is_active,
                'last_used_at' => $webhook->last_used_at?->toDateTimeString(),
                'failure_count' => $webhook->failure_count,
                'created_at' => $webhook->created_at?->toDateTimeString(),
                'user' => $webhook->user ? [
                    'id' => $webhook->user->id,
                    'name' => $webhook->user->name,
                    'email' => $webhook->user->email,
                ] : null,
            ],
        ]);
    }

    public function deliveries(WebhookEndpoint $webhook)
    {
        $deliveries = WebhookDelivery::query()
            ->where('webhook_endpoint_id', $webhook->id)
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString()
            ->through(fn ($delivery) => [
                'id' => $delivery->id,
                'event' => $delivery->event,
                'response_status' => $delivery->response_status,
                'error_message' => $delivery->error_message,
                'delivered_at' => $delivery->delivered_at?->toDateTimeString(),
                'failed_at' => $delivery->failed_at?->toDateTimeString(),
                'attempt_count' => $delivery->attempt_count,
                'created_at' => $delivery->created_at?->toDateTimeString(),
            ]);

        return Inertia::render('Admin/Webhooks/Deliveries', [
            'webhook' => [
                'id' => $webhook->id,
                'name' => $webhook->name,
                'url' => $webhook->url,
            ],
            'deliveries' => $deliveries,
        ]);
    }
}
