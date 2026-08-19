<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Businesses\Models\Business;
use Modules\Services\Models\BusinessService;

class ServicesController extends Controller
{
    public function index(Request $request, Business $business)
    {
        $this->authorize('viewAny', [BusinessService::class, $business]);

        $services = $business->services()->orderBy('sort_order')->get();

        return Inertia::render('Member/Services/Index', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'services' => $services,
        ]);
    }

    public function store(Request $request, Business $business, ActivityService $activity)
    {
        $this->authorize('create', [BusinessService::class, $business]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:1440'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'deposit_required' => ['boolean'],
            'deposit_amount' => ['nullable', 'numeric', 'min:0'],
            'allows_online_booking' => ['boolean'],
            'whatsapp_contact' => ['nullable', 'string', 'max:50'],
            'is_active' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
        ]);

        $data['business_id'] = $business->id;
        $data['slug'] = \Illuminate\Support\Str::slug($data['name']) . '-' . $business->id;

        $service = BusinessService::create($data);

        $activity->log('service_created', [
            'actor' => $request->user(),
            'subject' => $service,
            'description' => 'Servicio creado por miembro',
            'request' => $request,
        ]);

        return redirect()->back()->with('success', 'Servicio creado correctamente.');
    }

    public function update(Request $request, Business $business, BusinessService $service, ActivityService $activity)
    {
        $this->authorize('update', $service);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:1440'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'deposit_required' => ['boolean'],
            'deposit_amount' => ['nullable', 'numeric', 'min:0'],
            'allows_online_booking' => ['boolean'],
            'whatsapp_contact' => ['nullable', 'string', 'max:50'],
            'is_active' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
        ]);

        $service->update($data);

        $activity->log('service_updated', [
            'actor' => $request->user(),
            'subject' => $service,
            'description' => 'Servicio actualizado por miembro',
            'request' => $request,
        ]);

        return redirect()->back()->with('success', 'Servicio actualizado correctamente.');
    }

    public function destroy(Request $request, Business $business, BusinessService $service, ActivityService $activity)
    {
        $this->authorize('delete', $service);

        $service->delete();

        $activity->log('service_deleted', [
            'actor' => $request->user(),
            'subject' => $service,
            'description' => 'Servicio eliminado por miembro',
            'request' => $request,
        ]);

        return redirect()->back()->with('success', 'Servicio eliminado correctamente.');
    }
}
