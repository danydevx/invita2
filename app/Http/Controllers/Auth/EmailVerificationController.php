<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\SendVerificationEmailJob;
use App\Notifications\WelcomeNotification;
use App\Services\ActivityService;
use App\Services\NotificationPreferenceService;
use App\Services\UserNotificationService;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class EmailVerificationController extends Controller
{
    public function notice()
    {
        if (request()->user()->hasVerifiedEmail()) {
            return redirect('/member');
        }

        return Inertia::render('Auth/VerifyEmail');
    }

    public function verify(EmailVerificationRequest $request, ActivityService $activity, UserNotificationService $notifications, NotificationPreferenceService $preferences)
    {
        $request->fulfill();

        $user = $request->user();
        $user->is_active = true;
        $user->save();

        $normalRole = Role::query()
            ->where('id', 10)
            ->orWhere('name', 'guest')
            ->first();

        $memberRole = Role::query()
            ->where('id', 3)
            ->orWhere('name', 'member')
            ->first();

        if ($normalRole) {
            $user->removeRole($normalRole);
        }

        if ($memberRole) {
            $user->assignRole($memberRole);
        }

        $user->listings()
            ->where('is_published', false)
            ->update(['is_published' => true]);

        $activity->log('user_verified', [
            'user' => $user,
            'actor' => $user,
            'subject' => $user,
            'description' => 'Email verificado',
            'request' => $request,
        ]);

        $notifications->create(
            $user,
            'product',
            'Email verificado',
            'Tu email fue verificado correctamente.',
            '/member'
        );

        if ($preferences->allows($user, 'product', 'email')) {
            $user->notify(new WelcomeNotification);
        }

        if ($request->session()->has('selected_plan_id')) {
            return redirect('/member/plan-selection')
                ->with('success', 'Email verificado correctamente.');
        }

        return redirect('/member')->with('success', 'Email verificado correctamente.');
    }

    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect('/member');
        }

        SendVerificationEmailJob::dispatch($request->user()->id);

        return back()->with('success', 'Enviamos un nuevo correo de verificacion.');
    }
}
