<?php

namespace App\Console\Commands;

use Modules\Businesses\Models\Business;
use Illuminate\Console\Command;

class SyncBusinessModules extends Command
{
    protected $signature = 'app:sync-business-modules {--business= : Sync a specific business by ID}';

    protected $description = 'Sync business modules based on user plan';

    public function handle(): int
    {
        if ($businessId = $this->option('business')) {
            $business = Business::find($businessId);
            if (!$business) {
                $this->error("Business #{$businessId} not found.");
                return Command::FAILURE;
            }
            $business->syncModulesFromPlan();
            $this->info("Business '{$business->name}' modules synced.");
            $this->printModules($business);
            return Command::SUCCESS;
        }

        $businesses = Business::with('user')->get();
        $count = 0;

        foreach ($businesses as $business) {
            $business->syncModulesFromPlan();
            $count++;
        }

        $this->info("Synced {$count} businesses.");
        return Command::SUCCESS;
    }

    private function printModules(Business $business): void
    {
        foreach ($business->modules as $module) {
            $status = $module->is_enabled ? 'ON' : 'OFF';
            $this->line("  - {$module->module_name}: {$status}");
        }
    }
}
