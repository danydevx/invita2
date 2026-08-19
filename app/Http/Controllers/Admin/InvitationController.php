<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\Plan;
use App\Notifications\InvitationNotification;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class InvitationController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $status = $request->input('status', '');

        $invitations = Invitation::query()
            ->with(['invitedBy:id,name,email'])
            ->when($search !== '', fn ($query) => $query->where('email', 'like', "%{$search}%"))
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString()
            ->through(fn ($invitation) => [
                'id' => $invitation->id,
                'email' => $invitation->email,
                'status' => $invitation->status,
                'expires_at' => $invitation->expires_at?->toDateTimeString(),
                'created_at' => $invitation->created_at?->toDateTimeString(),
                'invited_by' => $invitation->invitedBy ? [
                    'id' => $invitation->invitedBy->id,
                    'name' => $invitation->invitedBy->name,
                    'email' => $invitation->invitedBy->email,
                ] : null,
            ]);

        return Inertia::render('Admin/Invitations/Index', [
            'invitations' => $invitations,
            'filters' => [
                'search' => $search,
                'status' => $status,
            ],
        ]);
    }

    public function create()
    {
        $plans = Plan::query()->orderBy('name')->get(['id', 'name']);

        return Inertia::render('Admin/Invitations/Create', [
            'plans' => $plans,
        ]);
    }

    public function store(Request $request, ActivityService $activity)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:150'],
            'role_name' => ['nullable', 'string', 'max:100'],
            'expires_at' => ['nullable', 'date', 'after:now'],
            'message' => ['nullable', 'string', 'max:500'],
            'redirect_to' => ['nullable', 'string', 'max:255'],
            'default_plan_id' => ['nullable', 'exists:plans,id'],
            'feature_flag' => ['nullable', 'string', 'max:150'],
        ]);

        $existing = Invitation::query()
            ->where('email', strtolower($data['email']))
            ->where('status', 'pending')
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->first();

        if ($existing) {
            return back()->withErrors(['email' => 'Ya existe una invitacion activa para este email.']);
        }

        $plainToken = Str::random(64);
        $tokenHash = hash('sha256', $plainToken);

        $invitation = Invitation::create([
            'email' => strtolower($data['email']),
            'token' => $tokenHash,
            'invited_by_user_id' => $request->user()->id,
            'role_name' => $data['role_name'] ?? null,
            'status' => 'pending',
            'expires_at' => $data['expires_at'] ?? null,
            'metadata' => [
                'message' => $data['message'] ?? null,
                'redirect_to' => $data['redirect_to'] ?? null,
                'default_plan_id' => $data['default_plan_id'] ?? null,
                'feature_flag' => $data['feature_flag'] ?? null,
            ],
        ]);

        $invitation->notify(new InvitationNotification($invitation, $plainToken));

        $activity->log('invitation_created', [
            'actor' => $request->user(),
            'subject' => $invitation,
            'description' => 'Invitacion creada',
            'request' => $request,
        ]);

        return redirect()->route('admin.invitations.index')->with('success', 'Invitacion creada correctamente.');
    }

    public function show(Invitation $invitation)
    {
        $invitation->load(['invitedBy:id,name,email']);

        return Inertia::render('Admin/Invitations/Show', [
            'invitation' => [
                'id' => $invitation->id,
                'email' => $invitation->email,
                'role_name' => $invitation->role_name,
                'status' => $invitation->status,
                'expires_at' => $invitation->expires_at?->toDateTimeString(),
                'accepted_at' => $invitation->accepted_at?->toDateTimeString(),
                'revoked_at' => $invitation->revoked_at?->toDateTimeString(),
                'metadata' => $invitation->metadata,
                'created_at' => $invitation->created_at?->toDateTimeString(),
                'invited_by' => $invitation->invitedBy ? [
                    'id' => $invitation->invitedBy->id,
                    'name' => $invitation->invitedBy->name,
                    'email' => $invitation->invitedBy->email,
                ] : null,
            ],
        ]);
    }

    public function revoke(Request $request, Invitation $invitation, ActivityService $activity)
    {
        if ($invitation->status === 'accepted') {
            return back()->with('error', 'La invitacion ya fue utilizada.');
        }

        $invitation->update([
            'status' => 'revoked',
            'revoked_at' => now(),
        ]);

        $activity->log('invitation_revoked', [
            'actor' => $request->user(),
            'subject' => $invitation,
            'description' => 'Invitacion revocada',
            'request' => $request,
        ]);

        return back()->with('success', 'La invitacion fue revocada.');
    }

    public function resend(Request $request, Invitation $invitation, ActivityService $activity)
    {
        if ($invitation->status === 'accepted') {
            return back()->with('error', 'La invitacion ya fue utilizada.');
        }

        $plainToken = Str::random(64);
        $tokenHash = hash('sha256', $plainToken);

        $invitation->update([
            'token' => $tokenHash,
            'status' => 'pending',
        ]);

        $invitation->notify(new InvitationNotification($invitation, $plainToken));

        $activity->log('invitation_resent', [
            'actor' => $request->user(),
            'subject' => $invitation,
            'description' => 'Invitacion reenviada',
            'request' => $request,
        ]);

        return back()->with('success', 'La invitacion fue enviada correctamente.');
    }
}
