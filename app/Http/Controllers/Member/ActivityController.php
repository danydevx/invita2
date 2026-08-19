<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $activities = Activity::query()
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(15)
            ->withQueryString()
            ->through(fn ($activity) => [
                'id' => $activity->id,
                'type' => $activity->type,
                'description' => $activity->description,
                'subject_type' => $activity->subject_type,
                'subject_id' => $activity->subject_id,
                'created_at' => $activity->created_at?->toDateTimeString(),
            ]);

        return Inertia::render('Member/Activity/Index', [
            'activities' => $activities,
        ]);
    }
}
