<?php

namespace Modules\ClientFidelity\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Modules\Businesses\Models\Business;
use Modules\ClientFidelity\Http\Requests\CreateClientFidelityCardRequest;
use Modules\ClientFidelity\Http\Requests\UpdateClientFidelityCardRequest;
use Modules\ClientFidelity\Models\ClientFidelityCard;
use Modules\ClientFidelity\Notifications\FidelityCardCompletedNotification;

class ClientFidelityCardController extends Controller
{
    public function index(Request $request, Business $business)
    {
        abort_unless($business->user_id === Auth::id(), 403);

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

        $query = ClientFidelityCard::where('business_id', $business->id)
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
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'dataTable' => $dataTable,
            'filters' => [
                'filter' => $filter,
                'search' => $search,
            ],
        ]);
    }

    public function create(Business $business)
    {
        abort_unless($business->user_id === Auth::id(), 403);

        return Inertia::render('Member/ClientFidelity/Create', [
            'business' => $business,
        ]);
    }

    public function store(CreateClientFidelityCardRequest $request, Business $business)
    {
        abort_unless($business->user_id === Auth::id(), 403);

        $validated = $request->validated();

        $card = ClientFidelityCard::create([
            'business_id' => $business->id,
            'client_name' => $validated['client_name'],
            'client_email' => $validated['client_email'] ?? null,
            'client_phone' => $validated['client_phone'] ?? null,
            'description' => $validated['description'] ?? null,
            'max_visits' => $validated['max_visits'],
            'current_visits' => $validated['max_visits'],
            'public_code' => ClientFidelityCard::generatePublicCode(),
            'is_active' => true,
        ]);

        return redirect()->route('member.businesses.fidelity-cards.show', [$business->id, $card->id])
            ->with('success', 'Tarjeta creada correctamente.');
    }

    public function show(Business $business, ClientFidelityCard $card)
    {
        abort_unless($business->user_id === Auth::id(), 403);
        abort_unless($card->business_id === $business->id, 403);

        $card->load('business');

        return Inertia::render('Member/ClientFidelity/Show', [
            'business' => $business,
            'card' => $card,
        ]);
    }

    public function edit(Business $business, ClientFidelityCard $card)
    {
        abort_unless($business->user_id === Auth::id(), 403);
        abort_unless($card->business_id === $business->id, 403);

        return Inertia::render('Member/ClientFidelity/Edit', [
            'business' => $business,
            'card' => $card,
        ]);
    }

    public function update(UpdateClientFidelityCardRequest $request, Business $business, ClientFidelityCard $card)
    {
        abort_unless($business->user_id === Auth::id(), 403);
        abort_unless($card->business_id === $business->id, 403);

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

    public function destroy(Business $business, ClientFidelityCard $card)
    {
        abort_unless($business->user_id === Auth::id(), 403);
        abort_unless($card->business_id === $business->id, 403);

        $card->delete();

        return redirect()->route('member.businesses.fidelity-cards.index', [$business->id])
            ->with('success', 'Tarjeta eliminada correctamente.');
    }

    public function scan(Business $business, ClientFidelityCard $card)
    {
        abort_unless($business->user_id === Auth::id(), 403);
        abort_unless($card->business_id === $business->id, 403);

        if ($card->current_visits <= 0) {
            return redirect()->back()->with('error', 'Esta tarjeta ya esta completada.');
        }

        $card->decrementVisit();

        if ($card->current_visits <= 0) {
            $owner = $business->user;
            $owner->notify(new FidelityCardCompletedNotification($card, $business));

            return redirect()->back()->with('success', "¡Tarjeta completada! El cliente {$card->client_name} ha ganado su premio.");
        }

        return redirect()->back()->with('success', "Visita registrada. {$card->visits_remaining} visitas restantes.");
    }

    public function reset(Business $business, ClientFidelityCard $card)
    {
        abort_unless($business->user_id === Auth::id(), 403);
        abort_unless($card->business_id === $business->id, 403);

        $card->reset();

        return redirect()->back()->with('success', 'Tarjeta reseteada correctamente.');
    }

    public function bulkDelete(Request $request, Business $business)
    {
        abort_unless($business->user_id === Auth::id(), 403);

        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:client_fidelity_cards,id',
        ]);

        ClientFidelityCard::where('business_id', $business->id)
            ->whereIn('id', $validated['ids'])
            ->delete();

        return redirect()->back()->with('success', 'Tarjetas eliminadas correctamente.');
    }

    public function scanByCode(Business $business, Request $request)
    {
        abort_unless($business->user_id === Auth::id(), 403);

        $request->validate([
            'public_code' => 'required|string|max:15',
        ]);

        $card = ClientFidelityCard::where('public_code', strtoupper($request->public_code))
            ->where('business_id', $business->id)
            ->first();

        if (!$card) {
            return redirect()->back()->with('error', 'Tarjeta no encontrada.');
        }

        if ($card->current_visits <= 0) {
            return redirect()->back()->with('error', 'Esta tarjeta ya esta completada.');
        }

        $card->decrementVisit();

        if ($card->current_visits <= 0) {
            $owner = $business->user;
            $owner->notify(new FidelityCardCompletedNotification($card, $business));

            return redirect()->back()->with('success', "¡Tarjeta completada! El cliente {$card->client_name} ha ganado su premio.");
        }

        return redirect()->back()->with('success', "Visita registrada para {$card->client_name}. {$card->visits_remaining} visitas restantes.");
    }

    public function scanView(Business $business)
    {
        abort_unless($business->user_id === Auth::id(), 403);

        return Inertia::render('Member/ClientFidelity/Scan', [
            'business' => $business,
        ]);
    }
}
