<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\SendVerificationEmailJob;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UserProfile;
use App\Services\ActivityService;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class RegisterController extends Controller
{
    public function showRegister(Request $request)
    {
        if (! $this->allowRegistration()) {
            return redirect('/login')->with('error', 'El registro esta deshabilitado.');
        }

        return view('auth.register', [
            'title' => 'Registro',
            'prefill' => [
                'email' => (string) $request->query('email', ''),
                'invite' => (string) $request->query('invite', ''),
            ],
            'formStartedAt' => now()->timestamp,
        ]);
    }

    public function register(Request $request, ActivityService $activity)
    {
        if (! $this->allowRegistration()) {
            return back()->withErrors([
                'register' => 'El registro esta deshabilitado.',
            ]);
        }

        $email = strtolower(trim($request->input('email', '')));
        $deletedUser = User::onlyTrashed()->where('email', $email)->first();

        if ($deletedUser) {
            return back()->withErrors([
                'email' => 'Este correo pertenece a una cuenta eliminada. Contacta al administrador para restaurarla.',
            ])->withInput();
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'regex:/^(?=.*[A-Za-z])(?=.*\d).+$/', 'confirmed'],
            'form_started_at' => ['required', 'integer', 'min:0'],
        ], [
            'password.regex' => 'Minimo 8 caracteres, con letras y numeros.',
            'email.unique' => 'Este correo ya esta registrado.',
        ]);

        if (now()->timestamp - (int) $data['form_started_at'] < 3) {
            throw ValidationException::withMessages([
                'register' => 'No se pudo completar el registro.',
            ]);
        }

        $user = User::create([
            'name' => trim($data['name']),
            'email' => strtolower(trim($data['email'])),
            'password' => Hash::make($data['password']),
            'is_active' => true,
            'email_verified_at' => null,
        ]);

        SendVerificationEmailJob::dispatch($user->id);

        UserProfile::create([
            'user_id' => $user->id,
            'name' => $user->name,
        ]);

        $activity->log('user_registered', [
            'user' => $user,
            'actor' => $user,
            'subject' => $user,
            'description' => 'Usuario registrado',
            'request' => $request,
        ]);

        $role = Role::query()
            ->where('name', 'member')
            ->first();

        if ($role) {
            $user->syncRoles([$role]);
        }

        $freePlan = Plan::where('slug', 'free')->where('is_active', true)->first();
        if ($freePlan) {
            Subscription::create([
                'user_id' => $user->id,
                'plan_id' => $freePlan->id,
                'status' => 'active',
                'starts_at' => now(),
                'ends_at' => null,
            ]);
        }

        Auth::login($user);

        return redirect('/member/dashboard')->with('success', 'Registro exitoso. Bienvenido!');
    }

    private function allowRegistration(): bool
    {
        $settings = app(SettingService::class);
        $value = $settings->get('auth.allow_registration');
        if ($value === null || $value === '') {
            $value = $settings->get('system.allow_registration');
        }

        if ($value === null) {
            return true;
        }

        return in_array((string) $value, ['1', 'true', 'on', 'yes'], true);
    }

    private function requireUserApproval(): bool
    {
        $settings = app(SettingService::class);
        $value = $settings->get('auth.require_admin_approval');
        if ($value === null || $value === '') {
            $value = $settings->get('system.require_user_approval');
        }

        if ($value === null) {
            return false;
        }

        return in_array((string) $value, ['1', 'true', 'on', 'yes'], true);
    }
}
