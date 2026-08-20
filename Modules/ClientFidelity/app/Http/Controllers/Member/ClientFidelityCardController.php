<?php

namespace Modules\ClientFidelity\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Modules\Listings\Models\Listing;
use Modules\ClientFidelity\Http\Requests\CreateClientFidelityCardRequest;
use Modules\ClientFidelity\Http\Requests\UpdateClientFidelityCardRequest;
use Modules\ClientFidelity\Models\ClientFidelityCard;
use Modules\ClientFidelity\Notifications\FidelityCardCompletedNotification;

class ClientFidelityCardController extends Controller
{
    public function index(Request $request, Listing $listing)
    {
        abort_unless($listing->user_id === Auth::id(), 403);

        $perPage = min((int) $request->get('per_page', 10), 100);
        $search = $request->get('search', '');
        $filter = $request->get('filter', 'all');
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');

        $allowedSorts = ['client_name', 'public_code', 'current_visits', 'max_visits', 'is_active', 'completed_at', 'created_at'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'created_at';
        }
        $direction = strtolower($direction) === 'asc' ? 'asc' : 'desc';

        $query = ClientFidelityCard::where('listing_id', $listing->id)
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('client_name', 'like', "%{$search}%")
                        ->orWhere('public_code', 'like', "%{$search}%")
                        ->orWhere('client_email', 'like', "%{$search}%")
                        ->orWhere('client_phone', 'like', "%{$search}%");
                });
            })
            ->when($filter === 'active', function ($q) {
                $q->whereNull('completed_at')->where('is_active', true);
            })
            ->when($filter === 'completed', function ($q) {
                $q->whereNotNull('completed_at');
            })
            ->orderBy($sort, $direction);

        $cards = $query->paginate($perPage);

        $dataTable = [
            'data' => collect($cards->items())->map(function ($card) {
                return [
                    'id' => $card->id,
                    'client_name' => $card->client_name,
                    'client_email' => $card->client_email,
                    'client_phone' => $card->client_phone,
                    'public_code' => $card->public_code,
                    'max_visits' => $card->max_visits,
                    'current_visits' => $card->current_visits,
                    'visits_remaining' => $card->visits_remaining,
                    'progress_percentage' => $card->progress_percentage,
                    'description' => $card->description,
                    'is_active' => $card->is_active,
                    'is_completed' => $card->isCompleted(),
                    'completed_at' => $card->completed_at,
                    'reset_count' => $card->reset_count,
                    'created_at' => $card->created_at,
                ];
            })->toArray(),
            'current_page' => $cards->currentPage(),
            'last_page' => $cards->lastPage(),
            'per_page' => $cards->perPage(),
            'total' => $cards->total(),
            'from' => $cards->firstItem(),
            'to' => $cards->lastItem(),
        ];

        return Inertia::render('Member/ClientFidelity/Index', [
            'listing' => [
                'id' => $listing->id,
                'name' => $listing->name,
            ],
            'dataTable' => $dataTable,
            'filters' => [
                'filter' => $filter,
                'search' => $search,
            ],
        ]);
    }

    public function create(Listing $listing)
    {
        abort_unless($listing->user_id === Auth::id(), 403);

        return Inertia::render('Member/ClientFidelity/Create', [
            'listing' => $listing,
        ]);
    }

    public function store(CreateClientFidelityCardRequest $request, Listing $listing)
    {
        abort_unless($listing->user_id === Auth::id(), 403);

        $validated = $request->validated();

        $card = ClientFidelityCard::create([
            'listing_id' => $listing->id,
            'client_name' => $validated['client_name'],
            'client_email' => $validated['client_email'] ?? null,
            'client_phone' => $validated['client_phone'] ?? null,
            'description' => $validated['description'] ?? null,
            'max_visits' => $validated['max_visits'],
            'current_visits' => $validated['max_visits'],
            'public_code' => ClientFidelityCard::generatePublicCode(),
            'is_active' => true,
        ]);

        return redirect()->route('member.listings.fidelity-cards.show', [$listing->id, $card->id])
            ->with('success', 'Tarjeta creada correctamente.');
    }

    public function show(Listing $listing, ClientFidelityCard $card)
    {
        abort_unless($listing->user_id === Auth::id(), 403);
        abort_unless($card->listing_id === $listing->id, 403);

        $card->load('business');

        return Inertia::render('Member/ClientFidelity/Show', [
            'listing' => $listing,
            'card' => $card,
        ]);
    }

    public function edit(Listing $listing, ClientFidelityCard $card)
    {
        abort_unless($listing->user_id === Auth::id(), 403);
        abort_unless($card->listing_id === $listing->id, 403);

        return Inertia::render('Member/ClientFidelity/Edit', [
            'listing' => $listing,
            'card' => $card,
        ]);
    }

    public function update(UpdateClientFidelityCardRequest $request, Listing $listing, ClientFidelityCard $card)
    {
        abort_unless($listing->user_id === Auth::id(), 403);
        abort_unless($card->listing_id === $listing->id, 403);

        $validated = $request->validated();

        $card->update([
            'client_name' => $validated['client_name'],
            'client_email' => $validated['client_email'] ?? null,
            'client_phone' => $validated['client_phone'] ?? null,
            'description' => $validated['description'] ?? null,
            'max_visits' => $validated['max_visits'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        if ($card->current_visits > $card->max_visits) {
            $card->update(['current_visits' => $card->max_visits]);
        }

        return redirect()->back()->with('success', 'Tarjeta actualizada correctamente.');
    }

    public function destroy(Listing $listing, ClientFidelityCard $card)
    {
        abort_unless($listing->user_id === Auth::id(), 403);
        abort_unless($card->listing_id === $listing->id, 403);

        $card->delete();

        return redirect()->route('member.listings.fidelity-cards.index', [$listing->id])
            ->with('success', 'Tarjeta eliminada correctamente.');
    }

    public function scan(Listing $listing, ClientFidelityCard $card)
    {
        abort_unless($listing->user_id === Auth::id(), 403);
        abort_unless($card->listing_id === $listing->id, 403);

        if ($card->current_visits <= 0) {
            return redirect()->back()->with('error', 'Esta tarjeta ya esta completada.');
        }

        $card->decrementVisit();

        if ($card->current_visits <= 0) {
            $owner = $listing->user;
            $owner->notify(new FidelityCardCompletedNotification($card, $business));

            return redirect()->back()->with('success', "¡Tarjeta completada! El cliente {$card->client_name} ha ganado su premio.");
        }

        return redirect()->back()->with('success', "Visita registrada. {$card->visits_remaining} visitas restantes.");
    }

    public function reset(Listing $listing, ClientFidelityCard $card)
    {
        abort_unless($listing->user_id === Auth::id(), 403);
        abort_unless($card->listing_id === $listing->id, 403);

        $card->reset();

        return redirect()->back()->with('success', 'Tarjeta reseteada correctamente.');
    }

    public function bulkDelete(Request $request, Listing $listing)
    {
        abort_unless($listing->user_id === Auth::id(), 403);

        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:client_fidelity_cards,id',
        ]);

        ClientFidelityCard::where('listing_id', $listing->id)
            ->whereIn('id', $validated['ids'])
            ->delete();

        return redirect()->back()->with('success', 'Tarjetas eliminadas correctamente.');
    }

    public function scanByCode(Listing $listing, Request $request)
    {
        abort_unless($listing->user_id === Auth::id(), 403);

        $request->validate([
            'public_code' => 'required|string|max:15',
        ]);

        $card = ClientFidelityCard::where('public_code', strtoupper($request->public_code))
            ->where('listing_id', $listing->id)
            ->first();

        if (!$card) {
            return redirect()->back()->with('error', 'Tarjeta no encontrada.');
        }

        if ($card->current_visits <= 0) {
            return redirect()->back()->with('error', 'Esta tarjeta ya esta completada.');
        }

        $card->decrementVisit();

        if ($card->current_visits <= 0) {
            $owner = $listing->user;
            $owner->notify(new FidelityCardCompletedNotification($card, $business));

            return redirect()->back()->with('success', "¡Tarjeta completada! El cliente {$card->client_name} ha ganado su premio.");
        }

        return redirect()->back()->with('success', "Visita registrada para {$card->client_name}. {$card->visits_remaining} visitas restantes.");
    }

    public function scanView(Listing $listing)
    {
        abort_unless($listing->user_id === Auth::id(), 403);

        return Inertia::render('Member/ClientFidelity/Scan', [
            'listing' => $listing,
        ]);
    }
}
