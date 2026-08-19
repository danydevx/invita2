<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Businesses\Models\Business;
use Modules\SocialMedia\Models\BusinessSocialNetwork;

class SocialNetworkController extends Controller
{
    public function index(Request $request, Business $business)
    {
        $this->authorize('viewAny', [BusinessSocialNetwork::class, $business]);

        $perPage = min((int) $request->get('per_page', 10), 100);
        $search = $request->get('search', '');
        $sort = $request->get('sort', 'sort_order');
        $direction = $request->get('direction', 'asc');

        $allowedSorts = ['platform', 'is_active', 'sort_order', 'created_at'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'sort_order';
        }
        $direction = strtolower($direction) === 'desc' ? 'desc' : 'asc';

        $query = $business->socialNetworks()
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('platform', 'like', "%{$search}%")
                      ->orWhere('username', 'like', "%{$search}%")
                      ->orWhere('url', 'like', "%{$search}%");
                });
            })
            ->orderBy($sort, $direction);

        $socialNetworks = $query->paginate($perPage);

        $dataTable = [
            'data' => collect($socialNetworks->items())->map(function ($sn) {
                return [
                    'id' => $sn->id,
                    'platform' => $sn->platform,
                    'url' => $sn->url,
                    'username' => $sn->username,
                    'is_active' => $sn->is_active,
                    'show_on_hero' => $sn->show_on_hero,
                    'show_on_footer' => $sn->show_on_footer,
                    'show_on_contact' => $sn->show_on_contact,
                    'sort_order' => $sn->sort_order,
                ];
            })->toArray(),
            'current_page' => $socialNetworks->currentPage(),
            'last_page' => $socialNetworks->lastPage(),
            'per_page' => $socialNetworks->perPage(),
            'total' => $socialNetworks->total(),
            'from' => $socialNetworks->firstItem(),
            'to' => $socialNetworks->lastItem(),
        ];

        return Inertia::render('Member/SocialNetworks/Index', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'socialNetworks' => $socialNetworks,
            'platforms' => BusinessSocialNetwork::$platforms,
            'dataTable' => $dataTable,
        ]);
    }

    public function store(Request $request, Business $business, ActivityService $activity)
    {
        $this->authorize('create', [BusinessSocialNetwork::class, $business]);

        $data = $request->validate([
            'platform' => ['required', 'string'],
            'url' => ['required', 'string', 'max:500', 'url'],
            'username' => ['nullable', 'string', 'max:100'],
            'is_active' => ['boolean'],
            'show_on_hero' => ['boolean'],
            'show_on_footer' => ['boolean'],
            'show_on_contact' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
        ]);

        $data['business_id'] = $business->id;

        $socialNetwork = BusinessSocialNetwork::create($data);

        $activity->log('social_network_created', [
            'actor' => $request->user(),
            'subject' => $socialNetwork,
            'description' => 'Red social creada por miembro',
            'request' => $request,
        ]);

        return redirect()->back()->with('success', 'Red social creada correctamente.');
    }

    public function update(Request $request, Business $business, BusinessSocialNetwork $socialNetwork, ActivityService $activity)
    {
        $this->authorize('update', $socialNetwork);

        $data = $request->validate([
            'url' => ['required', 'string', 'max:500', 'url'],
            'username' => ['nullable', 'string', 'max:100'],
            'is_active' => ['boolean'],
            'show_on_hero' => ['boolean'],
            'show_on_footer' => ['boolean'],
            'show_on_contact' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
        ]);

        $socialNetwork->update($data);

        $activity->log('social_network_updated', [
            'actor' => $request->user(),
            'subject' => $socialNetwork,
            'description' => 'Red social actualizada por miembro',
            'request' => $request,
        ]);

        return redirect()->back()->with('success', 'Red social actualizada correctamente.');
    }

    public function destroy(Request $request, Business $business, BusinessSocialNetwork $socialNetwork, ActivityService $activity)
    {
        $this->authorize('delete', $socialNetwork);

        $socialNetwork->delete();

        $activity->log('social_network_deleted', [
            'actor' => $request->user(),
            'subject' => $socialNetwork,
            'description' => 'Red social eliminada por miembro',
            'request' => $request,
        ]);

        return redirect()->back()->with('success', 'Red social eliminada correctamente.');
    }

    public function reorder(Request $request, Business $business)
    {
        $user = $request->user();

        if ($user->hasAnyRole(['superadmin', 'admin'])) {
        } else {
            abort_unless($business->user_id === $user->id, 403);
        }

        $data = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', \Illuminate\Validation\Rule::exists('business_social_networks', 'id')->where('business_id', $business->id)],
            'page' => ['nullable', 'integer', 'min:1'],
            'perPage' => ['nullable', 'integer', 'min:1'],
        ]);

        $page = $data['page'] ?? 1;
        $perPage = $data['perPage'] ?? count($data['ids']);
        $start = (($page - 1) * $perPage) + 1;

        \DB::transaction(function () use ($data, $business, $start) {
            foreach ($data['ids'] as $index => $id) {
                \Modules\SocialNetworks\Models\BusinessSocialNetwork::where('id', $id)
                    ->where('business_id', $business->id)
                    ->update(['sort_order' => $start + $index]);
            }
        });

        return back(303);
    }
}
