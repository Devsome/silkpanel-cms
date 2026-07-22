<?php

namespace App\Services;

use App\Enums\WebmallItemTypeEnum;
use App\Enums\SilkTypeEnum;
use App\Enums\SilkTypeIsroEnum;
use App\Helpers\SilkHelper;
use App\Models\ProcedureMapping;
use App\Models\User;
use App\Models\WebmallCategoryItem;
use App\Models\WebmallPurchase;
use Illuminate\Support\Facades\DB;
use Throwable;

class WebmallPurchaseService
{
    public function __construct(
        private readonly SilkroadItemService $itemService,
        private readonly ProcedureManager $procedureManager,
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

        if ($item->isCustomItem()) {
            return $this->purchaseCustomItem($user, $characterId, $characterName, $item);
        }

        $procedureResult = $this->procedureManager->execute(
            actionKey: 'webmall.buy_item',
            params: [
                'player_id' => config('silkpanel.version') === 'isro' ? (int) $user->pjid : (int) $user->jid,
                'character_id' => $characterId,
                'item_id' => $item->ref_item_id,
                'amount' => max(1, (int) $item->amount),
                'price_type' => $priceType,
                'price_amount' => $item->price_value,
            ],
            context: [
                'user_id' => $user->id,
                'character_name' => $characterName,
                'webmall_category_item_id' => $item->id,
            ],
        );

        if ($procedureResult['handled']) {
            if ($procedureResult['success']) {
                $this->persistPurchaseRecord($user, $characterId, $characterName, $item);

                return [
                    'success' => true,
                    'error' => null,
                    'destination' => 'Custom Procedure',
                ];
            }

            if (! $procedureResult['fallback']) {
                return ['success' => false, 'error' => 'custom_procedure_failed', 'destination' => null];
            }
        }

        // --- Check balance ---
        $balanceCheck = $this->checkBalance($user, $characterId, $priceType, $item->price_value);
        if (! $balanceCheck) {
            return ['success' => false, 'error' => 'insufficient_balance', 'destination' => null];
        }

        try {
            DB::transaction(function () use ($user, $characterId, $characterName, $item, $priceType, &$deliveryResult) {
                // 1. Deduct payment
                $this->deductPayment($user, $characterId, $priceType, $item->price_value);

                // 2. Deliver item
                $isIsro = config('silkpanel.version') === 'isro';
                $deliveryAmount = max(1, (int) $item->amount);
                $deliveryResult = $isIsro
                    ? $this->itemService->addItemIsro(charName: null, charId: $characterId, codeName: null, refItemId: $item->ref_item_id, data: $deliveryAmount)
                    : $this->itemService->addItemVsro(charName: null, charId: $characterId, codeName: null, refItemId: $item->ref_item_id, data: $deliveryAmount);

                if (!$deliveryResult['success']) {
                    throw new \RuntimeException('item_delivery_failed:' . $deliveryResult['return_code']);
                }

                // 3/4. Persist local sold counter and purchase log.
                $this->persistPurchaseRecord($user, $characterId, $characterName, $item);
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

    /**
     * @return array{success: bool, error: string|null, destination: string|null}
     */
    private function purchaseCustomItem(
        User $user,
        int $characterId,
        string $characterName,
        WebmallCategoryItem $item,
    ): array {
        $procedureName = trim((string) ($item->custom_procedure_name ?? ''));
        $databaseConnection = trim((string) ($item->custom_database_connection ?? ''));

        if ($procedureName === '' || $databaseConnection === '') {
            return ['success' => false, 'error' => 'custom_procedure_not_configured', 'destination' => null];
        }

        $priceType = $item->price_type;
        $balanceCheck = $this->checkBalance($user, $characterId, $priceType, $item->price_value);
        if (! $balanceCheck) {
            return ['success' => false, 'error' => 'insufficient_balance', 'destination' => null];
        }

        try {
            $purchase = DB::transaction(function () use ($user, $characterId, $characterName, $item, $priceType) {
                $this->deductPayment($user, $characterId, $priceType, $item->price_value);

                return $this->persistPurchaseRecord(
                    user: $user,
                    characterId: $characterId,
                    characterName: $characterName,
                    item: $item,
                    status: 'pending',
                    procedureMapping: null,
                );
            });

            $isIsro = config('silkpanel.version') === 'isro';
            $amount = (string) max(1, (int) $item->amount);
            $codename = (string) ($item->item_name_snapshot ?? ('Custom Item #' . $item->id));

            $procedureParameters = $this->procedureManager->listProcedureParameters($databaseConnection, $procedureName);
            $parameterLookup = collect($procedureParameters)
                ->mapWithKeys(fn(string $param) => [mb_strtolower($param) => $param])
                ->all();

            $optionalParameters = [];
            if (is_array($item->custom_parameters ?? null)) {
                foreach ($item->custom_parameters as $key => $value) {
                    if (! is_string($key) || trim($key) === '') {
                        continue;
                    }

                    $normalizedKey = str_starts_with($key, '@') ? $key : ('@' . $key);
                    $optionalParameters[mb_strtolower($normalizedKey)] = $this->resolveCustomParameterValue(
                        value: $value,
                        user: $user,
                        characterName: $characterName,
                        item: $item,
                        amount: $amount,
                        isIsro: $isIsro,
                    );
                }
            }

            $namedParams = [];

            if ($parameterLookup !== []) {
                foreach ($parameterLookup as $lookupKey => $originalParam) {
                    $value = $optionalParameters[$lookupKey] ?? null;

                    if ($value === null) {
                        continue;
                    }

                    $namedParams[$originalParam] = $value;
                }
            } else {
                $namedParams = $optionalParameters;
            }

            $procedureResult = $this->procedureManager->executeDirect(
                action: 'webmall.buy_custom_item',
                procedureName: $procedureName,
                databaseConnection: $databaseConnection,
                namedParams: $namedParams,
                context: [
                    'user_id' => $user->id,
                    'player_id' => $isIsro ? (int) $user->pjid : (int) $user->jid,
                    'character_id' => $characterId,
                    'character_name' => $characterName,
                    'webmall_category_item_id' => $item->id,
                    'webmall_purchase_id' => $purchase->id,
                    'item_type' => WebmallItemTypeEnum::CUSTOM_ITEM->value,
                ],
            );

            if (! $procedureResult['handled'] || ! $procedureResult['success']) {
                $purchase->forceFill([
                    'status' => 'failed',
                    'procedure_name_snapshot' => $procedureName,
                    'procedure_log_id' => $procedureResult['log_id'] ?? null,
                    'procedure_error_message' => (string) ($procedureResult['message'] ?? 'custom_procedure_failed'),
                ])->save();

                return ['success' => false, 'error' => 'custom_procedure_failed', 'destination' => null];
            }

            $purchase->forceFill([
                'status' => 'completed',
                'procedure_name_snapshot' => $procedureName,
                'procedure_log_id' => $procedureResult['log_id'] ?? null,
                'procedure_error_message' => null,
            ])->save();

            return [
                'success' => true,
                'error' => null,
                'destination' => $procedureName,
            ];
        } catch (Throwable $e) {
            report($e);

            return ['success' => false, 'error' => 'unexpected_error', 'destination' => null];
        }
    }

    private function resolveCustomParameterValue(
        mixed $value,
        User $user,
        string $characterName,
        WebmallCategoryItem $item,
        string $amount,
        bool $isIsro,
    ): mixed {
        if (! is_string($value)) {
            return $value;
        }

        return match (trim($value)) {
            '{user_id}' => (string) $user->id,
            '{jid}' => (string) $user->jid,
            '{pjid}' => (string) ($user->pjid ?? ''),
            '{username}' => (string) ($user->silkroad_id ?? $user->name ?? ''),
            '{account_name}' => (string) ($user->silkroad_id ?? $user->name ?? ''),
            '{jid_or_pjid}' => (string) ($isIsro ? ($user->pjid ?? '') : $user->jid),
            '{charname}' => $characterName,
            '{codename}' => (string) ($item->item_name_snapshot ?? ('Custom Item #' . $item->id)),
            '{amount}' => $amount,
            default => $value,
        };
    }

    private function persistPurchaseRecord(
        User $user,
        int $characterId,
        string $characterName,
        WebmallCategoryItem $item,
        string $status = 'completed',
        ?ProcedureMapping $procedureMapping = null,
    ): WebmallPurchase {
        $item->increment('sold');

        return WebmallPurchase::create([
            'user_id' => $user->id,
            'character_id' => $characterId,
            'character_name' => $characterName,
            'webmall_category_item_id' => $item->id,
            'item_type' => $item->item_type,
            'ref_item_id' => $item->ref_item_id,
            'item_name' => $item->item_name_snapshot ?? $item->ref_item_id,
            'price_type' => $item->price_type,
            'price_value' => $item->price_value,
            'amount' => max(1, (int) $item->amount),
            'status' => $status,
            'procedure_mapping_id' => $procedureMapping?->id,
            'procedure_name_snapshot' => $procedureMapping?->procedure_name,
        ]);
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
