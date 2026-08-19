<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Spatie\Permission\Guard;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $sort = $request->input('sort', 'order');
        $direction = $request->input('direction', 'asc');

        $sort = in_array($sort, ['order', 'id', 'name'], true) ? $sort : 'order';
        $direction = $direction === 'desc' ? 'desc' : 'asc';

        $permissions = Permission::query()
            ->withCount(['roles', 'users'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");

                    if (is_numeric($search)) {
                        $q->orWhere('id', (int) $search);
                    }
                });
            })
            ->orderBy($sort, $direction)
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString()
            ->through(fn ($permission) => [
                'id' => $permission->id,
                'name' => $permission->name,
                'order' => $permission->order,
                'roles_count' => $permission->roles_count,
                'users_count' => $permission->users_count,
            ]);

        return Inertia::render('Admin/Permissions/Index', [
            'permissions' => $permissions,
            'filters' => [
                'search' => $search,
                'sort' => $sort,
                'direction' => $direction,
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Permissions/Create');
    }

    public function store(Request $request)
    {
        $table = config('permission.table_names.permissions');
        $guard = Guard::getDefaultName(Permission::class);

        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique($table, 'name')->where('guard_name', $guard),
            ],
            'description' => ['required', 'string', 'max:255'],
            'order' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['name'] = trim($data['name']);
        $data['description'] = trim($data['description']);
        $data['order'] = $this->resolveOrder($request->input('order'));

        Permission::create($data);

        return redirect()->route('admin.permissions.index');
    }

    public function edit(Permission $permission)
    {
        return Inertia::render('Admin/Permissions/Edit', [
            'permission' => [
                'id' => $permission->id,
                'name' => $permission->name,
                'description' => $permission->description,
                'order' => $permission->order,
            ],
        ]);
    }

    public function update(Request $request, Permission $permission)
    {
        $table = config('permission.table_names.permissions');

        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique($table, 'name')
                    ->where('guard_name', $permission->guard_name)
                    ->ignore($permission->id),
            ],
            'description' => ['required', 'string', 'max:255'],
            'order' => ['nullable', 'integer', 'min:0'],
        ]);

        $permission->update([
            'name' => trim($data['name']),
            'description' => trim($data['description']),
            'order' => $this->resolveOrder($request->input('order'), $permission->order),
        ]);

        return redirect()->route('admin.permissions.index');
    }

    public function destroy(Permission $permission)
    {
        if ($permission->roles()->exists() || $permission->users()->exists()) {
            return back()->withErrors([
                'delete' => 'No se puede eliminar un permiso asignado a roles o usuarios.',
            ]);
        }

        $permission->delete();

        return redirect()->route('admin.permissions.index');
    }

    public function reorder(Request $request)
    {
        $table = config('permission.table_names.permissions');

        $data = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', Rule::exists($table, 'id')],
            'page' => ['nullable', 'integer', 'min:1'],
            'perPage' => ['nullable', 'integer', 'min:1'],
        ]);

        $page = $data['page'] ?? 1;
        $perPage = $data['perPage'] ?? count($data['ids']);
        $start = (($page - 1) * $perPage) + 1;

        DB::transaction(function () use ($data, $start) {
            foreach ($data['ids'] as $index => $id) {
                Permission::whereKey($id)->update([
                    'order' => $start + $index,
                ]);
            }
        });

        return back();
    }

    private function resolveOrder($order, ?int $fallback = null): int
    {
        if ($order === null || $order === '') {
            if ($fallback !== null) {
                return $fallback;
            }

            return (int) Permission::max('order') + 1;
        }

        return max(0, (int) $order);
    }
}
