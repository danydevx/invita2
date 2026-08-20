<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Modules\Listings\Models\Listing;
use Modules\ListingClients\Models\ListingClient;

class ClientController extends Controller
{
    public function index(Request $request, Listing $business)
    {
        $this->authorize('viewAny', [ListingClient::class, $business]);

        $perPage = min((int) $request->get('per_page', 10), 100);
        $search = trim((string) $request->get('search', ''));
        $sort = $request->get('sort', 'created_at');
        $direction = strtolower((string) $request->get('direction', 'desc')) === 'asc' ? 'asc' : 'desc';

        $allowedSorts = ['created_at', 'company_name', 'contact_person', 'customer_email'];
        if (! in_array($sort, $allowedSorts, true)) {
            $sort = 'created_at';
        }

        $query = $business->clients()
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('customer_name', 'like', "%{$search}%")
                        ->orWhere('customer_email', 'like', "%{$search}%")
                        ->orWhere('company_name', 'like', "%{$search}%")
                        ->orWhere('contact_person', 'like', "%{$search}%")
                        ->orWhere('whatsapp', 'like', "%{$search}%")
                        ->orWhere('rfc', 'like', "%{$search}%");
                });
            })
            ->orderBy($sort, $direction);

        $clients = $query->paginate($perPage);

        $dataTable = [
            'data' => collect($clients->items())->map(function (ListingClient $client) {
                return [
                    'id' => $client->id,
                    'customer_name' => $client->customer_name,
                    'customer_email' => $client->customer_email,
                    'company_name' => $client->company_name,
                    'contact_person' => $client->contact_person,
                    'whatsapp' => $client->whatsapp,
                    'rfc' => $client->rfc,
                    'state_code' => $client->state_code,
                    'municipality' => $client->municipality,
                    'appointment_date' => $client->appointment_date?->format('Y-m-d'),
                ];
            })->all(),
            'current_page' => $clients->currentPage(),
            'last_page' => $clients->lastPage(),
            'per_page' => $clients->perPage(),
            'total' => $clients->total(),
            'from' => $clients->firstItem(),
            'to' => $clients->lastItem(),
        ];

        return Inertia::render('Member/Clients/Index', [
            'listing' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'clients' => $clients,
            'dataTable' => $dataTable,
        ]);
    }

    public function create(Request $request, Listing $business)
    {
        $this->authorize('create', [ListingClient::class, $business]);

        return Inertia::render('Member/Clients/Create', [
            'listing' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
        ]);
    }

    public function store(Request $request, Listing $business, ActivityService $activity)
    {
        $this->authorize('create', [ListingClient::class, $business]);

        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:150'],
            'customer_email' => ['nullable', 'email', 'max:150'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'contact_person' => ['nullable', 'string', 'max:150'],
            'company_name' => ['nullable', 'string', 'max:150'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
            'website' => ['nullable', 'url', 'max:255'],
            'rfc' => ['nullable', 'string', 'max:20'],
            'address_line_1' => ['nullable', 'string', 'max:150'],
            'address_line_2' => ['nullable', 'string', 'max:150'],
            'neighborhood' => ['nullable', 'string', 'max:150'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'state_code' => [
                'nullable',
                Rule::exists('mx_states', 'code'),
            ],
            'municipality' => ['nullable', 'string', 'max:150'],
            'status' => ['nullable', 'string', 'in:pending,confirmed,cancelled,completed,no_show'],
            'notes' => ['nullable', 'string'],
        ]);

        $client = $business->clients()->create($data);

        $activity->log('client_created', [
            'actor' => $request->user(),
            'subject' => $client,
            'description' => 'Cliente creado',
            'request' => $request,
        ]);

        return redirect()->route('member.listings.clients.index', $business->id)
            ->with('success', 'Cliente creado correctamente.');
    }

    public function edit(Request $request, Listing $business, ListingClient $client)
    {
        abort_unless($client->listing_id === $business->id, 404);
        $this->authorize('update', [ListingClient::class, $client]);

        return Inertia::render('Member/Clients/Edit', [
            'listing' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'client' => [
                'id' => $client->id,
                'customer_name' => $client->customer_name,
                'customer_email' => $client->customer_email,
                'customer_phone' => $client->customer_phone,
                'contact_person' => $client->contact_person,
                'company_name' => $client->company_name,
                'whatsapp' => $client->whatsapp,
                'website' => $client->website,
                'rfc' => $client->rfc,
                'address_line_1' => $client->address_line_1,
                'address_line_2' => $client->address_line_2,
                'neighborhood' => $client->neighborhood,
                'postal_code' => $client->postal_code,
                'state_code' => $client->state_code,
                'municipality' => $client->municipality,
                'status' => $client->status,
                'notes' => $client->notes,
            ],
        ]);
    }

    public function update(Request $request, Listing $business, ListingClient $client, ActivityService $activity)
    {
        abort_unless($client->listing_id === $business->id, 404);
        $this->authorize('update', [ListingClient::class, $client]);

        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:150'],
            'customer_email' => ['nullable', 'email', 'max:150'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'contact_person' => ['nullable', 'string', 'max:150'],
            'company_name' => ['nullable', 'string', 'max:150'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
            'website' => ['nullable', 'url', 'max:255'],
            'rfc' => ['nullable', 'string', 'max:20'],
            'address_line_1' => ['nullable', 'string', 'max:150'],
            'address_line_2' => ['nullable', 'string', 'max:150'],
            'neighborhood' => ['nullable', 'string', 'max:150'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'state_code' => [
                'nullable',
                Rule::exists('mx_states', 'code'),
            ],
            'municipality' => ['nullable', 'string', 'max:150'],
            'status' => ['nullable', 'string', 'in:pending,confirmed,cancelled,completed,no_show'],
            'notes' => ['nullable', 'string'],
        ]);

        $client->update($data);

        $activity->log('client_updated', [
            'actor' => $request->user(),
            'subject' => $client,
            'description' => 'Cliente actualizado',
            'request' => $request,
        ]);

        return redirect()->route('member.listings.clients.index', $business->id)
            ->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Request $request, Listing $business, ListingClient $client, ActivityService $activity)
    {
        abort_unless($client->listing_id === $business->id, 404);
        $this->authorize('delete', [ListingClient::class, $client]);

        $activity->log('client_deleted', [
            'actor' => $request->user(),
            'subject' => $client,
            'description' => 'Cliente eliminado',
        ]);

        $client->delete();

        return redirect()->route('member.listings.clients.index', $business->id)
            ->with('success', 'Cliente eliminado correctamente.');
    }

    public function bulkDelete(Request $request, Listing $business)
    {
        $this->authorize('deleteAny', [ListingClient::class, $business]);

        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => [
                'integer',
                Rule::exists('listing_clients', 'id')->where('listing_id', $business->id),
            ],
        ]);

        $count = ListingClient::where('listing_id', $business->id)
            ->whereIn('id', $data['ids'])
            ->delete();

        $message = $count === 1
            ? '1 cliente eliminado correctamente.'
            : "{$count} clientes eliminados correctamente.";

        return redirect()->back()->with('success', $message);
    }

    public function clone(Request $request, Listing $business, ListingClient $client, ActivityService $activity)
    {
        abort_unless($client->listing_id === $business->id, 404);
        $this->authorize('create', [ListingClient::class, $business]);

        $clonedClient = $business->clients()->create([
            'customer_name' => $client->customer_name . ' (copia)',
            'customer_email' => $client->customer_email,
            'customer_phone' => $client->customer_phone,
            'contact_person' => $client->contact_person,
            'company_name' => $client->company_name,
            'whatsapp' => $client->whatsapp,
            'website' => $client->website,
            'rfc' => $client->rfc,
            'address_line_1' => $client->address_line_1,
            'address_line_2' => $client->address_line_2,
            'neighborhood' => $client->neighborhood,
            'postal_code' => $client->postal_code,
            'state_code' => $client->state_code,
            'municipality' => $client->municipality,
            'notes' => $client->notes,
        ]);

        $activity->log('client_cloned', [
            'actor' => $request->user(),
            'subject' => $clonedClient,
            'description' => 'Cliente clonado',
        ]);

        return redirect()->route('member.listings.clients.index', $business->id)
            ->with('success', 'Cliente clonado correctamente.');
    }
}