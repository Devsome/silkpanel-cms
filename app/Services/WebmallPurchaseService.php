<?php

namespace App\Services;

use App\Enums\SilkTypeEnum;
use App\Enums\SilkTypeIsroEnum;
use App\Helpers\SilkHelper;
use App\Models\User;
use App\Models\WebmallCategoryItem;
use App\Models\WebmallPurchase;
use Illuminate\Support\Facades\DB;
use Throwable;

class WebmallPurchaseService
{
    public function __construct(
        private readonly SilkroadItemService $itemService,
    ) {}

    /**
     * Process a webmall purchase.
     *
     * @return array{success: bool, error: string|null, destination: string|null}
     */
    public function purchase(
        User $user,
        int $characterId,
        string $characterName,
        WebmallCategoryItem $item,
    ): array {
        $priceType = $item->price_type;

        // --- Validate availability ---
        if (!$item->isAvailable()) {
            return ['success' => false, 'error' => 'item_unavailable', 'destination' => null];
        }

        // --- Check balance ---
        $balanceCheck = $this->checkBalance($user, $characterId, $priceType, $item->price_value);
        if (!$balanceCheck) {
            return ['success' => false, 'error' => 'insufficient_balance', 'destination' => null];
        }

        try {
            DB::transaction(function () use ($user, $characterId, $characterName, $item, $priceType, &$deliveryResult) {
                // 1. Deduct payment
                $this->deductPayment($user, $characterId, $priceType, $item->price_value);

                // 2. Deliver item
                $isIsro = config('silkpanel.version') === 'isro';
                $deliveryResult = $isIsro
                    ? $this->itemService->addItemIsro(charName: null, charId: $characterId, codeName: null, refItemId: $item->ref_item_id)
                    : $this->itemService->addItemVsro(charName: null, charId: $characterId, codeName: null, refItemId: $item->ref_item_id);

                if (!$deliveryResult['success']) {
                    throw new \RuntimeException('item_delivery_failed:' . $deliveryResult['return_code']);
                }

                // 3. Increment sold counter
                $item->increment('sold');

                // 4. Log purchase
                WebmallPurchase::create([
                    'user_id'                  => $user->id,
                    'character_id'             => $characterId,
                    'character_name'           => $characterName,
                    'webmall_category_item_id' => $item->id,
                    'ref_item_id'              => $item->ref_item_id,
                    'item_name'                => $item->item_name_snapshot ?? $item->ref_item_id,
                    'price_type'               => $item->price_type,
                    'price_value'              => $item->price_value,
                ]);
            });

            return [
                'success'     => true,
                'error'       => null,
                'destination' => $deliveryResult['destination'] ?? null,
            ];
        } catch (Throwable $e) {
            $msg = $e->getMessage();
            if (str_starts_with($msg, 'item_delivery_failed:')) {
                return ['success' => false, 'error' => 'item_delivery_failed', 'destination' => null];
            }

            report($e);
            return ['success' => false, 'error' => 'unexpected_error', 'destination' => null];
        }
    }

    private function checkBalance(User $user, int $characterId, string $priceType, int $amount): bool
    {
        if ($priceType === 'gold') {
            $char = $user->shardUsers()->wherePivot('CharID', $characterId)->first();
            if (!$char) {
                return false;
            }
            return ((int) $char->RemainGold) >= $amount;
        }

        $isIsro = config('silkpanel.version') === 'isro';

        if ($isIsro) {
            $jcash = $user->muuser?->JCash;
            if (!$jcash) {
                return false;
            }
            return match (SilkTypeIsroEnum::tryFrom((int) $priceType)) {
                SilkTypeIsroEnum::SILK_TYPE_NORMAL  => ((int) $jcash->Silk) >= $amount,
                SilkTypeIsroEnum::SILK_TYPE_PREMIUM => ((int) $jcash->PremiumSilk) >= $amount,
                default                             => false,
            };
        }

        $skSilk = $user->getSkSilk;
        if (!$skSilk) {
            return false;
        }

        return match (SilkTypeEnum::tryFrom($priceType)) {
            SilkTypeEnum::SILK_OWN   => ((int) $skSilk->silk_own) >= $amount,
            SilkTypeEnum::SILK_GIFT  => ((int) $skSilk->silk_gift) >= $amount,
            SilkTypeEnum::SILK_POINT => ((int) $skSilk->silk_point) >= $amount,
            default                  => false,
        };
    }

    private function deductPayment(User $user, int $characterId, string $priceType, int $amount): void
    {
        if ($priceType === 'gold') {
            $char = $user->shardUsers()->wherePivot('CharID', $characterId)->first();
            if ($char) {
                $char->decrement('RemainGold', $amount);
            }
            return;
        }

        $isIsro = config('silkpanel.version') === 'isro';
        // ISRO silk is stored in the portal DB keyed by pjid; VSRO uses the account jid.
        $jid = $isIsro ? (int) $user->pjid : (int) $user->jid;
        SilkHelper::addSilk($jid, -$amount, $priceType, request()->ip());
    }
}
