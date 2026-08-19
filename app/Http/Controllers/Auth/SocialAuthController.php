<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserProfile;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Models\Role;

class SocialAuthController extends Controller
{
    public function redirectToProvider(string $provider)
    {
        $validated = $this->validateProvider($provider);

        if (! $validated) {
            return redirect('/register')->with('error', 'Proveedor de autenticación no válido.');
        }

        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback(string $provider, ActivityService $activity)
    {
        $validated = $this->validateProvider($provider);

        if (! $validated) {
            return redirect('/register')->with('error', 'Proveedor de autenticación no válido.');
        }

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect('/register')->with('error', 'No se pudo autenticar con ' . ucfirst($provider) . '.');
        }

        $user = $this->findOrCreateUser($provider, $socialUser);

        if (! $user) {
            return redirect('/register')->with('error', 'Esta cuenta ha sido eliminada. Contacta al administrador.');
        }

        Auth::login($user, true);

        $activity->log('user_social_login', [
            'user' => $user,
            'actor' => $user,
            'subject' => $user,
            'description' => 'Usuario autenticado con ' . ucfirst($provider),
        ]);

        if (! $user->hasVerifiedEmail()) {
            return redirect('/email/verify')->with('success', 'Registro exitoso. Revisa tu correo para verificar tu cuenta.');
        }

        return redirect()->intended('/dashboard');
    }

    protected function validateProvider(string $provider): bool
    {
        return in_array($provider, ['google', 'facebook']);
    }

    protected function findOrCreateUser(string $provider, $socialUser): ?User
    {
        $email = strtolower(trim($socialUser->getEmail()));

        if (! $email) {
            return null;
        }

        $user = User::where('email', $email)->withTrashed()->first();

        if ($user && $user->deleted_at) {
            return null;
        }

        if ($user) {
            return $user;
        }

        $name = $socialUser->getName() ?? $socialUser->getNickname() ?? 'Usuario';

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make(Str::random(24)),
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        UserProfile::create([
            'user_id' => $user->id,
            'name' => $name,
        ]);

        $role = Role::query()
            ->where('id', 10)
            ->orWhere('name', 'guest')
            ->first();

        if ($role) {
            $user->syncRoles([$role]);
        }

        return $user;
    }
}
