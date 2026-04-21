<?php

use App\Console\Commands\ProcessReferrals;
use Illuminate\Support\Facades\Schedule;

Schedule::command(ProcessReferrals::class)->hourly();
