<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Businesses\Models\Business;
use Modules\Leads\Models\BusinessLead;
use Modules\Leads\Enums\LeadStatus;
use Modules\Leads\Enums\LeadSource;
use Modules\Locations\Models\BusinessLocation;

class LeadController extends Controller
{
    public function index(Request $request, Business $business)
    {
        $this->authorize('viewAny', [BusinessLead::class, $business]);

        $perPage = min((int) $request->get('per_page', 10), 100);
        $search = $request->get('search', '');
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');

        $allowedSorts = ['name', 'email', 'status', 'source', 'created_at'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'created_at';
        }
        $direction = strtolower($direction) === 'asc' ? 'asc' : 'desc';

        $query = $business->leads()
            ->with('location')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->orderBy($sort, $direction);

        $leads = $query->paginate($perPage);

        $dataTable = [
            'data' => collect($leads->items())->map(function ($lead) {
                return [
                    'id' => $lead->id,
                    'name' => $lead->name,
                    'email' => $lead->email,
                    'phone' => $lead->phone,
                    'status' => $lead->status->value,
                    'status_label' => $lead->status->label(),
                    'source' => $lead->source->value,
                    'source_label' => $lead->source->label(),
                    'notes' => $lead->notes,
                    'created_at' => $lead->created_at->toDateTimeString(),
                    'location' => $lead->location ? [
                        'id' => $lead->location->id,
                        'name' => $lead->location->name,
                    ] : null,
                ];
            })->toArray(),
            'current_page' => $leads->currentPage(),
            'last_page' => $leads->lastPage(),
            'per_page' => $leads->perPage(),
            'total' => $leads->total(),
            'from' => $leads->firstItem(),
            'to' => $leads->lastItem(),
        ];

        return Inertia::render('Member/Leads/Index', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'leads' => $leads,
            'dataTable' => $dataTable,
        ]);
    }

    public function create(Request $request, Business $business)
    {
        $this->authorize('create', [BusinessLead::class, $business]);

        $locations = $business->locations()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Member/Leads/Create', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'locations' => $locations,
        ]);
    }

    public function store(Request $request, Business $business, ActivityService $activity)
    {
        $this->authorize('create', [BusinessLead::class, $business]);

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

        $activity->log('lead_created', [
            'actor' => $request->user(),
            'subject' => $lead,
            'description' => 'Lead creado manualmente',
            'request' => $request,
        ]);

        return redirect()->route('member.businesses.leads.index', $business->id)
            ->with('success', 'Contacto creado correctamente.');
    }

    public function show(Request $request, Business $business, BusinessLead $lead)
    {
        $this->authorize('view', [BusinessLead::class, $lead]);

        $lead->load('location');

        return Inertia::render('Member/Leads/Show', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
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
        $this->authorize('update', [BusinessLead::class, $lead]);

        $lead->load('location');

        $locations = $business->locations()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Member/Leads/Edit', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
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
        $this->authorize('update', [BusinessLead::class, $lead]);

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

        $activity->log('lead_updated', [
            'actor' => $request->user(),
            'subject' => $lead,
            'description' => 'Lead actualizado',
            'request' => $request,
        ]);

        return redirect()->route('member.businesses.leads.index', $business->id)
            ->with('success', 'Contacto actualizado correctamente.');
    }

    public function destroy(Request $request, Business $business, BusinessLead $lead, ActivityService $activity)
    {
        $this->authorize('delete', [BusinessLead::class, $lead]);

        $activity->log('lead_deleted', [
            'actor' => $request->user(),
            'subject' => $lead,
            'description' => 'Lead eliminado',
        ]);

        $lead->delete();

        return redirect()->route('member.businesses.leads.index', $business->id)
            ->with('success', 'Contacto eliminado correctamente.');
    }

    public function export(Request $request, Business $business)
    {
        $this->authorize('viewAny', [BusinessLead::class, $business]);

        $leads = $business->leads()
            ->with('location')
            ->orderBy('created_at', 'desc')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="contactos_' . $business->id . '_' . date('Y-m-d') . '.csv"',
            'Cache-Control' => 'no-store, no-cache',
        ];

        $handle = fopen('php://temp', 'r+');

        fputcsv($handle, ['Nombre', 'Email', 'Telefono', 'Estado', 'Fuente', 'Notas', 'Ubicacion', 'Fecha']);

        foreach ($leads as $lead) {
            fputcsv($handle, [
                $lead->name,
                $lead->email,
                $lead->phone ?? '',
                $lead->status->label() ?? '',
                $lead->source->label() ?? '',
                $lead->notes ?? '',
                $lead->location?->name ?? '',
                $lead->created_at->format('Y-m-d H:i:s'),
            ]);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content, 200, $headers);
    }

    public function bulkDelete(Request $request, Business $business)
    {
        $this->authorize('deleteAny', [BusinessLead::class, $business]);

        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
        ]);

        $count = count($data['ids']);

        $business->leads()
            ->whereIn('id', $data['ids'])
            ->delete();

        $message = $count === 1
            ? "1 contacto eliminado correctamente."
            : "{$count} contactos eliminados correctamente.";

        return redirect()->back()
            ->with('success', $message);
    }
}
