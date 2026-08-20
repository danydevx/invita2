<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Listings\Models\Listing;
use Modules\ListingTeamMembers\Models\TeamMemberPosition;

class TeamMemberPositionController extends Controller
{
    public function index(Request $request, Listing $business)
    {
        $this->authorize('viewAny', [\Modules\ListingTeamMembers\Models\ListingTeamMember::class, $business]);

        $positions = TeamMemberPosition::where('listing_id', $business->id)
            ->with('parent:id,name')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(function ($position) {
                return [
                    'id' => $position->id,
                    'name' => $position->name,
                    'slug' => $position->slug,
                    'description' => $position->description,
                    'parent_id' => $position->parent_id,
                    'parent' => $position->parent ? [
                        'id' => $position->parent->id,
                        'name' => $position->parent->name,
                    ] : null,
                    'is_active' => $position->is_active,
                    'sort_order' => $position->sort_order,
                    'children_count' => $position->children()->count(),
                    'members_count' => $position->teamMembers()->count(),
                ];
            });

        return Inertia::render('Member/TeamMemberPositions/Index', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'positions' => $positions,
        ]);
    }

    public function create(Request $request, Listing $business)
    {
        $this->authorize('create', [\Modules\ListingTeamMembers\Models\ListingTeamMember::class, $business]);

        $parentPositions = TeamMemberPosition::where('listing_id', $business->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'parent_id']);

        return Inertia::render('Member/TeamMemberPositions/Create', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'parentPositions' => $parentPositions,
        ]);
    }

    public function store(Request $request, Listing $business, ActivityService $activity)
    {
        $this->authorize('create', [\Modules\ListingTeamMembers\Models\ListingTeamMember::class, $business]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'parent_id' => ['nullable', 'exists:team_member_positions,id'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ], [], [
            'name' => 'nombre',
            'description' => 'descripción',
            'parent_id' => 'puesto padre',
            'is_active' => 'activo',
            'sort_order' => 'orden',
        ]);

        $data['listing_id'] = $business->id;
        $data['slug'] = TeamMemberPosition::generateUniqueSlug($business->id, $data['name']);

        if (!isset($data['sort_order'])) {
            $data['sort_order'] = TeamMemberPosition::where('listing_id', $business->id)->max('sort_order') + 1;
        }

        $position = TeamMemberPosition::create($data);

        $activity->log('team_member_position_created', [
            'actor' => $request->user(),
            'subject' => $position,
            'description' => 'Puesto de equipo creado',
            'request' => $request,
        ]);

        return redirect()->route('member.businesses.team-member-positions.index', $business->id)
            ->with('success', 'Puesto creado correctamente.');
    }

    public function edit(Request $request, Listing $business, TeamMemberPosition $position)
    {
        $this->authorize('update', [\Modules\ListingTeamMembers\Models\ListingTeamMember::class, $position]);

        abort_unless($position->listing_id === $business->id, 404);

        $parentPositions = TeamMemberPosition::where('listing_id', $business->id)
            ->where('is_active', true)
            ->where('id', '!=', $position->id)
            ->orderBy('name')
            ->get(['id', 'name', 'parent_id']);

        return Inertia::render('Member/TeamMemberPositions/Edit', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'position' => [
                'id' => $position->id,
                'name' => $position->name,
                'slug' => $position->slug,
                'description' => $position->description,
                'parent_id' => $position->parent_id,
                'is_active' => $position->is_active,
                'sort_order' => $position->sort_order,
            ],
            'parentPositions' => $parentPositions,
        ]);
    }

    public function update(Request $request, Listing $business, TeamMemberPosition $position, ActivityService $activity)
    {
        $this->authorize('update', [\Modules\ListingTeamMembers\Models\ListingTeamMember::class, $position]);

        abort_unless($position->listing_id === $business->id, 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'parent_id' => ['nullable', 'exists:team_member_positions,id'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ], [], [
            'name' => 'nombre',
            'description' => 'descripción',
            'parent_id' => 'puesto padre',
            'is_active' => 'activo',
            'sort_order' => 'orden',
        ]);

        if ($data['name'] !== $position->name) {
            $data['slug'] = TeamMemberPosition::generateUniqueSlug($business->id, $data['name'], $position->id);
        }

        $position->update($data);

        $activity->log('team_member_position_updated', [
            'actor' => $request->user(),
            'subject' => $position,
            'description' => 'Puesto de equipo actualizado',
            'request' => $request,
        ]);

        return redirect()->route('member.businesses.team-member-positions.index', $business->id)
            ->with('success', 'Puesto actualizado correctamente.');
    }

    public function destroy(Request $request, Listing $business, TeamMemberPosition $position, ActivityService $activity)
    {
        $this->authorize('delete', [\Modules\ListingTeamMembers\Models\ListingTeamMember::class, $position]);

        abort_unless($position->listing_id === $business->id, 404);

        if ($position->teamMembers()->exists()) {
            return redirect()->back()->with('error', 'No se puede eliminar un puesto que tiene miembros asignados.');
        }

        if ($position->children()->exists()) {
            return redirect()->back()->with('error', 'No se puede eliminar un puesto que tiene sub-puestos.');
        }

        $activity->log('team_member_position_deleted', [
            'actor' => $request->user(),
            'subject' => $position,
            'description' => 'Puesto de equipo eliminado',
        ]);

        $position->delete();

        return redirect()->route('member.businesses.team-member-positions.index', $business->id)
            ->with('success', 'Puesto eliminado correctamente.');
    }
}
