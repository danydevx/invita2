<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Listings\Models\Listing;

class ListingAiChatbotController extends Controller
{
    public function index(Request $request, Listing $business)
    {
        return Inertia::render('Admin/BusinessContent/AiChatbotIndex', [
            'listing' => [
                'id' => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
            ],
        ]);
    }
}
