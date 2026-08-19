<?php

namespace App\Services;

use App\Models\User;

class AccessService
{
    public function canUseApi(User $user): bool
    {
        // Permite acceso total al usuario ID 1.
        if ((int) $user->id === 1) {
            return true;
        }

        // Valida permiso + feature flag para uso de API keys.
        return app(ModuleService::class)->isEnabled('api')
            && app(ModuleService::class)->isEnabled('integrations')
            && $this->hasPermission($user, 'api-keys.manage')
            && $this->featureEnabled($user, ['features.api_enabled', 'can_use_api'], false);
    }

    public function canUseWebhooks(User $user): bool
    {
        // Permite acceso total al usuario ID 1.
        if ((int) $user->id === 1) {
            return true;
        }

        // Valida permiso + feature flag para uso de webhooks.
        return app(ModuleService::class)->isEnabled('webhooks')
            && app(ModuleService::class)->isEnabled('integrations')
            && $this->hasPermission($user, 'webhooks.manage')
            && $this->featureEnabled($user, ['features.webhooks_enabled', 'can_use_webhooks'], false);
    }

    public function canUploadFiles(User $user): bool
    {
        // Permite acceso total al usuario ID 1.
        if ((int) $user->id === 1) {
            return true;
        }

        // Usa el feature flag actual para habilitar subida de archivos.
        return app(ModuleService::class)->isEnabled('media')
            && $this->featureEnabled($user, ['can_upload_files'], true);
    }

    public function hasPermission(User $user, string $permission): bool
    {
        // Permite a super-admin pasar todas las validaciones de permisos.
        if ($user->hasAnyRole(['superadmin'])) {
            return true;
        }

        return $user->can($permission);
    }

    private function featureEnabled(User $user, array $keys, bool $default): bool
    {
        $features = app(FeatureService::class);

        foreach ($keys as $key) {
            if ($features->enabled($user, $key, $default)) {
                return true;
            }
        }

        return false;
    }
}
