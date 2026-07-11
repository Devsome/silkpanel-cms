<?php

namespace App\Services;

use App\Enums\MarketListingStatusEnum;
use App\Enums\SilkTypeEnum;
use App\Enums\SilkTypeIsroEnum;
use App\Events\WebMarket\ListingSold;
use App\Helpers\SilkHelper;
use App\Models\MarketListing;
use App\Models\MarketTransaction;
use App\Models\Setting;
use App\Models\User;
use App\Models\WebStorage;
use Illuminate\Support\Facades\DB;
use Throwable;

class MarketPurchaseService
{
    /**
     * Purchase a marketplace listing.
     *
     * @return array{success: bool, error: string|null}
     */
    public function purchase(
        User $buyer,
        int $buyerCharId,
        string $buyerCharName,
        MarketListing $listing,
    ): array {
        if (! (bool) Setting::get('marketplace_enabled', false)) {
            return ['success' => false, 'error' => 'marketplace_disabled'];
        }

        // Prevent buying own listing
        if ($listing->user_id === $buyer->id) {
            return ['success' => false, 'error' => 'cannot_buy_own_listing'];
        }

        if (! $listing->isActive()) {
            return ['success' => false, 'error' => 'listing_not_active'];
        }

        if ($listing->isExpired()) {
            return ['success' => false, 'error' => 'listing_expired'];
        }

        if (! $this->buyerOwnsCharacter($buyer, $buyerCharId)) {
            return ['success' => false, 'error' => 'character_not_owned'];
        }

        if (! $this->checkBalance($buyer, $buyerCharId, $listing->price_type, $listing->price_amount)) {
            return ['success' => false, 'error' => 'insufficient_balance'];
        }

        $seller = User::find($listing->user_id);
        if (! $seller) {
            return ['success' => false, 'error' => 'seller_not_found'];
        }

        try {
            $transaction = null;

            DB::transaction(function () use ($buyer, $buyerCharId, $buyerCharName, $listing, $seller, &$transaction) {
                // Re-lock listing to prevent race conditions
                $fresh = MarketListing::where('id', $listing->id)
                    ->where('status', MarketListingStatusEnum::ACTIVE)
                    ->lockForUpdate()
                    ->first();

                if (! $fresh || $fresh->isExpired()) {
                    throw new \RuntimeException('listing_not_available');
                }

                // Deduct buyer payment
                $this->deductPayment($buyer, $buyerCharId, $listing->price_type, $listing->price_amount);

                // Credit seller (net amount after fees)
                $netAmount = $listing->netAmount();
                if ($netAmount > 0) {
                    $this->creditPayment($seller, $listing->price_type, $netAmount);
                }

                // Mark listing as sold
                $fresh->update(['status' => MarketListingStatusEnum::SOLD]);

                // Remove item from seller's web storage (prevent dupe)
                WebStorage::where('item_id64', $listing->item_id64)
                    ->where('user_id', $listing->user_id)
                    ->delete();

                // Create web storage entry for buyer
                WebStorage::create([
                    'user_id' => $buyer->id,
                    'character_id' => $buyerCharId,
                    'character_name' => $buyerCharName,
                    'item_id64' => $listing->item_id64,
                    'ref_item_id' => $listing->ref_item_id,
                    'item_name' => $listing->item_name,
                    'source_type' => 'inventory',
                    'opt_level' => $listing->opt_level,
                    'quantity' => $listing->quantity,
                    'item_data' => $listing->item_data,
                ]);

                // Record transaction
                $transaction = MarketTransaction::create([
                    'listing_id' => $listing->id,
                    'seller_id' => $seller->id,
                    'buyer_id' => $buyer->id,
                    'seller_character_id' => $listing->character_id,
                    'buyer_character_id' => $buyerCharId,
                    'seller_character_name' => $listing->character_name,
                    'buyer_character_name' => $buyerCharName,
                    'ref_item_id' => $listing->ref_item_id,
                    'item_name' => $listing->item_name,
                    'opt_level' => $listing->opt_level,
                    'quantity' => $listing->quantity,
                    'item_data' => $listing->item_data,
                    'price_type' => $listing->price_type,
                    'price_amount' => $listing->price_amount,
                    'fee_type' => $listing->fee_type,
                    'fee_amount' => $listing->fee_amount,
                    'net_amount' => $netAmount,
                ]);
            });

            event(new ListingSold($buyer, $seller, $listing, $transaction));
            return ['success' => true, 'error' => null];
        } catch (Throwable $e) {
            $msg = $e->getMessage();
            report($e);
            return ['success' => false, 'error' => match (true) {
                str_contains($msg, 'listing_not_available') => 'listing_not_active',
                default => 'unexpected_error',
            }];
        }
    }

    private function checkBalance(User $user, int $charId, string $priceType, int $amount): bool
    {
        if ($priceType === 'gold') {
            $char = $user->shardUsers()->wherePivot('CharID', $charId)->first();
            return $char && ((int) $char->RemainGold) >= $amount;
        }

        $isIsro = config('silkpanel.version') === 'isro';

        if ($isIsro) {
            $jcash = $user->muuser?->JCash;
            if (! $jcash) {
                return false;
            }
            return match (SilkTypeIsroEnum::tryFrom((int) $priceType)) {
                SilkTypeIsroEnum::SILK_TYPE_NORMAL  => ((int) $jcash->Silk) >= $amount,
                SilkTypeIsroEnum::SILK_TYPE_PREMIUM => ((int) $jcash->PremiumSilk) >= $amount,
                default                             => false,
            };
        }

        $skSilk = $user->getSkSilk;
        if (! $skSilk) {
            return false;
        }

        return match (SilkTypeEnum::tryFrom($priceType)) {
            SilkTypeEnum::SILK_OWN   => ((int) $skSilk->silk_own) >= $amount,
            SilkTypeEnum::SILK_GIFT  => ((int) $skSilk->silk_gift) >= $amount,
            SilkTypeEnum::SILK_POINT => ((int) $skSilk->silk_point) >= $amount,
            default                  => false,
        };
    }

    private function deductPayment(User $user, int $charId, string $priceType, int $amount): void
    {
        if ($priceType === 'gold') {
            $char = $user->shardUsers()->wherePivot('CharID', $charId)->first();
            if ($char) {
                $char->decrement('RemainGold', $amount);
            }
            return;
        }

        $isIsro = config('silkpanel.version') === 'isro';
        $jid = $isIsro ? (int) $user->pjid : (int) $user->jid;
        SilkHelper::addSilk($jid, -$amount, $priceType, request()->ip());
    }

    private function creditPayment(User $seller, string $priceType, int $amount): void
    {
        if ($priceType === 'gold') {
            // Credit gold to seller's most-leveled character
            $char = $seller->shardUsers()
                ->orderByDesc('CurLevel')
                ->first();
            if ($char) {
                $char->increment('RemainGold', $amount);
            }
            return;
        }

        $isIsro = config('silkpanel.version') === 'isro';
        $jid = $isIsro ? (int) $seller->pjid : (int) $seller->jid;
        SilkHelper::addSilk($jid, $amount, $priceType, request()->ip());
    }

    private function buyerOwnsCharacter(User $user, int $charId): bool
    {
        return $user->shardUsers()->wherePivot('CharID', $charId)->exists();
    }
}
