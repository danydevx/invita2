<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use App\Services\AccessService;
use App\Services\ActivityService;
use App\Services\SecurityService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ApiKeyController extends Controller
{
    public function index(Request $request, AccessService $access)
    {
        if (! $access->canUseApi($request->user())) {
            return redirect('/member')->with('error', 'No tiene permiso para usar integraciones API.');
        }

        $keys = ApiKey::query()
            ->where('user_id', $request->user()->id)
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
            ]);

        return Inertia::render('Member/ApiKeys/Index', [
            'apiKeys' => $keys,
        ]);
    }

    public function store(Request $request, ActivityService $activity, AccessService $access, SecurityService $security)
    {
        if (! $access->canUseApi($request->user())) {
            return back()->withErrors(['name' => 'No tiene permiso para usar integraciones API.']);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'expires_at' => ['nullable', 'date', 'after:now'],
        ]);

        $plainKey = $this->generateKey();
        $prefix = $this->prefixFromKey($plainKey);

        $apiKey = ApiKey::create([
            'user_id' => $request->user()->id,
            'name' => $data['name'],
            'key_prefix' => $prefix,
            'key_hash' => hash('sha256', $plainKey),
            'expires_at' => $data['expires_at'] ?? null,
            'is_active' => true,
        ]);

        $activity->log('api_key_created', [
            'user' => $request->user(),
            'actor' => $request->user(),
            'subject' => $apiKey,
            'description' => 'API key creada',
            'request' => $request,
        ]);

        $security->log('api_key_created', $request->user(), $request, 'API key creada', [
            'key_prefix' => $apiKey->key_prefix,
        ]);

        return redirect()
            ->route('member.api-keys.index')
            ->with('success', 'API key creada correctamente. Copie la clave ahora, no volvera a mostrarse.')
            ->with('api_key_plain', $plainKey);
    }

    public function update(Request $request, ApiKey $apiKey, ActivityService $activity, AccessService $access)
    {
        if (! $access->canUseApi($request->user())) {
            return back()->withErrors(['name' => 'No tiene permiso para usar integraciones API.']);
        }

        $this->authorize('update', $apiKey);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'is_active' => ['required', 'boolean'],
            'expires_at' => ['nullable', 'date', 'after:now'],
        ]);

        $apiKey->update([
            'name' => $data['name'],
            'is_active' => $data['is_active'],
            'expires_at' => $data['expires_at'] ?? null,
        ]);

        $activity->log('api_key_updated', [
            'user' => $request->user(),
            'actor' => $request->user(),
            'subject' => $apiKey,
            'description' => 'API key actualizada',
            'request' => $request,
        ]);

        return back()->with('success', 'API key actualizada correctamente.');
    }

    public function destroy(Request $request, ApiKey $apiKey, ActivityService $activity, AccessService $access, SecurityService $security)
    {
        if (! $access->canUseApi($request->user())) {
            return back()->withErrors(['name' => 'No tiene permiso para usar integraciones API.']);
        }

        $this->authorize('delete', $apiKey);

        $apiKey->update([
            'is_active' => false,
            'revoked_at' => now(),
        ]);

        $activity->log('api_key_revoked', [
            'user' => $request->user(),
            'actor' => $request->user(),
            'subject' => $apiKey,
            'description' => 'API key revocada',
            'request' => $request,
        ]);

        $security->log('api_key_revoked', $request->user(), $request, 'API key revocada', [
            'key_prefix' => $apiKey->key_prefix,
        ]);

        return back()->with('success', 'API key revocada correctamente.');
    }

    private function generateKey(): string
    {
        $token = Str::random(40);
        $prefix = substr($token, 0, 8);

        return 'ak_'.$prefix.'_'.Str::random(48);
    }

    private function prefixFromKey(string $key): string
    {
        $parts = explode('_', $key);
        if (count($parts) >= 2) {
            return $parts[0].'_'.$parts[1];
        }

        return substr($key, 0, 8);
    }
}
