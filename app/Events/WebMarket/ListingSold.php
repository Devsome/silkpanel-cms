<?php

namespace App\Events\WebMarket;

use App\Models\MarketListing;
use App\Models\MarketTransaction;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ListingSold
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly User $buyer,
        public readonly User $seller,
        public readonly MarketListing $listing,
        public readonly MarketTransaction $transaction,
    ) {}
}
