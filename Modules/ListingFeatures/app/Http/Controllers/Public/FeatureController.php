<?php

namespace Modules\ListingFeatures\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Listings\Models\Listing;
use Modules\ListingFeatures\Models\ListingFeature;

class FeatureController extends Controller
{
    public function index(Listing $business)
    {
        if (!$business->is_active || !$business->is_published) {
            abort(404);
        }

        $features = ListingFeature::with(['feature', 'location'])
            ->where('listing_id', $business->id)
            ->where('is_active', true)
            ->get()
            ->sortBy(function ($bf) {
                return $bf->feature->sort_order ?? 0;
            })
            ->values()
            ->map(function ($bf) {
                return [
                    'id' => $bf->id,
                    'title' => $bf->feature->title,
                    'description' => $bf->feature->description,
                    'icon' => $bf->feature->icon,
                    'image_path' => $bf->feature->image_path,
                    'location_name' => $bf->location?->name,
                    'location_id' => $bf->location_id,
                ];
            });

        return response()->json([
            'listing_id' => $business->id,
            'business_name' => $business->name,
            'features' => $features,
        ]);
    }
}
