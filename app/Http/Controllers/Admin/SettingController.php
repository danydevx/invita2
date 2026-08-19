<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Services\ActivityService;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingController extends Controller
{
    public function index(SettingService $settings)
    {
        $defaults = [
            'app.name' => config('app.name'),
            'app.short_name' => '',
            'app.email' => config('mail.from.address'),
            'app.phone' => '',
            'app.website' => config('app.url'),
            'app.address' => '',
            'app.description' => '',
            'branding.logo' => '',
            'branding.favicon' => '',
            'branding.primary_color' => '#2563eb',
            'branding.secondary_color' => '#0f172a',
            'branding.footer_text' => '',
            'branding.auth_image' => '',
            'branding.system_tagline' => '',
            'regional.timezone' => config('app.timezone'),
            'regional.locale' => config('app.locale'),
            'regional.currency' => 'USD',
            'auth.allow_registration' => '0',
            'auth.require_email_verification' => '1',
            'auth.require_admin_approval' => '0',
            'auth.login_with_email_only' => '1',
            'auth.password_min_length' => '8',
            'auth.password_require_letters' => '1',
            'auth.password_require_numbers' => '1',
            'system.default_pagination' => '10',
            'system.maintenance_mode' => '0',
            'system.maintenance_message' => '',
            'system.enable_activity_log' => '1',
            'system.enable_system_errors' => '1',
            'system.enable_internal_notifications' => '1',
            'billing.enabled' => '1',
            'billing.default_plan_id' => '',
            'billing.currency' => 'USD',
            'billing.trial_days' => '0',
            'billing.allow_plan_changes' => '1',
            'billing.allow_cancellations' => '1',
            'features.api_enabled' => '1',
            'features.webhooks_enabled' => '1',
            'features.support_enabled' => '1',
            'features.help_center_enabled' => '1',
            'features.exports_enabled' => '1',
            'features.member_billing_enabled' => '1',
            'mail.from_name' => config('mail.from.name'),
            'mail.from_address' => config('mail.from.address'),
            'mail.reply_to_address' => '',
            'mail.footer_signature' => '',
        ];

        $stored = $settings->getMany(array_keys($defaults));

        $data = [
            'app' => [
                'name' => $stored['app.name'] ?? $defaults['app.name'],
                'short_name' => $stored['app.short_name'] ?? $defaults['app.short_name'],
                'email' => $stored['app.email'] ?? $defaults['app.email'],
                'phone' => $stored['app.phone'] ?? $defaults['app.phone'],
                'website' => $stored['app.website'] ?? $defaults['app.website'],
                'address' => $stored['app.address'] ?? $defaults['app.address'],
                'description' => $stored['app.description'] ?? $defaults['app.description'],
            ],
            'branding' => [
                'logo' => $stored['branding.logo'] ?? $defaults['branding.logo'],
                'favicon' => $stored['branding.favicon'] ?? $defaults['branding.favicon'],
                'primary_color' => $stored['branding.primary_color'] ?? $defaults['branding.primary_color'],
                'secondary_color' => $stored['branding.secondary_color'] ?? $defaults['branding.secondary_color'],
                'footer_text' => $stored['branding.footer_text'] ?? $defaults['branding.footer_text'],
                'auth_image' => $stored['branding.auth_image'] ?? $defaults['branding.auth_image'],
                'system_tagline' => $stored['branding.system_tagline'] ?? $defaults['branding.system_tagline'],
            ],
            'regional' => [
                'timezone' => $stored['regional.timezone'] ?? $defaults['regional.timezone'],
                'locale' => $stored['regional.locale'] ?? $defaults['regional.locale'],
                'currency' => $stored['regional.currency'] ?? $defaults['regional.currency'],
            ],
            'auth' => [
                'allow_registration' => $this->toBool($stored['auth.allow_registration'] ?? $defaults['auth.allow_registration']),
                'require_email_verification' => $this->toBool($stored['auth.require_email_verification'] ?? $defaults['auth.require_email_verification']),
                'require_admin_approval' => $this->toBool($stored['auth.require_admin_approval'] ?? $defaults['auth.require_admin_approval']),
                'login_with_email_only' => $this->toBool($stored['auth.login_with_email_only'] ?? $defaults['auth.login_with_email_only']),
                'password_min_length' => (int) ($stored['auth.password_min_length'] ?? $defaults['auth.password_min_length']),
                'password_require_letters' => $this->toBool($stored['auth.password_require_letters'] ?? $defaults['auth.password_require_letters']),
                'password_require_numbers' => $this->toBool($stored['auth.password_require_numbers'] ?? $defaults['auth.password_require_numbers']),
            ],
            'system' => [
                'default_pagination' => (int) ($stored['system.default_pagination'] ?? $defaults['system.default_pagination']),
                'maintenance_mode' => $this->toBool($stored['system.maintenance_mode'] ?? $defaults['system.maintenance_mode']),
                'maintenance_message' => $stored['system.maintenance_message'] ?? $defaults['system.maintenance_message'],
                'enable_activity_log' => $this->toBool($stored['system.enable_activity_log'] ?? $defaults['system.enable_activity_log']),
                'enable_system_errors' => $this->toBool($stored['system.enable_system_errors'] ?? $defaults['system.enable_system_errors']),
                'enable_internal_notifications' => $this->toBool($stored['system.enable_internal_notifications'] ?? $defaults['system.enable_internal_notifications']),
            ],
            'billing' => [
                'enabled' => $this->toBool($stored['billing.enabled'] ?? $defaults['billing.enabled']),
                'default_plan_id' => $stored['billing.default_plan_id'] ?? $defaults['billing.default_plan_id'],
                'currency' => $stored['billing.currency'] ?? $defaults['billing.currency'],
                'trial_days' => (int) ($stored['billing.trial_days'] ?? $defaults['billing.trial_days']),
                'allow_plan_changes' => $this->toBool($stored['billing.allow_plan_changes'] ?? $defaults['billing.allow_plan_changes']),
                'allow_cancellations' => $this->toBool($stored['billing.allow_cancellations'] ?? $defaults['billing.allow_cancellations']),
            ],
            'features' => [
                'api_enabled' => $this->toBool($stored['features.api_enabled'] ?? $defaults['features.api_enabled']),
                'webhooks_enabled' => $this->toBool($stored['features.webhooks_enabled'] ?? $defaults['features.webhooks_enabled']),
                'support_enabled' => $this->toBool($stored['features.support_enabled'] ?? $defaults['features.support_enabled']),
                'help_center_enabled' => $this->toBool($stored['features.help_center_enabled'] ?? $defaults['features.help_center_enabled']),
                'exports_enabled' => $this->toBool($stored['features.exports_enabled'] ?? $defaults['features.exports_enabled']),
                'member_billing_enabled' => $this->toBool($stored['features.member_billing_enabled'] ?? $defaults['features.member_billing_enabled']),
            ],
            'mail' => [
                'from_name' => $stored['mail.from_name'] ?? $defaults['mail.from_name'],
                'from_address' => $stored['mail.from_address'] ?? $defaults['mail.from_address'],
                'reply_to_address' => $stored['mail.reply_to_address'] ?? $defaults['mail.reply_to_address'],
                'footer_signature' => $stored['mail.footer_signature'] ?? $defaults['mail.footer_signature'],
            ],
        ];

        $plans = Plan::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn ($plan) => [
                'value' => $plan->id,
                'label' => $plan->name,
            ]);

        return Inertia::render('Admin/Settings/Index', [
            'settings' => $data,
            'plans' => $plans,
        ]);
    }

    public function update(Request $request, ActivityService $activity, SettingService $settings)
    {
        $previousMaintenance = $this->toBool($settings->get('system.maintenance_mode'));
        $data = $request->validate([
            'app.name' => ['required', 'string', 'max:150'],
            'app.short_name' => ['nullable', 'string', 'max:80'],
            'app.email' => ['nullable', 'email', 'max:150'],
            'app.phone' => ['nullable', 'string', 'max:50'],
            'app.website' => ['nullable', 'url', 'max:255'],
            'app.address' => ['nullable', 'string', 'max:255'],
            'app.description' => ['nullable', 'string', 'max:500'],
            'branding.logo' => ['nullable', 'string', 'max:255'],
            'branding.favicon' => ['nullable', 'string', 'max:255'],
            'branding.primary_color' => ['nullable', 'string', 'max:20'],
            'branding.secondary_color' => ['nullable', 'string', 'max:20'],
            'branding.footer_text' => ['nullable', 'string', 'max:255'],
            'branding.auth_image' => ['nullable', 'string', 'max:255'],
            'branding.system_tagline' => ['nullable', 'string', 'max:255'],
            'regional.timezone' => ['required', 'string', 'max:100'],
            'regional.locale' => ['required', 'string', 'max:10'],
            'regional.currency' => ['required', 'string', 'max:10'],
            'auth.allow_registration' => ['boolean'],
            'auth.require_email_verification' => ['boolean'],
            'auth.require_admin_approval' => ['boolean'],
            'auth.login_with_email_only' => ['boolean'],
            'auth.password_min_length' => ['required', 'integer', 'min:6', 'max:32'],
            'auth.password_require_letters' => ['boolean'],
            'auth.password_require_numbers' => ['boolean'],
            'system.default_pagination' => ['required', 'integer', 'min:5', 'max:100'],
            'system.maintenance_mode' => ['boolean'],
            'system.maintenance_message' => ['nullable', 'string', 'max:500'],
            'system.enable_activity_log' => ['boolean'],
            'system.enable_system_errors' => ['boolean'],
            'system.enable_internal_notifications' => ['boolean'],
            'billing.enabled' => ['boolean'],
            'billing.default_plan_id' => ['nullable', 'exists:plans,id'],
            'billing.currency' => ['nullable', 'string', 'max:10'],
            'billing.trial_days' => ['nullable', 'integer', 'min:0', 'max:365'],
            'billing.allow_plan_changes' => ['boolean'],
            'billing.allow_cancellations' => ['boolean'],
            'features.api_enabled' => ['boolean'],
            'features.webhooks_enabled' => ['boolean'],
            'features.support_enabled' => ['boolean'],
            'features.help_center_enabled' => ['boolean'],
            'features.exports_enabled' => ['boolean'],
            'features.member_billing_enabled' => ['boolean'],
            'mail.from_name' => ['nullable', 'string', 'max:150'],
            'mail.from_address' => ['nullable', 'email', 'max:150'],
            'mail.reply_to_address' => ['nullable', 'email', 'max:150'],
            'mail.footer_signature' => ['nullable', 'string', 'max:255'],
        ]);

        $settings->set('app.name', $data['app']['name']);
        $settings->set('app.short_name', $data['app']['short_name'] ?? '');
        $settings->set('app.email', $data['app']['email'] ?? '');
        $settings->set('app.phone', $data['app']['phone'] ?? '');
        $settings->set('app.website', $data['app']['website'] ?? '');
        $settings->set('app.address', $data['app']['address'] ?? '');
        $settings->set('app.description', $data['app']['description'] ?? '');

        $settings->set('branding.logo', $data['branding']['logo'] ?? '');
        $settings->set('branding.favicon', $data['branding']['favicon'] ?? '');
        $settings->set('branding.primary_color', $data['branding']['primary_color'] ?? '');
        $settings->set('branding.secondary_color', $data['branding']['secondary_color'] ?? '');
        $settings->set('branding.footer_text', $data['branding']['footer_text'] ?? '');
        $settings->set('branding.auth_image', $data['branding']['auth_image'] ?? '');
        $settings->set('branding.system_tagline', $data['branding']['system_tagline'] ?? '');

        $settings->set('regional.timezone', $data['regional']['timezone']);
        $settings->set('regional.locale', $data['regional']['locale']);
        $settings->set('regional.currency', $data['regional']['currency']);

        $settings->set('auth.allow_registration', $this->boolToString($data['auth']['allow_registration'] ?? false));
        $settings->set('auth.require_email_verification', $this->boolToString($data['auth']['require_email_verification'] ?? true));
        $settings->set('auth.require_admin_approval', $this->boolToString($data['auth']['require_admin_approval'] ?? false));
        $settings->set('auth.login_with_email_only', $this->boolToString($data['auth']['login_with_email_only'] ?? true));
        $settings->set('auth.password_min_length', (string) $data['auth']['password_min_length']);
        $settings->set('auth.password_require_letters', $this->boolToString($data['auth']['password_require_letters'] ?? true));
        $settings->set('auth.password_require_numbers', $this->boolToString($data['auth']['password_require_numbers'] ?? true));

        $settings->set('system.default_pagination', (string) $data['system']['default_pagination']);
        $settings->set('system.maintenance_mode', $this->boolToString($data['system']['maintenance_mode'] ?? false));
        $settings->set('system.maintenance_message', $data['system']['maintenance_message'] ?? '');
        $settings->set('system.enable_activity_log', $this->boolToString($data['system']['enable_activity_log'] ?? true));
        $settings->set('system.enable_system_errors', $this->boolToString($data['system']['enable_system_errors'] ?? true));
        $settings->set('system.enable_internal_notifications', $this->boolToString($data['system']['enable_internal_notifications'] ?? true));

        $settings->set('billing.enabled', $this->boolToString($data['billing']['enabled'] ?? true));
        $settings->set('billing.default_plan_id', $data['billing']['default_plan_id'] ?? '');
        $settings->set('billing.currency', $data['billing']['currency'] ?? '');
        $settings->set('billing.trial_days', (string) ($data['billing']['trial_days'] ?? 0));
        $settings->set('billing.allow_plan_changes', $this->boolToString($data['billing']['allow_plan_changes'] ?? true));
        $settings->set('billing.allow_cancellations', $this->boolToString($data['billing']['allow_cancellations'] ?? true));

        $settings->set('features.api_enabled', $this->boolToString($data['features']['api_enabled'] ?? true));
        $settings->set('features.webhooks_enabled', $this->boolToString($data['features']['webhooks_enabled'] ?? true));
        $settings->set('features.support_enabled', $this->boolToString($data['features']['support_enabled'] ?? true));
        $settings->set('features.help_center_enabled', $this->boolToString($data['features']['help_center_enabled'] ?? true));
        $settings->set('features.exports_enabled', $this->boolToString($data['features']['exports_enabled'] ?? true));
        $settings->set('features.member_billing_enabled', $this->boolToString($data['features']['member_billing_enabled'] ?? true));

        $settings->set('mail.from_name', $data['mail']['from_name'] ?? '');
        $settings->set('mail.from_address', $data['mail']['from_address'] ?? '');
        $settings->set('mail.reply_to_address', $data['mail']['reply_to_address'] ?? '');
        $settings->set('mail.footer_signature', $data['mail']['footer_signature'] ?? '');

        // compatibilidad con keys antiguas
        $settings->set('system.allow_registration', $this->boolToString($data['auth']['allow_registration'] ?? false));
        $settings->set('system.require_user_approval', $this->boolToString($data['auth']['require_admin_approval'] ?? false));

        $currentMaintenance = $this->toBool($data['system']['maintenance_mode'] ?? false);
        if ($previousMaintenance !== $currentMaintenance) {
            $activity->log($currentMaintenance ? 'maintenance_enabled' : 'maintenance_disabled', [
                'actor' => $request->user(),
                'description' => $currentMaintenance ? 'Modo mantenimiento activado' : 'Modo mantenimiento desactivado',
                'request' => $request,
            ]);
        }

        $activity->log('settings_updated', [
            'actor' => $request->user(),
            'description' => 'Settings actualizados',
            'request' => $request,
        ]);

        return back()->with('success', 'Configuracion actualizada correctamente.');
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

    private function boolToString(bool $value): string
    {
        return $value ? '1' : '0';
    }
}
