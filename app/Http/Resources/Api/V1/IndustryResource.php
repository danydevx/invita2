<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IndustryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'icon' => $this->icon,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),

            'modules' => $this->whenLoaded('moduleDefinitions', function () {
                return $this->moduleDefinitions->map(function ($module) {
                    return [
                        'id' => $module->id,
                        'module_key' => $module->key,
                        'module_name' => $module->name,
                        'icon' => $module->icon,
                        'is_premium' => (bool) $module->is_premium,
                    ];
                });
            }),
        ];
    }
}
