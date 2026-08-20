<?php

namespace Modules\Properties\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\Properties\PropertyCollection;
use App\Http\Resources\Properties\PropertyResource;
use App\Http\Resources\Properties\PropertyTypeResource;
use Illuminate\Http\Request;
use Modules\Listings\Models\Listing;
use Modules\Properties\Models\Property;
use Modules\Properties\Models\PropertyType;

class PropertyApiController extends Controller
{
    public function index(Request $request, Listing $listing)
    {
        $perPage = min((int) $request->get('per_page', 12), 50);

        $query = Property::where('listing_id', $listing->id)
            ->where('status', 'published')
            ->where('is_public', true)
            ->with(['propertyType', 'values.propertyField']);

        if ($request->has('type')) {
            $query->whereHas('propertyType', function ($q) use ($request) {
                $q->where('key', $request->get('type'));
            });
        }

        if ($request->has('operation')) {
            $query->where('operation_type', $request->get('operation'));
        }

        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->get('min_price'));
        }

        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->get('max_price'));
        }

        if ($request->has('featured')) {
            $query->where('is_featured', true);
        }

        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');

        $allowedSorts = ['price', 'created_at', 'title'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction === 'asc' ? 'asc' : 'desc');
        }

        $properties = $query->paginate($perPage);

        return new PropertyCollection($properties);
    }

    public function show(Listing $listing, Property $property)
    {
        if ($property->listing_id !== $listing->id) {
            abort(404);
        }

        if ($property->status !== 'published' || ! $property->is_public) {
            abort(404);
        }

        $property->load(['propertyType', 'values.propertyField']);

        return new PropertyResource($property);
    }

    public function types()
    {
        $types = PropertyType::active()
            ->public()
            ->orderBy('sort_order')
            ->get();

        return PropertyTypeResource::collection($types);
    }
}
