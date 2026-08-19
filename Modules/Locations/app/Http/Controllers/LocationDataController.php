<?php

namespace Modules\Locations\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class LocationDataController extends Controller
{
    public function states(): JsonResponse
    {
        $states = DB::table('mx_states')
            ->orderBy('name')
            ->get(['code', 'name', 'lat', 'lng']);

        return response()->json([
            'states' => $states,
        ]);
    }

    public function municipalities(string $stateCode): JsonResponse
    {
        $municipalities = DB::table('mx_municipalities')
            ->where('state_code', $stateCode)
            ->orderBy('name')
            ->get(['code', 'name', 'is_metropolitan', 'lat', 'lng']);

        return response()->json([
            'municipalities' => $municipalities,
        ]);
    }
}
