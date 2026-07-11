<?php

namespace App\Services;

use App\Enums\MarketFeeTypeEnum;
use App\Enums\MarketListingStatusEnum;
use App\Events\WebMarket\ListingCancelled;
use App\Events\WebMarket\ListingCreated;
use App\Events\WebMarket\ListingExpired;
use App\Models\MarketListing;
use App\Models\Setting;
use App\Models\User;
use App\Models\WebStorage;
use Illuminate\Support\Facades\DB;
use Throwable;

class MarketListingService
{
    /**
     * Create a new marketplace listing from a web storage item.
     *
     * @return array{success: bool, error: string|null, listing: MarketListing|null}
     */
    public function createListing(
        User $user,
        int $charId,
        WebStorage $webStorageItem,
        string $priceType,
        int $priceAmount,
        int $durationHours,
        ?string $description = null,
    ): array {
        if (! (bool) Setting::get('marketplace_enabled', false)) {
            return ['success' => false, 'error' => 'marketplace_disabled', 'listing' => null];
        }

        if ($webStorageItem->user_id !== $user->id) {
            return ['success' => false, 'error' => 'item_not_owned', 'listing' => null];
        }

        if ($webStorageItem->isListed()) {
            return ['success' => false, 'error' => 'item_already_listed', 'listing' => null];
        }

        if (! ($webStorageItem->item_data['can_trade'] ?? true)) {
            return ['success' => false, 'error' => 'item_not_tradeable', 'listing' => null];
        }

        if (! $this->isPriceTypeAllowed($priceType)) {
            return ['success' => false, 'error' => 'price_type_not_allowed', 'listing' => null];
        }

        $maxPrice = $this->getMaxPriceForType($priceType);
        if ($maxPrice !== null && $priceAmount > $maxPrice) {
            return ['success' => false, 'error' => 'price_exceeds_maximum', 'listing' => null];
        }

        if ($priceAmount < 1) {
            return ['success' => false, 'error' => 'price_too_low', 'listing' => null];
        }

        $maxDuration = (int) Setting::get('web_market_max_duration_hours', 336);
        if ($durationHours > $maxDuration) {
            $durationHours = $maxDuration;
        }

        if ($durationHours < 1) {
            return ['success' => false, 'error' => 'duration_invalid', 'listing' => null];
        }

        // Check limits
        $maxPerAccount = (int) Setting::get('web_market_max_listings_account', 20);
        $activeAccountListings = MarketListing::where('user_id', $user->id)
            ->where('status', MarketListingStatusEnum::ACTIVE)
            ->count();
        if ($activeAccountListings >= $maxPerAccount) {
            return ['success' => false, 'error' => 'account_listing_limit_reached', 'listing' => null];
        }

        $maxPerChar = (int) Setting::get('web_market_max_listings_character', 10);
        $activeCharListings = MarketListing::where('user_id', $user->id)
            ->where('character_id', $charId)
            ->where('status', MarketListingStatusEnum::ACTIVE)
            ->count();
        if ($activeCharListings >= $maxPerChar) {
            return ['success' => false, 'error' => 'character_listing_limit_reached', 'listing' => null];
        }

        [$feeType, $feeAmount] = $this->calculateFee($priceAmount);

        try {
            $listing = DB::transaction(function () use ($user, $charId, $webStorageItem, $priceType, $priceAmount, $feeType, $feeAmount, $durationHours, $description) {
                return MarketListing::create([
                    'user_id' => $user->id,
                    'character_id' => $charId,
                    'character_name' => $webStorageItem->character_name,
                    'item_id64' => $webStorageItem->item_id64,
                    'ref_item_id' => $webStorageItem->ref_item_id,
                    'item_name' => $webStorageItem->item_name,
                    'opt_level' => $webStorageItem->opt_level,
                    'quantity' => $webStorageItem->quantity,
                    'item_data' => $webStorageItem->item_data,
                    'price_type' => $priceType,
                    'price_amount' => $priceAmount,
                    'fee_type' => $feeType,
                    'fee_amount' => $feeAmount,
                    'description' => $description,
                    'expires_at' => now()->addHours($durationHours),
                    'status' => MarketListingStatusEnum::ACTIVE,
                ]);
            });

            event(new ListingCreated($user, $listing));
            return ['success' => true, 'error' => null, 'listing' => $listing];
        } catch (Throwable $e) {
            report($e);
            return ['success' => false, 'error' => 'unexpected_error', 'listing' => null];
        }
    }

    /**
     * Cancel an active listing and return item to web storage.
     *
     * @return array{success: bool, error: string|null, web_storage: WebStorage|null}
     */
    public function cancelListing(User $user, MarketListing $listing): array
    {
        if ($listing->user_id !== $user->id) {
            return ['success' => false, 'error' => 'not_your_listing', 'web_storage' => null];
        }

        if (! $listing->isActive()) {
            return ['success' => false, 'error' => 'listing_not_active', 'web_storage' => null];
        }

        try {
            $webStorage = null;

            DB::transaction(function () use ($listing, $user, &$webStorage) {
                // Re-lock the listing
                $fresh = MarketListing::where('id', $listing->id)
                    ->where('status', MarketListingStatusEnum::ACTIVE)
                    ->lockForUpdate()
                    ->first();

                if (! $fresh) {
                    throw new \RuntimeException('listing_no_longer_active');
                }

                $fresh->update(['status' => MarketListingStatusEnum::CANCELLED]);

                $webStorage = WebStorage::create([
                    'user_id' => $user->id,
                    'character_id' => $listing->character_id,
                    'character_name' => $listing->character_name,
                    'item_id64' => $listing->item_id64,
                    'ref_item_id' => $listing->ref_item_id,
                    'item_name' => $listing->item_name,
                    'source_type' => 'inventory',
                    'opt_level' => $listing->opt_level,
                    'quantity' => $listing->quantity,
                    'item_data' => $listing->item_data,
                ]);
            });

            event(new ListingCancelled($user, $listing, $webStorage));
            return ['success' => true, 'error' => null, 'web_storage' => $webStorage];
        } catch (Throwable $e) {
            $msg = $e->getMessage();
            report($e);
            return ['success' => false, 'error' => match (true) {
                str_contains($msg, 'listing_no_longer_active') => 'listing_not_active',
                default => 'unexpected_error',
            }, 'web_storage' => null];
        }
    }

    /**
     * Process all expired active listings — returns them to web storage.
     */
    public function processExpiredListings(): int
    {
        $expired = MarketListing::expired()->lockForUpdate()->get();
        $count = 0;

        foreach ($expired as $listing) {
            try {
                DB::transaction(function () use ($listing, &$count) {
                    $listing->update(['status' => MarketListingStatusEnum::EXPIRED]);

                    $webStorage = WebStorage::create([
                        'user_id' => $listing->user_id,
                        'character_id' => $listing->character_id,
                        'character_name' => $listing->character_name,
                        'item_id64' => $listing->item_id64,
                        'ref_item_id' => $listing->ref_item_id,
                        'item_name' => $listing->item_name,
                        'source_type' => 'inventory',
                        'opt_level' => $listing->opt_level,
                        'quantity' => $listing->quantity,
                        'item_data' => $listing->item_data,
                    ]);

                    event(new ListingExpired($listing, $webStorage));
                    $count++;
                });
            } catch (Throwable $e) {
                report($e);
            }
        }

        return $count;
    }

    /**
     * @return array{0: string|null, 1: int}
     */
    private function calculateFee(int $priceAmount): array
    {
        $feeType = Setting::get('web_market_fee_type');
        $feeValue = Setting::get('web_market_fee_value');

        if (blank($feeType) || blank($feeValue)) {
            return [null, 0];
        }

        $feeAmount = match ($feeType) {
            MarketFeeTypeEnum::PERCENT->value => (int) round($priceAmount * ((float) $feeValue / 100)),
            MarketFeeTypeEnum::FIXED->value => (int) $feeValue,
            default => 0,
        };

        return [$feeType, max(0, min($feeAmount, $priceAmount))];
    }

    private function isPriceTypeAllowed(string $priceType): bool
    {
        if ($priceType === 'gold') {
            return (bool) Setting::get('web_market_allow_gold', true);
        }

        if (! (bool) Setting::get('web_market_allow_silk', true)) {
            return false;
        }

        $allowedSilkType = Setting::get('web_market_silk_type');

        // Guard against legacy array values (from old ->multiple() select)
        if (is_array($allowedSilkType)) {
            return in_array($priceType, $allowedSilkType, strict: true);
        }

        if (! blank($allowedSilkType)) {
            return (string) $allowedSilkType === $priceType;
        }

        return true;
    }

    private function getMaxPriceForType(string $priceType): ?int
    {
        if ($priceType === 'gold') {
            $max = Setting::get('web_market_max_gold_price');
            return blank($max) ? null : (int) $max;
        }

        $max = Setting::get('web_market_max_silk_price');
        return blank($max) ? null : (int) $max;
    }
}
