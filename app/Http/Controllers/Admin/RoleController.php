<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Spatie\Permission\Guard;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $sort = $request->input('sort', 'id');
        $direction = $request->input('direction', 'asc');

        $protectedRoleIds = [2, 3, 4];

        $sort = in_array($sort, ['id', 'name'], true) ? $sort : 'id';
        $direction = $direction === 'desc' ? 'desc' : 'asc';

        $roles = Role::query()
            ->withCount('users')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");

                    if (is_numeric($search)) {
                        $q->orWhere('id', (int) $search);
                    }
                });
            })
            ->orderBy($sort, $direction)
            ->paginate(10)
            ->withQueryString()
            ->through(fn ($role) => [
                'id' => $role->id,
                'name' => $role->name,
                'users_count' => $role->users_count,
                'protected' => in_array($role->id, $protectedRoleIds, true),
                'blocked' => (bool) $role->blocked,
            ]);

        return Inertia::render('Admin/Roles/Index', [
            'roles' => $roles,
            'filters' => [
                'search' => $search,
                'sort' => $sort,
                'direction' => $direction,
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Roles/Create');
    }

    public function store(Request $request)
    {
        $table = config('permission.table_names.roles');
        $guard = Guard::getDefaultName(Role::class);

        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique($table, 'name')->where('guard_name', $guard),
            ],
            'blocked' => ['nullable', 'boolean'],
        ]);

        $data['name'] = trim($data['name']);
        $data['blocked'] = (bool) ($data['blocked'] ?? false);

        Role::create($data);

        return redirect()->route('admin.roles.index');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::query()
            ->where('guard_name', $role->guard_name)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn ($permission) => [
                'id' => $permission->id,
                'label' => $permission->name,
            ]);

        return Inertia::render('Admin/Roles/Edit', [
            'role' => [
                'id' => $role->id,
                'name' => $role->name,
                'blocked' => (bool) $role->blocked,
            ],
            'permissions' => $permissions,
            'rolePermissions' => $role->permissions()->pluck('id')->all(),
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $table = config('permission.table_names.roles');
        $permissionsTable = config('permission.table_names.permissions');

        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique($table, 'name')
                    ->where('guard_name', $role->guard_name)
                    ->ignore($role->id),
            ],
            'blocked' => ['nullable', 'boolean'],
            'permissions' => ['array'],
            'permissions.*' => [
                'integer',
                Rule::exists($permissionsTable, 'id')->where('guard_name', $role->guard_name),
            ],
        ]);

        $role->update([
            'name' => trim($data['name']),
            'blocked' => (bool) ($data['blocked'] ?? false),
        ]);

        $permissionIds = $request->input('permissions', []);
        $permissions = Permission::query()
            ->whereIn('id', $permissionIds)
            ->where('guard_name', $role->guard_name)
            ->get();

        $role->syncPermissions($permissions);

        return redirect()->route('admin.roles.index');
    }

    public function destroy(Role $role)
    {
        if (in_array($role->id, [2, 3, 4], true)) {
            return back()->withErrors([
                'delete' => 'No se puede eliminar este rol porque es protegido.',
            ]);
        }

        if ($role->users()->exists()) {
            return back()->withErrors([
                'delete' => 'No se puede eliminar un rol que ya esta asignado a usuarios.',
            ]);
        }

        $role->delete();

        return redirect()->route('admin.roles.index');
    }
}
