<?php

use App\Jobs\CleanupExpiredPasswordResetsJob;
use App\Jobs\CleanupOldSystemErrorsJob;
use App\Jobs\CleanupOldWebhookDeliveriesJob;
use App\Jobs\RunProfileIncompleteAutomationsJob;
use App\Jobs\RunSubscriptionExpiredAutomationsJob;
use App\Jobs\RunTicketIdleAutomationsJob;
use App\Jobs\RunTrialEndingAutomationsJob;
use App\Jobs\RunWebhookFailureAutomationsJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new CleanupExpiredPasswordResetsJob)->hourly();
Schedule::job(new CleanupOldWebhookDeliveriesJob)->daily();
Schedule::job(new CleanupOldSystemErrorsJob)->weekly();
Schedule::job(new RunTicketIdleAutomationsJob)->hourly();
Schedule::job(new RunWebhookFailureAutomationsJob)->hourly();
Schedule::job(new RunTrialEndingAutomationsJob)->daily();
Schedule::job(new RunSubscriptionExpiredAutomationsJob)->daily();
Schedule::job(new RunProfileIncompleteAutomationsJob)->daily();
