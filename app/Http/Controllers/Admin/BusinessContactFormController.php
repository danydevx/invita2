<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Businesses\Models\Business;

class BusinessContactFormController extends Controller
{
    public function submissions(Request $request, Business $business)
    {
        $submissions = $business->leads()
            ->where('source', 'website')
            ->with('location')
            ->orderByDesc('created_at')
            ->paginate(20);

        return Inertia::render('Admin/BusinessContent/ContactFormIndex', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
            ],
            'submissions' => $submissions,
        ]);
    }
}
