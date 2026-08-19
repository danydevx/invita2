<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use App\Services\UserPreferenceService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PreferenceController extends Controller
{
    public function edit(Request $request, UserPreferenceService $preferences)
    {
        return Inertia::render('Member/Preferences/Edit', [
            'preferences' => $preferences->forUser($request->user()),
        ]);
    }

    public function update(Request $request, UserPreferenceService $preferences, ActivityService $activity)
    {
        $data = $request->validate([
            'locale' => ['nullable', 'string', 'max:10'],
            'timezone' => ['nullable', 'string', 'max:100'],
            'email_notifications' => ['boolean'],
            'system_notifications' => ['boolean'],
            'dashboard_welcome_dismissed' => ['boolean'],
        ]);

        $user = $request->user();
        $profile = $user->profile;

        if (! $profile) {
            $profile = $user->profile()->create([
                'name' => $user->name,
            ]);
        }

        $current = $profile->preferences ?? [];
        $defaults = $preferences->defaults();

        $profile->update([
            'preferences' => array_merge($defaults, $current, $data),
        ]);

        $activity->log('preference_updated', [
            'user' => $user,
            'actor' => $user,
            'subject' => $profile,
            'description' => 'Preferencias actualizadas',
            'request' => $request,
        ]);

        return back()->with('success', 'Preferencias actualizadas correctamente.');
    }
}
