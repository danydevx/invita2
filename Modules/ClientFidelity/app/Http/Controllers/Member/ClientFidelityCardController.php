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
use Modules\ClientFidelity\Models\FidelityCardCompletion;
use Modules\ClientFidelity\Models\FidelityReward;
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

        $rewards = FidelityReward::where('listing_id', $listing->id)->active()->sorted()->get(['id', 'title', 'max_visits']);

        return Inertia::render('Member/ClientFidelity/Create', [
            'listing' => $listing,
            'rewards' => $rewards,
        ]);
    }

    public function store(CreateClientFidelityCardRequest $request, Listing $listing)
    {
        abort_unless($listing->user_id === Auth::id(), 403);

        $validated = $request->validated();

        $reward = null;
        $maxVisits = $validated['max_visits'];
        if (!empty($validated['fidelity_reward_id'])) {
            $reward = FidelityReward::where('listing_id', $listing->id)->find($validated['fidelity_reward_id']);
            if ($reward) {
                $maxVisits = $reward->max_visits;
            }
        }

        $card = ClientFidelityCard::create([
            'listing_id' => $listing->id,
            'fidelity_reward_id' => $validated['fidelity_reward_id'] ?? null,
            'client_name' => $validated['client_name'],
            'client_email' => $validated['client_email'] ?? null,
            'client_phone' => $validated['client_phone'] ?? null,
            'description' => $validated['description'] ?? null,
            'max_visits' => $maxVisits,
            'current_visits' => $maxVisits,
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

        $rewards = FidelityReward::where('listing_id', $listing->id)->active()->sorted()->get(['id', 'title', 'max_visits']);

        return Inertia::render('Member/ClientFidelity/Edit', [
            'listing' => $listing,
            'card' => $card,
            'rewards' => $rewards,
        ]);
    }

    public function update(UpdateClientFidelityCardRequest $request, Listing $listing, ClientFidelityCard $card)
    {
        abort_unless($listing->user_id === Auth::id(), 403);
        abort_unless($card->listing_id === $listing->id, 403);

        $validated = $request->validated();

        $updateData = [
            'client_name' => $validated['client_name'],
            'client_email' => $validated['client_email'] ?? null,
            'client_phone' => $validated['client_phone'] ?? null,
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ];

        if (isset($validated['fidelity_reward_id'])) {
            $updateData['fidelity_reward_id'] = $validated['fidelity_reward_id'];
            $reward = FidelityReward::where('listing_id', $listing->id)->find($validated['fidelity_reward_id']);
            if ($reward) {
                $updateData['max_visits'] = $reward->max_visits;
                if ($card->current_visits > $reward->max_visits) {
                    $updateData['current_visits'] = $reward->max_visits;
                }
            }
        }

        $card->update($updateData);

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

        $wasCompleted = $card->decrementVisit(Auth::id());

        if ($wasCompleted && $card->current_visits <= 0) {
            $owner = $listing->user;
            $owner->notify(new FidelityCardCompletedNotification($card, $listing));

            return redirect()->back()->with('success', "¡Tarjeta completada! El cliente {$card->client_name} ha ganado su premio.");
        }

        return redirect()->back()->with('success', "Visita registrada. {$card->visits_remaining} visitas restantes.");
    }

    public function reset(Listing $listing, ClientFidelityCard $card)
    {
        abort_unless($listing->user_id === Auth::id(), 403);
        abort_unless($card->listing_id === $listing->id, 403);

        $card->reset(Auth::id());

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
            $owner->notify(new FidelityCardCompletedNotification($card, $listing));

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

    public function history(Request $request, Listing $listing)
    {
        abort_unless($listing->user_id === Auth::id(), 403);

        $perPage = min((int) $request->get('per_page', 25), 100);
        $search = $request->get('search', '');
        $rewardId = $request->get('reward_id');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        $query = FidelityCardCompletion::whereHas('card', function ($q) use ($listing) {
            $q->where('listing_id', $listing->id);
        })
            ->with(['card', 'reward', 'completedBy'])
            ->when($search, function ($q) use ($search) {
                $q->where('client_name', 'like', "%{$search}%");
            })
            ->when($rewardId, function ($q) use ($rewardId) {
                $q->where('fidelity_reward_id', $rewardId);
            })
            ->when($dateFrom, function ($q) use ($dateFrom) {
                $q->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($dateTo, function ($q) use ($dateTo) {
                $q->whereDate('created_at', '<=', $dateTo);
            })
            ->orderBy('created_at', 'desc');

        $completions = $query->paginate($perPage);

        $rewards = FidelityReward::where('listing_id', $listing->id)->active()->sorted()->get(['id', 'title', 'max_visits']);

        $groupedByClient = FidelityCardCompletion::whereHas('card', function ($q) use ($listing) {
                $q->where('listing_id', $listing->id);
            })
            ->when($search, function ($q) use ($search) {
                $q->where('client_name', 'like', "%{$search}%");
            })
            ->when($rewardId, function ($q) use ($rewardId) {
                $q->where('fidelity_reward_id', $rewardId);
            })
            ->when($dateFrom, function ($q) use ($dateFrom) {
                $q->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($dateTo, function ($q) use ($dateTo) {
                $q->whereDate('created_at', '<=', $dateTo);
            })
            ->selectRaw('client_name, COUNT(*) as total_completions, MAX(created_at) as last_completion')
            ->groupBy('client_name')
            ->orderByDesc('total_completions')
            ->get();

        $stats = [
            'total_completions' => $completions->total(),
            'unique_clients' => $groupedByClient->count(),
            'top_client' => $groupedByClient->first()?->client_name ?? '-',
            'top_client_count' => $groupedByClient->first()?->total_completions ?? 0,
            'most_popular_reward' => FidelityCardCompletion::whereHas('card', function ($q) use ($listing) {
                    $q->where('listing_id', $listing->id);
                })
                ->selectRaw('fidelity_reward_id, COUNT(*) as count')
                ->groupBy('fidelity_reward_id')
                ->orderByDesc('count')
                ->first()?->fidelity_reward_id,
        ];

        if ($stats['most_popular_reward']) {
            $reward = FidelityReward::find($stats['most_popular_reward']);
            $stats['most_popular_reward'] = $reward ? $reward->title : '-';
        }

        return Inertia::render('Member/ClientFidelity/History', [
            'listing' => $listing,
            'completions' => $completions,
            'groupedByClient' => $groupedByClient,
            'rewards' => $rewards,
            'stats' => $stats,
            'filters' => [
                'search' => $search,
                'reward_id' => $rewardId,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ],
        ]);
    }
}
