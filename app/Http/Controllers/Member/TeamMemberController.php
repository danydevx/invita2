<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Listings\Models\Listing;
use Modules\ListingTeamMembers\Models\ListingTeamMember;
use Modules\ListingTeamMembers\Models\TeamMemberPosition;
use Illuminate\Support\Facades\Storage;

class TeamMemberController extends Controller
{
    public function index(Request $request, Listing $business)
    {
        $this->authorize('viewAny', [ListingTeamMember::class, $business]);

        $perPage = min((int) $request->get('per_page', 10), 100);
        $search = $request->get('search', '');
        $positionId = $request->get('position');
        $sort = $request->get('sort', 'sort_order');
        $direction = $request->get('direction', 'asc');

        $allowedSorts = ['name', 'email', 'phone', 'is_active', 'sort_order', 'created_at'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'sort_order';
        }
        $direction = strtolower($direction) === 'desc' ? 'desc' : 'asc';

        $query = $business->teamMembers()
            ->with('position:id,name')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($positionId, function ($q) use ($positionId) {
                $q->where('position_id', $positionId);
            })
            ->orderBy($sort, $direction)
            ->orderBy('name');

        $members = $query->paginate($perPage);

        $positions = TeamMemberPosition::where('listing_id', $business->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $dataTable = [
            'data' => collect($members->items())->map(function ($member) {
                return [
                    'id' => $member->id,
                    'name' => $member->name,
                    'email' => $member->email,
                    'phone' => $member->phone,
                    'bio' => $member->bio,
                    'image' => $member->image,
                    'position_id' => $member->position_id,
                    'position' => $member->position ? [
                        'id' => $member->position->id,
                        'name' => $member->position->name,
                    ] : null,
                    'is_active' => $member->is_active,
                    'sort_order' => $member->sort_order,
                    'created_at' => $member->created_at,
                ];
            })->toArray(),
            'current_page' => $members->currentPage(),
            'last_page' => $members->lastPage(),
            'per_page' => $members->perPage(),
            'total' => $members->total(),
            'from' => $members->firstItem(),
            'to' => $members->lastItem(),
        ];

        return Inertia::render('Member/TeamMembers/Index', [
            'listing' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'dataTable' => $dataTable,
            'positions' => $positions,
            'filters' => [
                'position' => $positionId,
            ],
        ]);
    }

    public function create(Request $request, Listing $business)
    {
        $this->authorize('create', [ListingTeamMember::class, $business]);

        $positions = TeamMemberPosition::where('listing_id', $business->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Member/TeamMembers/Create', [
            'listing' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'positions' => $positions,
        ]);
    }

    public function store(Request $request, Listing $business, ActivityService $activity)
    {
        $this->authorize('create', [ListingTeamMember::class, $business]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:50'],
            'bio' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
            'position_id' => ['nullable', 'exists:team_member_positions,id'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ], [], [
            'name' => 'nombre',
            'email' => 'correo electrónico',
            'phone' => 'teléfono',
            'bio' => 'biografía',
            'image' => 'imagen',
            'position_id' => 'puesto',
            'is_active' => 'activo',
            'sort_order' => 'orden',
        ]);

        $data['listing_id'] = $business->id;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('team-members/' . $business->id, ['disk' => 'public']);
            $data['image'] = Storage::disk('public')->url($path);
        }

        if (!isset($data['sort_order'])) {
            $data['sort_order'] = ListingTeamMember::where('listing_id', $business->id)->max('sort_order') + 1;
        }

        $member = ListingTeamMember::create($data);

        $activity->log('team_member_created', [
            'actor' => $request->user(),
            'subject' => $member,
            'description' => 'Miembro del equipo creado',
            'request' => $request,
        ]);

        return redirect()->route('member.listings.team-members.index', $business->id)
            ->with('success', 'Miembro del equipo creado correctamente.');
    }

    public function edit(Request $request, Listing $business, ListingTeamMember $member)
    {
        $this->authorize('update', [ListingTeamMember::class, $member]);

        abort_unless($member->listing_id === $business->id, 404);

        $positions = TeamMemberPosition::where('listing_id', $business->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Member/TeamMembers/Edit', [
            'listing' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'member' => [
                'id' => $member->id,
                'name' => $member->name,
                'email' => $member->email,
                'phone' => $member->phone,
                'bio' => $member->bio,
                'image' => $member->image,
                'position_id' => $member->position_id,
                'is_active' => $member->is_active,
                'sort_order' => $member->sort_order,
            ],
            'positions' => $positions,
        ]);
    }

    public function update(Request $request, Listing $business, ListingTeamMember $member, ActivityService $activity)
    {
        $this->authorize('update', [ListingTeamMember::class, $member]);

        abort_unless($member->listing_id === $business->id, 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:50'],
            'bio' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
            'position_id' => ['nullable', 'exists:team_member_positions,id'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ], [], [
            'name' => 'nombre',
            'email' => 'correo electrónico',
            'phone' => 'teléfono',
            'bio' => 'biografía',
            'image' => 'imagen',
            'position_id' => 'puesto',
            'is_active' => 'activo',
            'sort_order' => 'orden',
        ]);

        if ($request->hasFile('image')) {
            if ($member->image) {
                $oldPath = str_replace(url('/') . '/storage/', '', $member->image);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('image')->store('team-members/' . $business->id, ['disk' => 'public']);
            $data['image'] = Storage::disk('public')->url($path);
        } else {
            unset($data['image']);
        }

        $member->update($data);

        $activity->log('team_member_updated', [
            'actor' => $request->user(),
            'subject' => $member,
            'description' => 'Miembro del equipo actualizado',
            'request' => $request,
        ]);

        return redirect()->route('member.listings.team-members.index', $business->id)
            ->with('success', 'Miembro del equipo actualizado correctamente.');
    }

    public function destroy(Request $request, Listing $business, ListingTeamMember $member, ActivityService $activity)
    {
        $this->authorize('delete', [ListingTeamMember::class, $member]);

        abort_unless($member->listing_id === $business->id, 404);

        if ($member->image) {
            $oldPath = str_replace(url('/') . '/storage/', '', $member->image);
            Storage::disk('public')->delete($oldPath);
        }

        $activity->log('team_member_deleted', [
            'actor' => $request->user(),
            'subject' => $member,
            'description' => 'Miembro del equipo eliminado',
        ]);

        $member->delete();

        return redirect()->route('member.listings.team-members.index', $business->id)
            ->with('success', 'Miembro del equipo eliminado correctamente.');
    }

    public function bulkDelete(Request $request, Listing $business)
    {
        $this->authorize('deleteAny', [ListingTeamMember::class, $business]);

        $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', \Illuminate\Validation\Rule::exists('business_team_members', 'id')->where('listing_id', $business->id)],
        ]);

        $members = ListingTeamMember::where('listing_id', $business->id)
            ->whereIn('id', $request->ids)
            ->get();

        foreach ($members as $member) {
            if ($member->image) {
                $oldPath = str_replace(url('/') . '/storage/', '', $member->image);
                Storage::disk('public')->delete($oldPath);
            }
        }

        $count = ListingTeamMember::where('listing_id', $business->id)
            ->whereIn('id', $request->ids)
            ->delete();

        return redirect()->back()->with('success', $count . ' miembro(s) eliminado(s).');
    }

    public function reorder(Request $request, Listing $business)
    {
        $user = $request->user();

        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            // allowed
        } else {
            abort_unless($business->user_id === $user->id, 403);
        }

        $data = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', \Illuminate\Validation\Rule::exists('business_team_members', 'id')->where('listing_id', $business->id)],
            'page' => ['nullable', 'integer', 'min:1'],
            'perPage' => ['nullable', 'integer', 'min:1'],
        ]);

        $page = $data['page'] ?? 1;
        $perPage = $data['perPage'] ?? count($data['ids']);
        $start = (($page - 1) * $perPage) + 1;

        \DB::transaction(function () use ($data, $business, $start) {
            foreach ($data['ids'] as $index => $id) {
                ListingTeamMember::where('id', $id)
                    ->where('listing_id', $business->id)
                    ->update(['sort_order' => $start + $index]);
            }
        });

        return back(303);
    }
}
