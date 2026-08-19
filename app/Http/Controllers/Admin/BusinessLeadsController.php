<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Businesses\Models\Business;
use Modules\Leads\Models\BusinessLead;
use Modules\Leads\Enums\LeadStatus;
use Modules\Leads\Enums\LeadSource;

class BusinessLeadsController extends Controller
{
    public function index(Request $request, Business $business)
    {
        $leads = $business->leads()
            ->with('location')
            ->orderByDesc('created_at')
            ->paginate(20);

        return Inertia::render('Admin/BusinessContent/LeadsIndex', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
            ],
            'leads' => $leads,
        ]);
    }

    public function create(Request $request, Business $business)
    {
        $locations = $business->locations()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Admin/BusinessContent/LeadsCreate', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
            ],
            'locations' => $locations,
        ]);
    }

    public function store(Request $request, Business $business, ActivityService $activity)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
            'business_location_id' => ['nullable', 'exists:business_locations,id'],
            'source' => ['nullable', 'string'],
        ]);

        $lead = $business->leads()->create([
            'business_id' => $business->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'notes' => $data['notes'] ?? null,
            'business_location_id' => $data['business_location_id'] ?? null,
            'status' => LeadStatus::NEW,
            'source' => $data['source'] ? LeadSource::from($data['source']) : LeadSource::MANUAL,
        ]);

        $activity->log('admin_lead_created', [
            'actor' => $request->user(),
            'subject' => $lead,
            'description' => 'Admin: Lead creado',
            'request' => $request,
        ]);

        return redirect()->route('admin.business.leads.index', $business->id)
            ->with('success', 'Contacto creado correctamente.');
    }

    public function show(Request $request, Business $business, BusinessLead $lead)
    {
        $lead->load('location');

        return Inertia::render('Admin/BusinessContent/LeadsShow', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
            ],
            'lead' => [
                'id' => $lead->id,
                'name' => $lead->name,
                'email' => $lead->email,
                'phone' => $lead->phone,
                'notes' => $lead->notes,
                'status' => $lead->status->value,
                'status_label' => $lead->status->label(),
                'source' => $lead->source->value,
                'source_label' => $lead->source->label(),
                'created_at' => $lead->created_at->toDateTimeString(),
                'updated_at' => $lead->updated_at->toDateTimeString(),
                'business_location_id' => $lead->business_location_id,
                'location' => $lead->location ? [
                    'id' => $lead->location->id,
                    'name' => $lead->location->name,
                ] : null,
            ],
        ]);
    }

    public function edit(Request $request, Business $business, BusinessLead $lead)
    {
        $lead->load('location');

        $locations = $business->locations()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Admin/BusinessContent/LeadsEdit', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
            ],
            'lead' => [
                'id' => $lead->id,
                'name' => $lead->name,
                'email' => $lead->email,
                'phone' => $lead->phone,
                'notes' => $lead->notes,
                'status' => $lead->status->value,
                'source' => $lead->source->value,
                'business_location_id' => $lead->business_location_id,
            ],
            'locations' => $locations,
        ]);
    }

    public function update(Request $request, Business $business, BusinessLead $lead, ActivityService $activity)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
            'business_location_id' => ['nullable', 'exists:business_locations,id'],
            'status' => ['required', 'string', 'in:new,contacted,qualified,converted,lost'],
            'source' => ['nullable', 'string'],
        ]);

        $lead->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'notes' => $data['notes'] ?? null,
            'business_location_id' => $data['business_location_id'] ?? null,
            'status' => LeadStatus::from($data['status']),
            'source' => $data['source'] ? LeadSource::from($data['source']) : LeadSource::MANUAL,
        ]);

        $activity->log('admin_lead_updated', [
            'actor' => $request->user(),
            'subject' => $lead,
            'description' => 'Admin: Lead actualizado',
            'request' => $request,
        ]);

        return redirect()->route('admin.business.leads.index', $business->id)
            ->with('success', 'Contacto actualizado correctamente.');
    }

    public function destroy(Request $request, Business $business, BusinessLead $lead, ActivityService $activity)
    {
        $activity->log('admin_lead_deleted', [
            'actor' => $request->user(),
            'subject' => $lead,
            'description' => 'Admin: Lead eliminado',
        ]);

        $lead->delete();

        return redirect()->route('admin.business.leads.index', $business->id)
            ->with('success', 'Contacto eliminado correctamente.');
    }
}
