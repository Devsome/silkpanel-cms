<?php

namespace App\Jobs;

use App\Services\MarketListingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessExpiredMarketListingsJob implements ShouldQueue
{
    use Queueable;

    public function handle(MarketListingService $listingService): void
    {
        $listingService->processExpiredListings();
    }
}
