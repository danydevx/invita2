<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportDepartment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SupportDepartmentController extends Controller
{
    public function index()
    {
        $departments = SupportDepartment::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(fn ($dept) => [
                'id' => $dept->id,
                'name' => $dept->name,
                'email' => $dept->email,
                'description' => $dept->description,
                'is_active' => $dept->is_active,
                'sort_order' => $dept->sort_order,
                'tickets_count' => $dept->tickets()->count(),
            ]);

        return Inertia::render('Admin/Support/Departments/Index', [
            'departments' => $departments,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:150'],
            'description' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        SupportDepartment::create([
            'name' => trim($data['name']),
            'email' => $data['email'] ? trim($data['email']) : null,
            'description' => $data['description'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $data['is_active'] ?? true,
        ]);

        return redirect()->route('admin.support.departments.index')
            ->with('success', 'Departamento creado correctamente.');
    }

    public function update(Request $request, SupportDepartment $department)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:150'],
            'description' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $department->update([
            'name' => trim($data['name']),
            'email' => $data['email'] ? trim($data['email']) : null,
            'description' => $data['description'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $data['is_active'] ?? true,
        ]);

        return redirect()->back()->with('success', 'Departamento actualizado correctamente.');
    }

    public function destroy(SupportDepartment $department)
    {
        if ($department->tickets()->exists()) {
            return redirect()->back()->withErrors(['error' => 'No se puede eliminar un departamento con tickets.']);
        }

        $department->delete();

        return redirect()->route('admin.support.departments.index')
            ->with('success', 'Departamento eliminado correctamente.');
    }
}
