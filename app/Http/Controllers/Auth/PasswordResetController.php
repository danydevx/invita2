<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\SendPasswordResetEmailJob;
use App\Models\PasswordResetRequest;
use App\Models\User;
use App\Notifications\PasswordChangedNotification;
use App\Services\ActivityService;
use App\Services\NotificationPreferenceService;
use App\Services\SecurityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class PasswordResetController extends Controller
{
    public function showForgotPassword()
    {
        return Inertia::render('Auth/ForgotPassword');
    }

    public function sendResetLink(Request $request, ActivityService $activity, SecurityService $security)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:150'],
        ]);

        $user = User::query()->where('email', $data['email'])->first();

        if ($user) {
            PasswordResetRequest::query()
                ->where('user_id', $user->id)
                ->whereNull('used_at')
                ->delete();

            $token = Str::random(64);
            $code = str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);

            PasswordResetRequest::create([
                'user_id' => $user->id,
                'token_hash' => hash('sha256', $token),
                'code_hash' => Hash::make($code),
                'expires_at' => now()->addMinutes(30),
            ]);

            SendPasswordResetEmailJob::dispatch($user->id, $token, $code);

            $activity->log('user_password_reset_requested', [
                'user' => $user,
                'actor' => $user,
                'subject' => $user,
                'description' => 'Solicitud de reset password',
                'request' => $request,
            ]);

            $security->log('password_reset_requested', $user, $request, 'Solicitud de reset password');
        }

        return back()->with('success', 'Si el correo existe en el sistema, te enviamos instrucciones para restablecer tu password.');
    }

    public function showVerifyCode(string $token)
    {
        $reset = $this->getValidReset($token);

        if (! $reset) {
            return redirect('/forgot-password')->with('error', 'El enlace de recuperacion no es valido o ya expiro.');
        }

        return Inertia::render('Auth/VerifyResetCode', [
            'token' => $token,
        ]);
    }

    public function verifyCode(Request $request, string $token)
    {
        $reset = $this->getValidReset($token);

        if (! $reset) {
            return redirect('/forgot-password')->with('error', 'El enlace de recuperacion no es valido o ya expiro.');
        }

        $data = $request->validate([
            'code' => ['required', 'digits:4'],
        ]);

        if (! Hash::check($data['code'], $reset->code_hash)) {
            throw ValidationException::withMessages([
                'code' => 'El codigo no es valido.',
            ]);
        }

        $reset->update([
            'verified_at' => now(),
        ]);

        return redirect('/reset-password/'.$token.'/new-password');
    }

    public function showResetPasswordForm(string $token)
    {
        $reset = $this->getVerifiedReset($token);

        if (! $reset) {
            return redirect('/forgot-password')->with('error', 'El enlace de recuperacion no es valido o ya expiro.');
        }

        return Inertia::render('Auth/ResetPassword', [
            'token' => $token,
        ]);
    }

    public function resetPassword(Request $request, string $token, ActivityService $activity, SecurityService $security, NotificationPreferenceService $preferences)
    {
        $reset = $this->getVerifiedReset($token);

        if (! $reset) {
            return redirect('/forgot-password')->with('error', 'El enlace de recuperacion no es valido o ya expiro.');
        }

        $data = $request->validate([
            'password' => ['required', 'string', 'min:8', 'regex:/^(?=.*[A-Za-z])(?=.*\d).+$/', 'confirmed'],
        ], [
            'password.regex' => 'Minimo 8 caracteres, con letras y numeros.',
        ]);

        $reset->user->update([
            'password' => Hash::make($data['password']),
        ]);

        $reset->update([
            'used_at' => now(),
        ]);

        $activity->log('user_password_reset_completed', [
            'user' => $reset->user,
            'actor' => $reset->user,
            'subject' => $reset->user,
            'description' => 'Reset password completado',
            'request' => $request,
        ]);

        $security->log('password_reset_completed', $reset->user, $request, 'Reset password completado');

        if ($preferences->allows($reset->user, 'security', 'email')) {
            $reset->user->notify(new PasswordChangedNotification);
        }

        return redirect('/login')->with('success', 'Tu password fue actualizada correctamente. Ya puedes iniciar sesion.');
    }

    private function getValidReset(string $token): ?PasswordResetRequest
    {
        $tokenHash = hash('sha256', $token);

        return PasswordResetRequest::query()
            ->where('token_hash', $tokenHash)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->first();
    }

    private function getVerifiedReset(string $token): ?PasswordResetRequest
    {
        $tokenHash = hash('sha256', $token);

        return PasswordResetRequest::query()
            ->where('token_hash', $tokenHash)
            ->whereNotNull('verified_at')
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->first();
    }
}
