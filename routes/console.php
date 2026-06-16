<?php

use App\Console\Commands\ProcessReferrals;
use Illuminate\Support\Facades\Schedule;

Schedule::command(ProcessReferrals::class)->hourly();

// Processes queued jobs (e.g. Discord webhooks) every minute and exits when the queue is empty.
// This is an alternative to running a persistent queue:work daemon.
// Requires the scheduler itself to be triggered via cron: * * * * * php artisan schedule:run
Schedule::command('queue:work --stop-when-empty')->everyMinute()->withoutOverlapping();

// Writes a heartbeat timestamp to cache so the admin panel can detect whether the scheduler is running.
Schedule::call(fn() => cache()->put('silkpanel_scheduler_last_run', now()->toIso8601String(), now()->addMinutes(10)))->everyMinute()->name('scheduler-heartbeat');
