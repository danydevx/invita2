<?php

namespace Modules\ClientFidelity\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientFidelityCardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'listing_id' => $this->listing_id,
            'client_name' => $this->client_name,
            'client_email' => $this->client_email,
            'client_phone' => $this->client_phone,
            'description' => $this->description,
            'max_visits' => $this->max_visits,
            'current_visits' => $this->current_visits,
            'public_code' => $this->public_code,
            'is_active' => $this->is_active,
            'is_completed' => $this->isCompleted(),
            'completed_at' => $this->completed_at?->toIso8601String(),
            'reset_count' => $this->reset_count,
            'progress_percentage' => $this->progress_percentage,
            'visits_remaining' => $this->visits_remaining,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
            'business' => $this->whenLoaded('business', function () {
                return [
                    'id' => $this->business->id,
                    'name' => $this->business->name,
                    'slug' => $this->business->slug,
                ];
            }),
        ];
    }
}
