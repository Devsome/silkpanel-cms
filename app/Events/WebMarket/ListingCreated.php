<?php

namespace App\Events\WebMarket;

use App\Models\MarketListing;
use App\Models\User;
use App\Services\ItemTooltipService;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use SilkPanel\Discord\Enums\DiscordEventEnum;

class ListingCreated
{
    use Dispatchable, SerializesModels;

    public string $eventSlug = DiscordEventEnum::MARKET_ITEM_LISTED->value;

    public function __construct(
        public readonly User $user,
        public readonly MarketListing $listing,
    ) {}

    public function getVariables(): array
    {
        $itemName = app(ItemTooltipService::class)->getItemName((int) $this->listing->item_id64)
            ?: ($this->listing->item_name ?: 'Unknown Item');

        if ($this->listing->opt_level > 0) {
            $itemName = '+' . $this->listing->opt_level . ' ' . $itemName;
        }

        return [
            '{player_name}'  => $this->listing->character_name ?: ($this->user->name ?? 'Unknown'),
            '{item_name}'    => $itemName,
            '{price}'        => number_format($this->listing->price_amount),
            '{price_type}'   => $this->listing->priceTypeLabel(),
            '{timestamp}'    => now()->toIso8601String(),
            '{server_name}'  => config('app.name', 'SilkPanel'),
        ];
    }
}
