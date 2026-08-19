<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\UserListResource;
use App\Http\Resources\Api\V1\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Modules\Businesses\Models\Business;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->get('per_page', 20), 100);

        $users = User::with(['subscriptions.plan:id,name'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'data' => UserListResource::collection($users->items()),
            'meta' => [
                'current_page' => $users->currentPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
                'last_page' => $users->lastPage(),
            ],
        ]);
    }

    public function show(User $user): JsonResponse
    {
        $user->load(['subscriptions.plan:id,name,limits']);

        return response()->json([
            'data' => new UserResource($user),
        ]);
    }

    public function businesses(User $user): JsonResponse
    {
        $businesses = Business::where('user_id', $user->id)
            ->with(['subscriptions.plan:id,name'])
            ->orderBy('created_at', 'desc')
            ->get(['id', 'name', 'slug', 'is_active', 'created_at']);

        return response()->json([
            'data' => $businesses,
            'meta' => ['total' => $businesses->count()],
        ]);
    }
}
