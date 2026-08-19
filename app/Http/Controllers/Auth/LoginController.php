<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use App\Services\SecurityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLogin()
    {
        return view('auth.login', [
            'title' => 'Iniciar sesion',
        ]);
    }

    public function store(Request $request, ActivityService $activity, SecurityService $security)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        $user = \App\Models\User::where('email', $credentials['email'])->withTrashed()->first();

        if ($user && $user->deleted_at) {
            $security->log('login_failed', null, $request, 'Intento de login en cuenta eliminada', [
                'email' => strtolower((string) $credentials['email']),
            ]);
            throw ValidationException::withMessages([
                'email' => 'Esta cuenta ha sido eliminada.',
            ]);
        }

        if (! Auth::attempt($credentials, $remember)) {
            $security->log('login_failed', null, $request, 'Intento de login fallido', [
                'email' => strtolower((string) $credentials['email']),
            ]);
            throw ValidationException::withMessages([
                'email' => 'Las credenciales no coinciden con nuestros registros.',
            ]);
        }

        $request->session()->regenerate();

        $user = $request->user();

        if (! $user->hasVerifiedEmail()) {
            return redirect('/email/verify')->with('error', 'Debes verificar tu email para continuar.');
        }

        if (! $user->is_active) {
            Auth::logout();

            return redirect('/login')->with('error', 'Tu cuenta esta inactiva.');
        }

        $activity->log('user_login', [
            'user' => $user,
            'actor' => $user,
            'subject' => $user,
            'description' => 'Inicio de sesion',
            'request' => $request,
        ]);

        $security->log('login_success', $user, $request, 'Login exitoso');

        if ($user->hasAnyRole(['admin', 'superadmin'])) {
            return redirect()->intended('/admin/dashboard');
        }

        if ($user->hasRole('member')) {
            if ($request->session()->has('selected_plan_id')) {
                return redirect('/member/plan-selection');
            }

            return redirect()->intended(route('member.dashboard'));
        }

        return redirect('/login')->with('error', 'Tu cuenta aun no esta habilitada.');
    }
}
