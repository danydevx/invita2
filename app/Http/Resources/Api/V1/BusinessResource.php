<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BusinessResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'business_type' => $this->business_type?->value ?? $this->business_type,
            'industry_id' => $this->industry_id,
            'logo_path' => $this->logo_path,
            'cover_image_path' => $this->cover_image_path,
            'description' => $this->description,
            'phone' => $this->phone,
            'email' => $this->email,
            'website' => $this->website,
            'timezone' => $this->timezone,
            'currency' => $this->currency,
            'is_active' => $this->is_active,
            'is_published' => $this->is_published,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),

            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),

            'industry' => $this->whenLoaded('industry', function () {
                return $this->industry ? [
                    'id' => $this->industry->id,
                    'name' => $this->industry->name,
                    'slug' => $this->industry->slug,
                ] : null;
            }),

            'plan' => $this->whenLoaded('user.subscriptions', function () {
                $activeSubscription = $this->user?->subscriptions?->where('status', 'active')->first();
                return $activeSubscription?->plan ? [
                    'id' => $activeSubscription->plan->id,
                    'name' => $activeSubscription->plan->name,
                    'limits' => $activeSubscription->plan->limits,
                ] : null;
            }),

            'modules' => $this->whenLoaded('modules', function () {
                return $this->modules->mapWithKeys(function ($module) {
                    return [$module->module_key => [
                        'enabled' => (bool) $module->is_enabled,
                        'module_name' => $module->moduleDefinition?->name,
                    ]];
                });
            }),
        ];
    }
}
