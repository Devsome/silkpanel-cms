<?php

namespace App\Events\WebMarket;

use App\Models\User;
use App\Models\WebStorage;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ItemTransferredToStorage
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly User $user,
        public readonly WebStorage $webStorage,
    ) {}
}
