<?php

namespace App\Events\WebMarket;

use App\Models\MarketListing;
use App\Models\User;
use App\Models\WebStorage;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ListingCancelled
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly User $user,
        public readonly MarketListing $listing,
        public readonly WebStorage $webStorage,
    ) {}
}
