<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Appointments\Models\BusinessAppointment;
use Modules\Businesses\Models\Business;
use Modules\Leads\Models\BusinessLead;
use Modules\Promotions\Models\BusinessPromotion;
use Modules\Reviews\Models\BusinessReview;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $businesses = $user->businesses()
            ->select('id', 'name', 'description')
            ->orderBy('name')
            ->get()
            ->map(fn ($biz) => [
                'id' => $biz->id,
                'name' => $biz->name,
                'description' => $biz->description,
            ]);

        $businessIds = $businesses->pluck('id');
        $businessCount = $businessIds->count();

        if ($businessCount === 0) {
            $counts = [
                'leads' => 0,
                'appointments' => 0,
                'promotions' => 0,
                'reviews' => 0,
            ];
        } else {
            $counts = [
                'leads' => BusinessLead::whereIn('business_id', $businessIds)->count(),
                'appointments' => BusinessAppointment::whereIn('business_id', $businessIds)->count(),
                'promotions' => BusinessPromotion::whereIn('business_id', $businessIds)->count(),
                'reviews' => BusinessReview::whereIn('business_id', $businessIds)->count(),
            ];
        }

        return Inertia::render('Member/Dashboard', [
            'businessCount' => $businessCount,
            'stats' => $counts,
            'businesses' => $businesses,
        ]);
    }
}
