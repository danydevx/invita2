<?php

namespace Modules\Properties\Console\Commands;

use App\Services\Properties\GeneralFieldService;
use Illuminate\Console\Command;
use Modules\Properties\Models\GeneralFieldSection;
use Modules\Properties\Models\PropertyType;

class AssignLockedSections extends Command
{
    protected $signature = 'properties:assign-locked-sections';

    protected $description = 'Assigns locked sections to all PropertyTypes that do not have them';

    public function handle(GeneralFieldService $generalFieldService): int
    {
        $lockedSections = GeneralFieldSection::where('is_locked', true)->get();

        if ($lockedSections->isEmpty()) {
            $this->warn('No locked sections found.');
            return Command::SUCCESS;
        }

        $propertyTypes = PropertyType::all();
        $assigned = 0;
        $alreadyHad = 0;

        foreach ($propertyTypes as $propertyType) {
            $hasAllLocked = true;

            foreach ($lockedSections as $section) {
                $exists = $propertyType->generalFieldSections()
                    ->where('general_field_section_id', $section->id)
                    ->exists();

                if (!$exists) {
                    $hasAllLocked = false;
                    $generalFieldService->assignSectionToPropertyType($propertyType, $section);
                    $this->line("Assigned '{$section->name}' to PropertyType '{$propertyType->name}'");
                }
            }

            if ($hasAllLocked) {
                $alreadyHad++;
            } else {
                $assigned++;
            }
        }

        $this->info("Done! {$assigned} PropertyTypes were assigned new sections, {$alreadyHad} already had all locked sections.");

        return Command::SUCCESS;
    }
}
