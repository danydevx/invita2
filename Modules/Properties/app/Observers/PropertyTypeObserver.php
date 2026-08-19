<?php

namespace Modules\Properties\Observers;

use App\Services\Properties\GeneralFieldService;
use Modules\Properties\Models\GeneralFieldSection;
use Modules\Properties\Models\PropertyType;

class PropertyTypeObserver
{
    public function __construct(
        protected GeneralFieldService $generalFieldService
    ) {}

    public function created(PropertyType $propertyType): void
    {
        $this->assignLockedSections($propertyType);
    }

    protected function assignLockedSections(PropertyType $propertyType): void
    {
        $lockedSections = GeneralFieldSection::where('is_locked', true)->get();

        foreach ($lockedSections as $section) {
            $this->generalFieldService->assignSectionToPropertyType($propertyType, $section);
        }
    }
}
