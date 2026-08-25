<?php

namespace Modules\Properties\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Property\StorePropertyRequest;
use App\Http\Requests\Property\UpdatePropertyRequest;
use App\Services\ActivityService;
use App\Services\Properties\PropertyFormSchemaService;
use App\Services\Properties\PropertyImageService;
use App\Services\Properties\PropertyLimitService;
use App\Services\Properties\PropertyService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Listings\Models\Listing;
use Modules\Properties\Models\Property;
use Modules\Properties\Models\PropertyType;

class PropertyController extends Controller
{
    public function __construct(
        protected PropertyService $propertyService,
        protected PropertyFormSchemaService $formSchemaService,
        protected PropertyImageService $imageService
    ) {}

    public function index(Request $request, Listing $listing)
    {
        $this->authorize('viewAny', [Property::class, $listing]);

        $perPage = min((int) $request->get('per_page', 10), 100);
        $search = $request->get('search', '');
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $propertyTypeId = $request->get('property_type');
        $operationType = $request->get('operation');
        $status = $request->get('status');
        $minPrice = $request->get('min_price');
        $maxPrice = $request->get('max_price');
        $city = $request->get('city');
        $state = $request->get('state');

        $filters = array_filter([
            'search' => $search,
            'property_type_id' => $propertyTypeId,
            'operation_type' => $operationType,
            'status' => $status,
            'min_price' => $minPrice,
            'max_price' => $maxPrice,
            'city' => $city,
            'state' => $state,
            'sort' => $sort,
            'direction' => $direction,
        ]);

        $query = $this->propertyService->getPropertiesQuery($listing, $filters);

        $properties = $query->paginate($perPage);

        $properties->getCollection()->transform(function ($property) {
            $item = $property->toArray();
            $item['main_image_url'] = $this->imageService->getImageUrl($property->main_image);
            $item['formatted_price'] = $property->getFormattedPrice();
            $item['operation_label'] = $property->getOperationLabel();
            $item['status_label'] = $property->getStatusLabel();
            $item['property_type_name'] = $property->propertyType?->name;
            $item['is_featured'] = $property->is_featured;

            $locationParts = array_filter([$property->colony, $property->city, $property->state]);
            $item['location'] = implode(', ', $locationParts);

            return $item;
        });

        $propertyTypes = PropertyType::active()->orderBy('name')->get(['id', 'name', 'key']);

        $availableStates = $listing->properties()
            ->whereNotNull('state_code')
            ->where('state_code', '!=', '')
            ->distinct()
            ->orderBy('state_code')
            ->pluck('state_code')
            ->toArray();

        $availableCities = [];
        if ($state) {
            $availableCities = $listing->properties()
                ->where('state_code', $state)
                ->whereNotNull('city')
                ->where('city', '!=', '')
                ->distinct()
                ->orderBy('city')
                ->pluck('city')
                ->toArray();
        }

        $dataTable = [
            'data' => $properties->items(),
            'current_page' => $properties->currentPage(),
            'last_page' => $properties->lastPage(),
            'per_page' => $properties->perPage(),
            'total' => $properties->total(),
            'from' => $properties->firstItem(),
            'to' => $properties->lastItem(),
        ];

        $statusOptions = Property::STATUSES;
        $operationOptions = Property::OPERATIONS;

        return Inertia::render('Member/Properties/Index', [
            'listing' => [
                'id' => $listing->id,
                'name' => $listing->name,
            ],
            'properties' => $properties,
            'propertyTypes' => $propertyTypes,
            'dataTable' => $dataTable,
            'filters' => $filters,
            'statusOptions' => $statusOptions,
            'operationOptions' => $operationOptions,
            'availableStates' => $availableStates,
            'availableCities' => $availableCities,
        ]);
    }

    public function create(Request $request, Listing $listing)
    {
        $this->authorize('create', [Property::class, $listing]);

        $limitCheck = (new PropertyLimitService())->forBusiness($listing)->canCreateProperty();

        if (! $limitCheck['allowed']) {
            return redirect()->back()->with('error', $limitCheck['reason']);
        }

        $propertyTypes = PropertyType::active()->orderBy('name')->get(['id', 'name', 'key', 'icon']);

        $selectedTypeId = $request->get('type');
        $formSchema = null;
        $typeAmenities = [];
        $selectedAmenityIds = [];

        if ($selectedTypeId) {
            $propertyType = PropertyType::find($selectedTypeId);
            if ($propertyType) {
                $formSchema = $this->formSchemaService->getFormSchema($propertyType);
                $typeAmenities = $propertyType->amenities()
                    ->active()
                    ->sorted()
                    ->get()
                    ->map(fn($a) => [
                        'id' => $a->id,
                        'key' => $a->key,
                        'name' => $a->name,
                        'icon' => $a->icon,
                    ]);
            }
        }

        return Inertia::render('Member/Properties/Create', [
            'listing' => [
                'id' => $listing->id,
                'name' => $listing->name,
            ],
            'propertyTypes' => $propertyTypes,
            'selectedTypeId' => $selectedTypeId,
            'formSchema' => $formSchema,
            'limitInfo' => $limitCheck,
            'amenities' => $typeAmenities,
            'selectedAmenityIds' => $selectedAmenityIds,
        ]);
    }

    public function store(StorePropertyRequest $request, Listing $listing, ActivityService $activity)
    {
        $this->authorize('create', [Property::class, $listing]);

        $limitCheck = (new PropertyLimitService())->forBusiness($listing)->canCreateProperty();

        if (! $limitCheck['allowed']) {
            return redirect()->back()->with('error', $limitCheck['reason']);
        }

        $data = $request->validated();

        $property = $this->propertyService->createProperty($listing, $data);

        $activity->log('property_created', [
            'actor' => $request->user(),
            'subject' => $property,
            'description' => 'Propiedad creada',
            'request' => $request,
        ]);

        return redirect()->route('member.listings.properties.index', $listing->id)
            ->with('success', 'Propiedad creada correctamente.');
    }

    public function edit(Request $request, Listing $listing, Property $property)
    {
        $property->load('listing');
        $this->authorize('update', [Property::class, $property]);

        $propertyType = $property->propertyType;
        $formSchema = $this->formSchemaService->getFormSchema($propertyType);

        $property->load(['values.propertyField', 'images']);

        $dynamicValues = [];
        foreach ($property->values as $value) {
            $fieldKey = $value->propertyField->field_key ?? null;
            if ($fieldKey) {
                $val = $value->getValue();
                if ($val instanceof \Carbon\Carbon) {
                    $val = $val->format('Y-m-d');
                }
                $dynamicValues[$fieldKey] = $val;
            }
        }

        $propertyImages = $property->images->map(fn($img) => [
            'id' => $img->id,
            'url' => $img->image_path ? "/storage/{$img->image_path}" : '',
            'filename' => basename($img->image_path ?? ''),
            'is_main' => $img->is_main,
        ])->toArray();

        $typeAmenities = $propertyType->amenities()
            ->active()
            ->sorted()
            ->get()
            ->map(fn($a) => [
                'id' => $a->id,
                'key' => $a->key,
                'name' => $a->name,
                'icon' => $a->icon,
            ]);

        $selectedAmenityIds = $property->amenities()
            ->pluck('property_amenity_id')
            ->toArray();

        return Inertia::render('Member/Properties/Edit', [
            'listing' => [
                'id' => $listing->id,
                'name' => $listing->name,
            ],
            'property' => [
                'id' => $property->id,
                'title' => $property->title,
                'slug' => $property->slug,
                'property_code' => $property->property_code,
                'description' => $property->description,
                'operation_type' => $property->operation_type,
                'price' => $property->price,
                'currency' => $property->currency,
                'price_period' => $property->price_period,
                'main_image' => $property->main_image,
                'main_image_url' => $this->imageService->getImageUrl($property->main_image),
                'status' => $property->status,
                'is_featured' => $property->is_featured,
                'is_public' => $property->is_public,
                'property_type_id' => $property->property_type_id,
                'country' => $property->country,
                'state' => $property->state,
                'state_code' => $property->state_code,
                'city' => $property->city,
                'municipality' => $property->municipality,
                'colony' => $property->colony,
                'postal_code' => $property->postal_code,
                'street' => $property->street,
                'exterior_number' => $property->exterior_number,
                'interior_number' => $property->interior_number,
                'references' => $property->references,
                'latitude' => $property->latitude,
                'longitude' => $property->longitude,
                'show_exact_location' => $property->show_exact_location,
            ],
            'propertyType' => [
                'id' => $propertyType->id,
                'name' => $propertyType->name,
                'key' => $propertyType->key,
            ],
            'formSchema' => $formSchema,
            'dynamicValues' => $dynamicValues,
            'propertyImages' => $propertyImages,
            'amenities' => $typeAmenities,
            'selectedAmenityIds' => $selectedAmenityIds,
        ]);
    }

    public function update(UpdatePropertyRequest $request, Listing $listing, Property $property, ActivityService $activity)
    {
        $property->load('listing');
        $this->authorize('update', [Property::class, $property]);

        $data = $request->validated();

        $property = $this->propertyService->updateProperty($property, $data);

        $activity->log('property_updated', [
            'actor' => $request->user(),
            'subject' => $property,
            'description' => 'Propiedad actualizada',
            'request' => $request,
        ]);

        return redirect()->route('member.listings.properties.index', $listing->id)
            ->with('success', 'Propiedad actualizada correctamente.');
    }

    public function destroy(Request $request, Listing $listing, Property $property, ActivityService $activity)
    {
        $property->load('listing');
        $this->authorize('delete', [Property::class, $property]);

        $this->propertyService->deleteProperty($property);

        $activity->log('property_deleted', [
            'actor' => $request->user(),
            'subject' => $property,
            'description' => 'Propiedad eliminada',
        ]);

        return redirect()->route('member.listings.properties.index', $listing->id)
            ->with('success', 'Propiedad eliminada correctamente.');
    }

    public function duplicate(Request $request, Listing $listing, Property $property, ActivityService $activity)
    {
        $property->load('listing');
        $this->authorize('update', [Property::class, $property]);

        $limitCheck = (new PropertyLimitService())->forBusiness($listing)->canCreateProperty();

        if (! $limitCheck['allowed']) {
            return redirect()->back()->with('error', $limitCheck['reason']);
        }

        $newProperty = $this->propertyService->duplicateProperty($property);

        $activity->log('property_duplicated', [
            'actor' => $request->user(),
            'subject' => $newProperty,
            'description' => 'Propiedad duplicada',
        ]);

        return redirect()->route('member.listings.properties.edit', [$listing->id, $newProperty->id])
            ->with('success', 'Propiedad duplicada correctamente.');
    }

    public function changeStatus(Request $request, Listing $listing, Property $property, ActivityService $activity)
    {
        $property->load('listing');
        $this->authorize('update', [Property::class, $property]);

        $data = $request->validate([
            'status' => ['required', 'in:' . implode(',', Property::STATUSES)],
        ]);

        $property = $this->propertyService->changeStatus($property, $data['status']);

        $activity->log('property_status_changed', [
            'actor' => $request->user(),
            'subject' => $property,
            'description' => 'Estado cambiado a ' . $property->getStatusLabel(),
        ]);

        return redirect()->back()
            ->with('success', "Estado cambiado a {$property->getStatusLabel()}.");
    }

    public function reorder(Request $request, Listing $listing)
    {
        $user = $request->user();

        if ($user->hasAnyRole(['superadmin', 'admin'])) {
        } else {
            abort_unless($listing->user_id === $user->id, 403);
        }

        $data = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer'],
        ]);

        $ids = $data['ids'];
        $start = 1;

        foreach ($ids as $id) {
            Property::where('id', $id)
                ->where('listing_id', $listing->id)
                ->update(['sort_order' => $start++]);
        }

        return back(303);
    }

    public function bulkDelete(Request $request, Listing $listing)
    {
        $user = $request->user();

        if ($user->hasAnyRole(['superadmin', 'admin'])) {
        } else {
            abort_unless($listing->user_id === $user->id, 403);
        }

        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
        ]);

        $properties = Property::where('listing_id', $listing->id)
            ->whereIn('id', $data['ids'])
            ->get();

        foreach ($properties as $property) {
            $this->propertyService->deleteProperty($property);
        }

        $count = count($data['ids']);
        $message = $count === 1
            ? "1 propiedad eliminada correctamente."
            : "{$count} propiedades eliminadas correctamente.";

        return redirect()->back()
            ->with('success', $message);
    }

    public function getFormSchema(Request $request, Listing $listing)
    {
        $this->authorize('viewAny', [Property::class, $listing]);

        $typeId = $request->get('type_id');

        if (! $typeId) {
            return response()->json(['error' => 'type_id es requerido'], 400);
        }

        $propertyType = PropertyType::find($typeId);

        if (! $propertyType) {
            return response()->json(['error' => 'Tipo de propiedad no encontrado'], 404);
        }

        $schema = $this->formSchemaService->getFormSchema($propertyType);

        return response()->json($schema);
    }
}
