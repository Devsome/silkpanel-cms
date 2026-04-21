<?php

namespace App\Console\Commands;

use App\Services\ReferralService;
use Illuminate\Console\Command;

class ProcessReferrals extends Command
{
    protected $signature = 'referrals:process';

    protected $description = 'Check pending referrals and award silk when referred players reach the required character level.';

    public function handle(ReferralService $referralService): int
    {
        $this->info('Processing pending referrals…');

        $processed = $referralService->processPendingReferrals();

        $this->info("Done. {$processed} referral(s) validated and rewarded.");

        return Command::SUCCESS;
    }
}
