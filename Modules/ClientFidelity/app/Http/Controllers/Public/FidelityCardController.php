<?php

namespace Modules\ClientFidelity\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Businesses\Models\Business;
use Modules\ClientFidelity\Models\ClientFidelityCard;

class FidelityCardController extends Controller
{
    public function show(string $slug, string $publicCode)
    {
        $business = Business::where('slug', $slug)->firstOrFail();

        $card = ClientFidelityCard::where('public_code', strtoupper($publicCode))
            ->where('business_id', $business->id)
            ->first();

        if (!$card) {
            abort(404, 'Tarjeta no encontrada');
        }

        return Inertia::render('Public/FidelityCard', [
            'business' => [
                'name' => $business->name,
                'slug' => $business->slug,
            ],
            'card' => [
                'client_name' => $card->client_name,
                'max_visits' => $card->max_visits,
                'current_visits' => $card->current_visits,
                'visits_remaining' => $card->visits_remaining,
                'progress_percentage' => $card->progress_percentage,
                'description' => $card->description,
                'is_completed' => $card->isCompleted(),
                'public_code' => $card->public_code,
            ],
        ]);
    }

    public function findByCode(Request $request, Business $business): JsonResponse
    {
        $request->validate([
            'public_code' => 'required|string|max:15',
        ]);

        $card = ClientFidelityCard::where('public_code', strtoupper($request->public_code))
            ->where('business_id', $business->id)
            ->first();

        if (!$card) {
            return response()->json(['error' => 'Tarjeta no encontrada'], 404);
        }

        return response()->json([
            'data' => [
                'id' => $card->id,
                'client_name' => $card->client_name,
                'max_visits' => $card->max_visits,
                'current_visits' => $card->current_visits,
                'visits_remaining' => $card->visits_remaining,
                'progress_percentage' => $card->progress_percentage,
                'description' => $card->description,
                'is_completed' => $card->isCompleted(),
                'public_code' => $card->public_code,
            ],
        ]);
    }
}
