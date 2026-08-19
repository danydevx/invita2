<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use App\Models\SystemError;
use App\Models\WebhookDelivery;
use App\Models\WebhookEndpoint;
use App\Services\AccessService;
use App\Services\WebhookService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IntegrationController extends Controller
{
    public function index(Request $request, AccessService $access)
    {
        $user = $request->user();

        // Usa el helper central para validar acceso a integraciones.
        $canUseApi = $access->canUseApi($user);
        $canUseWebhooks = $access->canUseWebhooks($user);

        // Obtiene las API keys visibles para el usuario y solo si el feature esta habilitado.
        $apiKeys = $canUseApi
            ? ApiKey::query()
                ->where('user_id', $user->id)
                ->orderByDesc('id')
                ->get()
                ->map(fn ($key) => [
                    'id' => $key->id,
                    'name' => $key->name,
                    'key_prefix' => $key->key_prefix,
                    'is_active' => $key->is_active,
                    'last_used_at' => $key->last_used_at?->toDateTimeString(),
                    'expires_at' => $key->expires_at?->toDateString(),
                    'revoked_at' => $key->revoked_at?->toDateTimeString(),
                    'created_at' => $key->created_at?->toDateTimeString(),
                    'metadata' => $key->metadata,
                ])
            : collect();

        // Obtiene webhooks y sus ultimas entregas para mostrar un estado basico.
        $endpoints = $canUseWebhooks
            ? WebhookEndpoint::query()
                ->where('user_id', $user->id)
                ->orderByDesc('id')
                ->get()
            : collect();

        $endpointIds = $endpoints->pluck('id')->all();
        $lastDeliveries = $endpointIds
            ? WebhookDelivery::query()
                ->whereIn('webhook_endpoint_id', $endpointIds)
                ->orderByDesc('id')
                ->get()
                ->groupBy('webhook_endpoint_id')
                ->map(fn ($items) => $items->first())
            : collect();

        $webhooks = $endpoints->map(fn ($endpoint) => [
            'id' => $endpoint->id,
            'name' => $endpoint->name,
            'url' => $endpoint->url,
            'is_active' => $endpoint->is_active,
            'events' => $endpoint->events ?? [],
            'last_used_at' => $endpoint->last_used_at?->toDateTimeString(),
            'failure_count' => $endpoint->failure_count,
            'last_delivery' => $lastDeliveries->get($endpoint->id)
                ? [
                    'event' => $lastDeliveries->get($endpoint->id)->event,
                    'response_status' => $lastDeliveries->get($endpoint->id)->response_status,
                    'failed_at' => $lastDeliveries->get($endpoint->id)->failed_at?->toDateTimeString(),
                    'delivered_at' => $lastDeliveries->get($endpoint->id)->delivered_at?->toDateTimeString(),
                ]
                : null,
        ]);

        // Resume el estado de integraciones para el panel.
        $activeApiKeysCount = $canUseApi
            ? ApiKey::query()->where('user_id', $user->id)->where('is_active', true)->whereNull('revoked_at')->count()
            : 0;
        $activeWebhooksCount = $canUseWebhooks
            ? WebhookEndpoint::query()->where('user_id', $user->id)->where('is_active', true)->count()
            : 0;

        $recentDeliveries = $endpointIds
            ? WebhookDelivery::query()
                ->whereIn('webhook_endpoint_id', $endpointIds)
                ->orderByDesc('id')
                ->limit(5)
                ->get()
                ->map(fn ($delivery) => [
                    'id' => $delivery->id,
                    'event' => $delivery->event,
                    'response_status' => $delivery->response_status,
                    'failed_at' => $delivery->failed_at?->toDateTimeString(),
                    'delivered_at' => $delivery->delivered_at?->toDateTimeString(),
                    'attempt_count' => $delivery->attempt_count,
                ])
            : collect();

        $recentErrors = SystemError::query()
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->limit(5)
            ->get()
            ->map(fn ($error) => [
                'id' => $error->id,
                'type' => $error->type,
                'message' => $error->message,
                'created_at' => $error->created_at?->toDateTimeString(),
            ]);

        return Inertia::render('Member/Integrations/Index', [
            'canUseApi' => $canUseApi,
            'canUseWebhooks' => $canUseWebhooks,
            'appUrl' => (string) config('app.url'),
            'apiKeys' => $apiKeys,
            'webhooks' => $webhooks,
            'availableEvents' => WebhookService::EVENTS,
            'stats' => [
                'active_api_keys' => $activeApiKeysCount,
                'active_webhooks' => $activeWebhooksCount,
            ],
            'recentDeliveries' => $recentDeliveries,
            'recentErrors' => $recentErrors,
        ]);
    }

    public function apiDocumentation(Request $request, AccessService $access)
    {
        $user = $request->user();

        // Expone informacion basica para documentacion sin exponer secretos.
        return response()->json([
            'api_base' => (string) config('app.url'),
            'auth' => [
                'header' => 'Authorization: Bearer {API_KEY}',
                'example' => 'curl '.config('app.url').'/api/me -H "Authorization: Bearer YOUR_API_KEY"',
            ],
            'webhook' => [
                'signature_header' => 'X-Signature',
                'signature_note' => 'Firma HMAC SHA256 usando el secret del webhook.',
                'events' => WebhookService::EVENTS,
            ],
            'features' => [
                'api_enabled' => $access->canUseApi($user),
                'webhooks_enabled' => $access->canUseWebhooks($user),
            ],
        ]);
    }
}
