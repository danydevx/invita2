<?php

namespace App\Services\Properties;

use App\Services\ActivityService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Properties\Models\Property;
use Modules\Properties\Models\PropertyField;
use Modules\Properties\Models\PropertyImage;
use Modules\Properties\Models\PropertyValue;
use Modules\Properties\Models\PropertyAmenityProperty;

class PropertyService
{
    public function __construct(
        protected PropertyValueService $valueService,
        protected PropertyImageService $imageService
    ) {}

    public function getPropertiesQuery($business, array $filters = [])
    {
        $query = $business->properties()->with(['propertyType']);

        if (! empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', "%{$filters['search']}%")
                  ->orWhere('description', 'like', "%{$filters['search']}%");
            });
        }

        if (! empty($filters['property_type_id'])) {
            $query->where('property_type_id', $filters['property_type_id']);
        }

        if (! empty($filters['operation_type'])) {
            $query->where('operation_type', $filters['operation_type']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (isset($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        if (! empty($filters['city'])) {
            $query->where('city', $filters['city']);
        }

        if (! empty($filters['state'])) {
            $query->where('state_code', $filters['state']);
        }

        $sort = $filters['sort'] ?? 'created_at';
        $direction = $filters['direction'] ?? 'desc';
        $allowedSorts = ['title', 'price', 'status', 'created_at', 'operation_type'];

        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query;
    }

    public function createProperty($business, array $data): Property
    {
        return DB::transaction(function () use ($business, $data) {
            $propertyData = $this->extractMainFields($data);
            $propertyData['listing_id'] = $business->id;
            $propertyData['slug'] = $this->generateUniqueSlug($business, $data['title'] ?? '');
            $propertyData['property_code'] = $this->generateUniquePropertyCode();

            if ($this->isPublic($propertyData)) {
                $propertyData['published_at'] = now();
            }

            $property = $business->properties()->create($propertyData);

            if (isset($data['main_image'])) {
                $this->imageService->saveMainImage($property, $data['main_image']);
            }

            if (! empty($data['dynamic_values'])) {
                $dynamicValues = is_string($data['dynamic_values'])
                    ? json_decode($data['dynamic_values'], true)
                    : $data['dynamic_values'];
                $this->valueService->saveValues($property, $dynamicValues);
            }

            if (isset($data['amenity_ids'])) {
                $this->saveAmenities($property, $data['amenity_ids']);
            }

            return $property;
        });
    }

    public function updateProperty(Property $property, array $data): Property
    {
        return DB::transaction(function () use ($property, $data) {
            $propertyData = $this->extractMainFields($data);

            if (isset($data['title']) && $data['title'] !== $property->title) {
                $propertyData['slug'] = $this->generateUniqueSlug($property->listing, $data['title'], $property->id);
            }

            if (isset($data['remove_main_image']) && $data['remove_main_image']) {
                $this->imageService->deleteMainImage($property);
                $propertyData['main_image'] = null;
            } elseif (isset($data['main_image'])) {
                $this->imageService->saveMainImage($property, $data['main_image']);
            }

            $wasPublic = $property->is_public;
            $isNowPublic = $this->isPublic($propertyData);

            if ($isNowPublic && ! $wasPublic && ! $property->published_at) {
                $propertyData['published_at'] = now();
            }

            $property->update($propertyData);

            if (! empty($data['dynamic_values'])) {
                $dynamicValues = is_string($data['dynamic_values'])
                    ? json_decode($data['dynamic_values'], true)
                    : $data['dynamic_values'];
                $this->valueService->saveValues($property, $dynamicValues);
            }

            if (isset($data['amenity_ids'])) {
                $this->saveAmenities($property, $data['amenity_ids']);
            }

            return $property;
        });
    }

    protected function saveAmenities(Property $property, array $amenityIds): void
    {
        PropertyAmenityProperty::where('property_id', $property->id)->delete();

        foreach ($amenityIds as $amenityId) {
            PropertyAmenityProperty::create([
                'property_id' => $property->id,
                'property_amenity_id' => $amenityId,
                'value' => true,
            ]);
        }
    }

    public function deleteProperty(Property $property): void
    {
        DB::transaction(function () use ($property) {
            if ($property->main_image) {
                $this->imageService->deleteMainImage($property);
            }

            $property->values()->delete();
            $property->images()->delete();
            $property->amenities()->delete();
            $property->delete();
        });
    }

    public function duplicateProperty(Property $property): Property
    {
        return DB::transaction(function () use ($property) {
            $newProperty = $property->replicate();
            $newProperty->title = $property->title . ' (Copia)';
            $newProperty->slug = $this->generateUniqueSlug($property->listing, $property->title . '-copy');
            $newProperty->property_code = $this->generateUniquePropertyCode();
            $newProperty->status = 'draft';
            $newProperty->is_public = false;
            $newProperty->published_at = null;
            $newProperty->save();

            foreach ($property->values as $value) {
                $newValue = $value->replicate();
                $newValue->property_id = $newProperty->id;
                $newValue->save();
            }

            foreach ($property->amenities as $amenity) {
                PropertyAmenityProperty::create([
                    'property_id' => $newProperty->id,
                    'property_amenity_id' => $amenity->property_amenity_id,
                    'value' => $amenity->value,
                ]);
            }

            foreach ($property->images as $image) {
                $newImage = $image->replicate();
                $newImage->property_id = $newProperty->id;
                $newImage->save();

                if ($image->image_path && Storage::disk('public')->exists($image->image_path)) {
                    $directory = dirname($image->image_path);
                    $extension = pathinfo($image->image_path, PATHINFO_EXTENSION);
                    $filename = 'property_' . now()->format('YmdHis') . '_' . Str::random(8) . '.' . $extension;
                    $newPath = $directory . '/' . $filename;
                    Storage::disk('public')->copy($image->image_path, $newPath);
                    $newImage->update(['image_path' => $newPath]);
                }
            }

            return $newProperty;
        });
    }

    public function changeStatus(Property $property, string $status): Property
    {
        $property->update(['status' => $status]);

        if ($status === 'published' && ! $property->published_at) {
            $property->update(['published_at' => now(), 'is_public' => true]);
        }

        return $property;
    }

    protected function extractMainFields(array $data): array
    {
        $mainFields = [
            'property_type_id',
            'title',
            'description',
            'operation_type',
            'price',
            'currency',
            'price_period',
            'main_image',
            'status',
            'is_featured',
            'is_public',
            'country',
            'state',
            'state_code',
            'city',
            'municipality',
            'colony',
            'postal_code',
            'street',
            'exterior_number',
            'interior_number',
            'references',
            'latitude',
            'longitude',
            'show_exact_location',
        ];

        $result = [];
        foreach ($mainFields as $field) {
            if (array_key_exists($field, $data)) {
                $result[$field] = $data[$field];
            }
        }

        if (isset($result['is_featured'])) {
            $result['is_featured'] = (bool) $result['is_featured'];
        }

        if (isset($result['is_public'])) {
            $result['is_public'] = (bool) $result['is_public'];
        }

        if (isset($result['show_exact_location'])) {
            $result['show_exact_location'] = (bool) $result['show_exact_location'];
        }

        return $result;
    }

    protected function isPublic(array $data): bool
    {
        if (isset($data['is_public'])) {
            return (bool) $data['is_public'];
        }

        if (isset($data['status'])) {
            return $data['status'] === 'published';
        }

        return false;
    }

    protected function generateUniqueSlug($business, string $title, ?int $excludeId = null): string
    {
        $slug = \Illuminate\Support\Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        $baseQuery = $business->properties()->withTrashed();
        if ($excludeId) {
            $baseQuery->where('id', '!=', $excludeId);
        }

        while ($baseQuery->where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    protected function generateUniquePropertyCode(): string
    {
        do {
            $year = date('Y');
            $random = strtoupper(\Illuminate\Support\Str::random(6));
            $code = "PR{$year}{$random}";
        } while (Property::where('property_code', $code)->exists());

        return $code;
    }
}
