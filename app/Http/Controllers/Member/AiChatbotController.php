<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Listings\Models\Listing;

class AiChatbotController extends Controller
{
    public function index(Request $request, Listing $business)
    {
        $user = $request->user();

        if (!$user->hasAnyRole(['superadmin', 'admin']) && $business->user_id !== $user->id) {
            abort(403, 'No tienes permiso para acceder a este modulo.');
        }

        return Inertia::render('Member/AiChatbot/Index', [
            'listing' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
        ]);
    }
}
