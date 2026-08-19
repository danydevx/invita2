<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\UserNotificationPreference;
use App\Services\ActivityService;
use App\Services\NotificationPreferenceService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class NotificationPreferenceController extends Controller
{
    public function edit(Request $request, NotificationPreferenceService $preferences)
    {
        return Inertia::render('Member/NotificationPreferences/Edit', [
            'categories' => $preferences->forUser($request->user()),
        ]);
    }

    public function update(Request $request, NotificationPreferenceService $preferences, ActivityService $activity)
    {
        $allowed = $preferences->allowedCategories();

        $data = $request->validate([
            'preferences' => ['required', 'array'],
            'preferences.*.category' => ['required', Rule::in($allowed)],
            'preferences.*.in_app_enabled' => ['boolean'],
            'preferences.*.email_enabled' => ['boolean'],
        ]);

        $user = $request->user();
        $items = collect($data['preferences'])
            ->keyBy('category');

        $overrides = false;

        foreach ($allowed as $category) {
            $defaults = $preferences->defaultsFor($category);
            $payload = $items->get($category, [
                'in_app_enabled' => $defaults['in_app'],
                'email_enabled' => $defaults['email'],
            ]);

            $inApp = (bool) ($payload['in_app_enabled'] ?? $defaults['in_app']);
            $email = (bool) ($payload['email_enabled'] ?? $defaults['email']);

            if ($preferences->isRequiredChannel($category, 'in_app')) {
                if (! $inApp) {
                    $overrides = true;
                }
                $inApp = true;
            }
            if ($preferences->isRequiredChannel($category, 'email')) {
                if (! $email) {
                    $overrides = true;
                }
                $email = true;
            }

            UserNotificationPreference::updateOrCreate([
                'user_id' => $user->id,
                'category' => $category,
            ], [
                'in_app_enabled' => $inApp,
                'email_enabled' => $email,
            ]);
        }

        $activity->log('notification_preferences_updated', [
            'user' => $user,
            'actor' => $user,
            'subject' => $user,
            'description' => 'Preferencias de notificacion actualizadas',
            'request' => $request,
        ]);

        $message = $overrides
            ? 'Algunas notificaciones de seguridad no pueden desactivarse.'
            : 'Preferencias de notificacion actualizadas correctamente.';

        return back()->with('success', $message);
    }
}
