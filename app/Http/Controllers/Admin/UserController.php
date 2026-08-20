<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendVerificationEmailJob;
use App\Models\Plan;
use App\Models\User;
use App\Services\ActivityService;
use App\Services\UserNotificationService;
use App\Services\WebhookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $status = $request->input('status', '');
        $verified = $request->input('verified', '');
        $roleId = $request->input('role', '');

        $users = User::query()
            ->with(['roles:id,name', 'profile:id,user_id,phone'])
            ->when($search !== '', function ($query) use ($search) {
                $needle = mb_strtolower($search);
                $query->where(function ($q) use ($search) {
                    $q->whereRaw('LOWER(name) like ?', ['%'.mb_strtolower($search).'%'])
                        ->orWhereRaw('LOWER(email) like ?', ['%'.mb_strtolower($search).'%']);

                    if (is_numeric($search)) {
                        $q->orWhere('id', (int) $search);
                    }
                });
            })
            ->when($status !== '', function ($query) use ($status) {
                if ($status === 'active') {
                    $query->where('is_active', true);
                }
                if ($status === 'inactive') {
                    $query->where('is_active', false);
                }
            })
            ->when($verified !== '', function ($query) use ($verified) {
                if ($verified === 'verified') {
                    $query->whereNotNull('email_verified_at');
                }
                if ($verified === 'unverified') {
                    $query->whereNull('email_verified_at');
                }
            })
            ->when($roleId !== '', function ($query) use ($roleId) {
                $query->whereHas('roles', function ($q) use ($roleId) {
                    $q->where('id', $roleId);
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString()
            ->through(fn ($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->profile?->phone,
                'roles' => $user->roles->pluck('name')->values(),
                'is_active' => (bool) $user->is_active,
                'email_verified_at' => $user->email_verified_at,
                'created_at' => $user->created_at?->toDateString(),
            ]);

        $roles = Role::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn ($role) => [
                'id' => $role->id,
                'label' => $role->name,
            ]);

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'roles' => $roles,
            'filters' => [
                'search' => $search,
                'status' => $status,
                'verified' => $verified,
                'role' => $roleId,
            ],
        ]);
    }

    public function create()
    {
        $roles = Role::query()
            ->where('blocked', false)
            ->where('name', '!=', 'guest')
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn ($role) => [
                'id' => $role->id,
                'label' => $role->name,
            ]);

        $plans = Plan::query()
            ->where('is_active', true)
            ->orderByRaw('sort_order is null, sort_order asc')
            ->orderBy('id')
            ->get(['id', 'name'])
            ->map(fn ($plan) => [
                'id' => $plan->id,
                'label' => $plan->name,
            ]);

        return Inertia::render('Admin/Users/Create', [
            'roles' => $roles,
        ]);
    }

    public function store(Request $request, ActivityService $activity)
    {
        $rolesTable = config('permission.table_names.roles');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'max:30', 'regex:/^(?=.*[A-Za-z])(?=.*\d).+$/', 'confirmed'],
            'is_active' => ['boolean'],
            'roles' => ['array'],
            'roles.*' => [
                'integer',
                Rule::exists($rolesTable, 'id')->where(fn ($query) => $query
                    ->where('blocked', false)
                    ->where('name', '!=', 'guest')),
            ],
        ], [
            'password.regex' => 'Minimo 8 caracteres, con letras y numeros.',
        ]);

        $roles = Role::query()->whereIn('id', $data['roles'] ?? [])->get();
        $names = $roles->pluck('name')->all();
        if (in_array('superadmin', $names, true) && ! $request->user()->hasRole('superadmin')) {
            return back()->withErrors([
                'roles' => 'Solo un superadmin puede asignar ese rol.',
            ]);
        }

        $user = User::create([
            'name' => trim($data['name']),
            'email' => strtolower(trim($data['email'])),
            'password' => Hash::make($data['password']),
            'is_active' => (bool) ($data['is_active'] ?? true),
        ]);

        $user->profile()->create();

        $activity->log('user_created', [
            'user' => $user,
            'actor' => $request->user(),
            'subject' => $user,
            'description' => 'Usuario creado por admin',
            'request' => $request,
        ]);

        $user->syncRoles($roles);

        return redirect()->route('admin.users.index');
    }

    public function edit(User $user)
    {
        $user->load('profile');

        $roles = Role::query()
            ->where('blocked', false)
            ->where('name', '!=', 'guest')
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn ($role) => [
                'id' => $role->id,
                'label' => $role->name,
            ]);

        return Inertia::render('Admin/Users/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->profile?->phone,
                'is_active' => (bool) $user->is_active,
                'email_verified_at' => $user->email_verified_at,
            ],
            'roles' => $roles,
            'userRoles' => $user->roles()->pluck('id')->all(),
        ]);
    }

    public function update(Request $request, User $user, ActivityService $activity, UserNotificationService $notifications, WebhookService $webhooks)
    {
        $rolesTable = config('permission.table_names.roles');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password' => ['nullable', 'string', 'min:8', 'max:30', 'regex:/^(?=.*[A-Za-z])(?=.*\d).+$/', 'confirmed'],
            'is_active' => ['boolean'],
            'roles' => ['array'],
            'roles.*' => [
                'integer',
                Rule::exists($rolesTable, 'id')->where(fn ($query) => $query
                    ->where('blocked', false)
                    ->where('name', '!=', 'guest')),
            ],
        ], [
            'password.regex' => 'Minimo 8 caracteres, con letras y numeros.',
        ]);

        $rolesToAssign = Role::query()->whereIn('id', $data['roles'] ?? [])->get(['name']);
        $names = $rolesToAssign->pluck('name')->all();
        if (in_array('superadmin', $names, true) && ! $request->user()->hasRole('superadmin')) {
            return back()->withErrors([
                'roles' => 'Solo un superadmin puede asignar ese rol.',
            ]);
        }

        $user->update([
            'name' => trim($data['name']),
            'email' => strtolower(trim($data['email'])),
            'is_active' => (bool) ($data['is_active'] ?? $user->is_active),
        ]);

        if (! empty($data['password'])) {
            $user->update([
                'password' => Hash::make($data['password']),
            ]);
        }

        $roles = Role::query()->whereIn('id', $data['roles'] ?? [])->get();
        $previousRoles = $user->roles()->pluck('name')->values()->toArray();
        $user->syncRoles($roles);
        $currentRoles = $user->roles()->pluck('name')->values()->toArray();

        if ($previousRoles !== $currentRoles) {
            $activity->log('user_role_changed', [
                'user' => $user,
                'actor' => $request->user(),
                'subject' => $user,
                'description' => 'Cambio de roles',
                'metadata' => [
                    'from' => $previousRoles,
                    'to' => $currentRoles,
                ],
                'request' => $request,
            ]);
        }

        $webhooks->dispatchUserEvent($user, 'user.updated', [
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ]);

        return redirect()->route('admin.users.index');
    }

    public function destroy(User $user, ActivityService $activity)
    {
        if ($user->id === 1) {
            return back()->withErrors([
                'delete' => 'No se puede eliminar este usuario.',
            ]);
        }

        if ($user->deleted_at) {
            return back()->withErrors([
                'delete' => 'Este usuario ya esta eliminado.',
            ]);
        }

        $userId = $user->id;
        $userName = $user->name;
        $actor = request()->user();

        $user->delete();

        $activity->log('user_archived', [
            'user' => null,
            'actor' => $actor,
            'subject' => null,
            'description' => "Usuario archivado por admin: {$userName} (ID: {$userId})",
            'request' => request(),
        ]);

        return redirect()->route('admin.users.index');
    }

    public function activate(User $user, ActivityService $activity, UserNotificationService $notifications)
    {
        if ($user->id === 1) {
            return back()->withErrors([
                'activate' => 'No se puede modificar este usuario.',
            ]);
        }

        $user->update(['is_active' => true]);

        $activity->log('user_activated', [
            'user' => $user,
            'actor' => request()->user(),
            'subject' => $user,
            'description' => 'Usuario activado',
            'request' => request(),
        ]);

        $notifications->create(
            $user,
            'product',
            'Cuenta activada',
            'Tu cuenta fue activada por un administrador.',
            '/member'
        );

        return back()->with('success', 'Usuario activado correctamente.');
    }

    public function deactivate(User $user, ActivityService $activity, UserNotificationService $notifications)
    {
        if ($user->id === 1) {
            return back()->withErrors([
                'deactivate' => 'No se puede modificar este usuario.',
            ]);
        }

        $user->update(['is_active' => false]);

        $activity->log('user_deactivated', [
            'user' => $user,
            'actor' => request()->user(),
            'subject' => $user,
            'description' => 'Usuario desactivado',
            'request' => request(),
        ]);

        $notifications->create(
            $user,
            'product',
            'Cuenta desactivada',
            'Tu cuenta fue desactivada por un administrador.',
            '/member'
        );

        return back()->with('success', 'Usuario desactivado correctamente.');
    }

    public function verifyEmail(User $user, ActivityService $activity, UserNotificationService $notifications)
    {
        if ($user->hasVerifiedEmail()) {
            return back()->with('error', 'El usuario ya tiene email verificado.');
        }

        $user->markEmailAsVerified();

        $activity->log('user_verified_manually', [
            'user' => $user,
            'actor' => request()->user(),
            'subject' => $user,
            'description' => 'Email verificado manualmente por admin',
            'request' => request(),
        ]);

        $notifications->create(
            $user,
            'product',
            'Email verificado',
            'Tu email fue verificado manualmente por un administrador.',
            '/member'
        );

        return back()->with('success', 'Usuario verificado correctamente.');
    }

    public function resendVerification(User $user, ActivityService $activity)
    {
        if ($user->hasVerifiedEmail()) {
            return back()->with('error', 'El usuario ya tiene email verificado.');
        }

        SendVerificationEmailJob::dispatch($user->id);

        $activity->log('user_verification_resent', [
            'user' => $user,
            'actor' => request()->user(),
            'subject' => $user,
            'description' => 'Reenvio de verificacion',
            'request' => request(),
        ]);

        return back()->with('success', 'Correo de verificacion enviado.');
    }

    public function archived(Request $request)
    {
        $search = trim((string) $request->input('search', ''));

        $users = User::onlyTrashed()
            ->with(['roles:id,name', 'profile:id,user_id,phone'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->whereRaw('LOWER(name) like ?', ['%'.mb_strtolower($search).'%'])
                        ->orWhereRaw('LOWER(email) like ?', ['%'.mb_strtolower($search).'%']);

                    if (is_numeric($search)) {
                        $q->orWhere('id', (int) $search);
                    }
                });
            })
            ->orderBy('deleted_at', 'desc')
            ->paginate(10)
            ->withQueryString()
            ->through(fn ($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->profile?->phone,
                'roles' => $user->roles->pluck('name')->values(),
                'is_active' => (bool) $user->is_active,
                'email_verified_at' => $user->email_verified_at,
                'created_at' => $user->created_at?->toDateString(),
                'deleted_at' => $user->deleted_at?->toDateString(),
            ]);

        return Inertia::render('Admin/Users/Archived', [
            'users' => $users,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function restore(int $id, ActivityService $activity)
    {
        $user = User::withTrashed()->findOrFail($id);

        if (! $user->deleted_at) {
            return back()->withErrors([
                'restore' => 'Este usuario no esta eliminado.',
            ]);
        }

        $userName = $user->name;
        $user->restore();

        $activity->log('user_restored', [
            'user' => null,
            'actor' => request()->user(),
            'subject' => null,
            'description' => "Usuario restaurado por admin: {$userName} (ID: {$user->id})",
            'request' => request(),
        ]);

        return redirect()->route('admin.users.archived')->with('success', 'Usuario restaurado correctamente.');
    }

    public function forceDestroy(int $id, ActivityService $activity)
    {
        $user = User::withTrashed()->findOrFail($id);

        if (! $user->deleted_at) {
            return back()->withErrors([
                'force_delete' => 'Solo se pueden eliminar permanentemente usuarios archivados.',
            ]);
        }

        $userId = $user->id;
        $userName = $user->name;
        $actor = request()->user();

        $user->forceDeleteWithRelations();

        $activity->log('user_force_deleted', [
            'user' => null,
            'actor' => $actor,
            'subject' => null,
            'description' => "Usuario eliminado permanentemente por admin: {$userName} (ID: {$userId})",
            'request' => request(),
        ]);

        return redirect()->route('admin.users.archived')->with('success', 'Usuario y todos sus datos han sido eliminados permanentemente.');
    }
}
