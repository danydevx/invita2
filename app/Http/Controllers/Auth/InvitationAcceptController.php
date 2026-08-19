<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class InvitationAcceptController extends Controller
{
    public function show(Request $request, string $token)
    {
        $invitation = $this->resolveInvitation($token);
        if (! $invitation) {
            return Inertia::render('Auth/Invitation/Accept', [
                'status' => 'invalid',
            ]);
        }

        if ($invitation->isExpired()) {
            $invitation->update(['status' => 'expired']);

            return Inertia::render('Auth/Invitation/Accept', [
                'status' => 'expired',
            ]);
        }

        if ($invitation->status === 'revoked') {
            return Inertia::render('Auth/Invitation/Accept', [
                'status' => 'revoked',
            ]);
        }

        if ($invitation->status === 'accepted') {
            return Inertia::render('Auth/Invitation/Accept', [
                'status' => 'accepted',
            ]);
        }

        return Inertia::render('Auth/Invitation/Accept', [
            'status' => 'pending',
            'email' => $invitation->email,
            'token' => $token,
        ]);
    }

    public function accept(Request $request, string $token, ActivityService $activity)
    {
        $invitation = $this->resolveInvitation($token);
        if (! $invitation) {
            return redirect('/login')->with('error', 'Invitacion invalida.');
        }

        if ($invitation->isExpired()) {
            $invitation->update(['status' => 'expired']);

            return redirect('/login')->with('error', 'La invitacion ha expirado.');
        }

        if ($invitation->status === 'revoked') {
            return redirect('/login')->with('error', 'La invitacion fue revocada.');
        }

        if ($invitation->status === 'accepted') {
            return redirect('/login')->with('error', 'La invitacion ya fue utilizada.');
        }

        $user = $request->user();
        if (! $user) {
            return redirect('/login')->with('error', 'Debes iniciar sesion para aceptar la invitacion.');
        }

        if (strtolower($user->email) !== strtolower($invitation->email)) {
            return redirect('/login')->with('error', 'La invitacion no corresponde a este usuario.');
        }

        $invitation->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        if ($invitation->role_name) {
            $role = Role::query()->where('name', $invitation->role_name)->first();
            if ($role && ! in_array($role->name, ['admin', 'superadmin'], true)) {
                $user->assignRole($role);
            }
        }

        $activity->log('invitation_accepted', [
            'actor' => $user,
            'subject' => $invitation,
            'description' => 'Invitacion aceptada',
            'request' => $request,
        ]);

        return redirect('/member')->with('success', 'Invitacion aceptada correctamente.');
    }

    private function resolveInvitation(string $token): ?Invitation
    {
        $tokenHash = hash('sha256', $token);

        return Invitation::query()
            ->where('token', $tokenHash)
            ->first();
    }
}
