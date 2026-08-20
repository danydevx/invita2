<?php

namespace App\Services\Properties;

use Modules\Listings\Models\Listing;
use App\Models\Plan;
use App\Models\Subscription;
use Modules\Properties\Models\Property;

class PropertyLimitService
{
    protected ?Listing $business = null;

    public function forBusiness(Listing $business): self
    {
        $service = new self();
        $service->business = $business;
        return $service;
    }

    public function canCreateProperty(): array
    {
        $limits = $this->getLimits();

        if (! $limits) {
            return ['allowed' => true, 'reason' => null, 'limit' => null];
        }

        $propertyLimit = $limits['properties_limit'] ?? null;

        if ($propertyLimit === 0) {
            return [
                'allowed' => false,
                'reason' => 'Tu plan no incluye propiedades.',
                'limit' => 0,
            ];
        }

        if ($propertyLimit === null) {
            return ['allowed' => true, 'reason' => null, 'limit' => null];
        }

        $currentCount = $this->business->properties()->count();

        if ($currentCount >= $propertyLimit) {
            return [
                'allowed' => false,
                'reason' => "Has alcanzado el límite de {$propertyLimit} propiedades.",
                'limit' => $propertyLimit,
                'current' => $currentCount,
            ];
        }

        return [
            'allowed' => true,
            'reason' => null,
            'limit' => $propertyLimit,
            'current' => $currentCount,
            'remaining' => $propertyLimit - $currentCount,
        ];
    }

    public function canUsePublic(): bool
    {
        $limits = $this->getLimits();
        return $limits['properties_public'] ?? true;
    }

    public function canUseGallery(): bool
    {
        $limits = $this->getLimits();
        return $limits['properties_gallery'] ?? true;
    }

    public function getMaxImages(): int
    {
        $limits = $this->getLimits();
        return $limits['properties_max_images'] ?? 10;
    }

    public function canUseFeatured(): bool
    {
        $limits = $this->getLimits();
        return $limits['properties_featured'] ?? true;
    }

    protected function getLimits(): ?array
    {
        if (! $this->business) {
            return null;
        }

        $subscription = $this->business->user?->subscription;

        if (! $subscription) {
            return null;
        }

        $plan = $subscription->plan;

        if (! $plan) {
            return null;
        }

        return $plan->limits;
    }
}
