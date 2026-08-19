<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Businesses\Models\Business;

class BusinessAiChatbotController extends Controller
{
    public function index(Request $request, Business $business)
    {
        return Inertia::render('Admin/BusinessContent/AiChatbotIndex', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
            ],
        ]);
    }
}
