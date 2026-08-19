<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use App\Services\UserNotificationService;
use App\Services\WebhookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class UserProfileController extends Controller
{
    public function edit(Request $request)
    {
        $user = $request->user();
        $profile = $user->profile;

        return Inertia::render('Admin/Profile/Edit', [
            'profile' => [
                'name' => $profile?->name ?? '',
                'phone' => $profile?->phone ?? '',
                'whatsapp' => $profile?->whatsapp ?? '',
                'whatsapp_country' => $profile?->whatsapp_country ?? '+1',
                'facebook' => $profile?->facebook ?? '',
                'instagram' => $profile?->instagram ?? '',
                'x' => $profile?->x ?? '',
                'personal_email' => $profile?->personal_email ?? '',
                'country' => $profile?->country ?? '',
                'avatar' => $profile?->avatar ?? '',
            ],
        ]);
    }

    public function editMember(Request $request)
    {
        $user = $request->user();
        $profile = $user->profile;

        return Inertia::render('Member/Profile/Edit', [
            'profile' => [
                'name' => $profile?->name ?? '',
                'phone' => $profile?->phone ?? '',
                'whatsapp' => $profile?->whatsapp ?? '',
                'whatsapp_country' => $profile?->whatsapp_country ?? '+52',
                'personal_email' => $profile?->personal_email ?? '',
                'country' => $profile?->country ?? 'MX',
                'avatar' => $profile?->avatar ?? '',
            ],
        ]);
    }

    public function update(Request $request, ActivityService $activity, UserNotificationService $notifications, WebhookService $webhooks)
    {
        $user = $request->user();

        $rules = [
            'name' => ['nullable', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:50'],
            'whatsapp' => ['nullable', 'string', 'max:20'],
            'whatsapp_country' => ['nullable', 'string', 'max:5'],
            'personal_email' => ['nullable', 'email', 'max:150'],
            'country' => ['nullable', 'string', 'max:5'],
        ];

        if ($request->hasFile('avatar')) {
            $rules['avatar'] = ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'];
        } elseif ($request->filled('avatar') && is_string($request->avatar)) {
            $rules['avatar'] = ['nullable', 'string', 'max:500'];
        }

        $data = $request->validate($rules);

        $profileData = [
            'name' => $data['name'] ?? null,
            'phone' => $data['phone'] ?? null,
            'whatsapp' => $data['whatsapp'] ?? null,
            'whatsapp_country' => $data['whatsapp_country'] ?? null,
            'personal_email' => $data['personal_email'] ?? null,
            'country' => $data['country'] ?? null,
        ];

        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $profileData['avatar'] = $avatarPath;
        } elseif ($request->filled('avatar_delete')) {
            $profileData['avatar'] = null;
        }

        $user->profile()->updateOrCreate([
            'user_id' => $user->id,
        ], $profileData);

        $activity->log('user_updated', [
            'user' => $user,
            'actor' => $user,
            'subject' => $user->profile,
            'description' => 'Perfil actualizado',
            'request' => $request,
        ]);

        $webhooks->dispatchUserEvent($user, 'user.updated', [
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ]);

        $notifications->create(
            $user,
            'product',
            'Perfil actualizado',
            'Tus datos de perfil fueron actualizados.',
            '/profile'
        );

        return back()->with('success', 'Perfil actualizado correctamente.');
    }
}
