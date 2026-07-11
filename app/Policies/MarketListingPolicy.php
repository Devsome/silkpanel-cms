<?php

namespace App\Policies;

use App\Models\MarketListing;
use App\Models\User;

class MarketListingPolicy
{
    public function view(User $user, MarketListing $listing): bool
    {
        return true; // Public listings are viewable by all authenticated users
    }

    public function cancel(User $user, MarketListing $listing): bool
    {
        return $user->id === $listing->user_id && $listing->isActive();
    }
}
