<?php

namespace App\Http\Resources\Properties;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => [
                'key' => $this->propertyType->key,
                'name' => $this->propertyType->name,
            ],
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'operation' => [
                'key' => $this->operation_type,
                'label' => $this->getOperationLabel(),
            ],
            'price' => [
                'amount' => (float) $this->price,
                'currency' => $this->currency,
                'period' => $this->price_period,
                'formatted' => $this->getFormattedPrice(),
            ],
            'main_image' => $this->main_image ? [
                'url' => "/storage/{$this->main_image}",
                'alt' => $this->title,
            ] : null,
            'status' => $this->status,
            'status_label' => $this->getStatusLabel(),
            'is_featured' => $this->is_featured,
            'is_public' => $this->is_public,
            'published_at' => $this->published_at?->toIso8601String(),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
            'fields' => $this->getDynamicValues(),
        ];
    }
}
