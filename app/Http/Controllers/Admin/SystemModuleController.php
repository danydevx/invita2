<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemModule;
use App\Services\ActivityService;
use App\Services\ModuleService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SystemModuleController extends Controller
{
    public function index(Request $request, ModuleService $modules)
    {
        $this->ensureSuperAdmin($request);

        return Inertia::render('Admin/SystemModules/Index', [
            'modules' => $modules->list(),
        ]);
    }

    public function update(Request $request, SystemModule $module, ModuleService $modules, ActivityService $activity)
    {
        $this->ensureSuperAdmin($request);

        $data = $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        if (! $modules->canDisable($module->key)) {
            return back()->withErrors([
                'is_active' => 'Este modulo no se puede desactivar.',
            ]);
        }

        $module->update([
            'is_active' => (bool) $data['is_active'],
        ]);

        $activity->log('module_updated', [
            'actor' => $request->user(),
            'subject' => $module,
            'description' => $data['is_active'] ? 'Modulo activado' : 'Modulo desactivado',
            'request' => $request,
        ]);

        return back()->with('success', 'Modulo actualizado correctamente.');
    }

    private function ensureSuperAdmin(Request $request): void
    {
        $user = $request->user();
        if (! $user || (! $user->hasAnyRole(['superadmin']) && (int) $user->id !== 1)) {
            abort(403);
        }
    }
}
