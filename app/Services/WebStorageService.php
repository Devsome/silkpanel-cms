<?php

namespace App\Services;

use App\Actions\WebMarket\TransferFromWebStorageAction;
use App\Actions\WebMarket\TransferToWebStorageAction;
use App\Enums\DatabaseNameEnums;
use App\Enums\WebStorageSourceTypeEnum;
use App\Events\WebMarket\ItemTransferredFromStorage;
use App\Events\WebMarket\ItemTransferredToStorage;
use App\Models\Setting;
use App\Models\User;
use App\Models\WebStorage;
use Illuminate\Support\Facades\DB;
use SilkPanel\SilkroadModels\Models\Shard\Inventory;
use Throwable;

class WebStorageService
{
    private const INVENTORY_SLOT_MIN = 13;

    public function __construct(
        private readonly ProcedureManager $procedureManager,
    ) {}

    /**
     * Transfer an item from character inventory/storage to web storage.
     *
     * @return array{success: bool, error: string|null, web_storage: WebStorage|null}
     */
    public function transferToWebStorage(
        User $user,
        int $charId,
        string $charName,
        int $slot,
        WebStorageSourceTypeEnum $sourceType,
    ): array {
        if (! (bool) Setting::get('web_storage_enabled', false)) {
            return ['success' => false, 'error' => 'web_storage_disabled', 'web_storage' => null];
        }

        if (! $this->userOwnsCharacter($user, $charId)) {
            return ['success' => false, 'error' => 'character_not_owned', 'web_storage' => null];
        }

        if ((bool) Setting::get('web_market_require_logout', false)) {
            if ($this->isCharacterOnline($user, $charId)) {
                return ['success' => false, 'error' => 'character_must_be_offline', 'web_storage' => null];
            }
        }

        $maxItems = (int) Setting::get('web_market_max_storage_items', 50);
        $currentCount = WebStorage::where('user_id', $user->id)->count();
        if ($currentCount >= $maxItems) {
            return ['success' => false, 'error' => 'storage_limit_reached', 'web_storage' => null];
        }

        $connection = DatabaseNameEnums::SRO_SHARD->value;

        try {
            if ($sourceType === WebStorageSourceTypeEnum::INVENTORY) {
                $inventoryRow = DB::connection($connection)->selectOne(
                    'SELECT ItemID FROM dbo._Inventory WHERE CharID = ? AND Slot = ? AND ItemID > 0',
                    [$charId, $slot]
                );
            } else {
                // STORAGE → _Chest is keyed by UserJID, not CharID
                $userJid = $this->getUserJid($connection, $charId);
                if ($userJid === null) {
                    return ['success' => false, 'error' => 'character_not_owned', 'web_storage' => null];
                }
                $inventoryRow = DB::connection($connection)->selectOne(
                    'SELECT ItemID FROM dbo._Chest WHERE UserJID = ? AND Slot = ? AND ItemID > 0',
                    [$userJid, $slot]
                );
            }

            if (! $inventoryRow) {
                return ['success' => false, 'error' => 'item_not_found', 'web_storage' => null];
            }

            $itemId64 = (int) $inventoryRow->ItemID;

            $itemRow = DB::connection($connection)->selectOne(
                'SELECT it.ID64, it.RefItemId, it.Data, it.OptLevel, it.Variance,
                        roc.CodeName128, roc.AssocFileIcon128, roc.TypeID2, roc.TypeID3, roc.ReqLevel1, roc.NameStrID128, roc.CanTrade
                 FROM dbo._Items it
                 JOIN dbo._RefObjCommon roc ON roc.ID = it.RefItemId
                 WHERE it.ID64 = ?',
                [$itemId64]
            );

            if (! $itemRow) {
                return ['success' => false, 'error' => 'item_data_not_found', 'web_storage' => null];
            }

            if (! (bool) ($itemRow->CanTrade ?? false)) {
                return ['success' => false, 'error' => 'item_not_tradeable', 'web_storage' => null];
            }

            $isIsro = config('silkpanel.version') === 'isro';
            $playerId = $isIsro ? (int) $user->pjid : (int) $user->jid;

            $procedureResult = $this->procedureManager->execute(
                actionKey: TransferToWebStorageAction::ACTION_KEY,
                params: [
                    'player_id' => $playerId,
                    'character_id' => $charId,
                    'item_id64' => $itemId64,
                    'source_slot' => $slot,
                    'source_type' => $sourceType->value,
                ],
                context: ['user_id' => $user->id, 'character_name' => $charName],
            );

            $webStorage = null;

            if ($procedureResult['handled']) {
                if (! $procedureResult['success'] && ! $procedureResult['fallback']) {
                    return ['success' => false, 'error' => 'procedure_failed', 'web_storage' => null];
                }

                if ($procedureResult['success']) {
                    $webStorage = $this->createWebStorageRecord($user, $charId, $charName, $itemId64, $itemRow, $sourceType);
                    $this->forgetInventoryCache($charId);
                    event(new ItemTransferredToStorage($user, $webStorage));
                    return ['success' => true, 'error' => null, 'web_storage' => $webStorage];
                }
            }

            // Default implementation: direct DB manipulation
            DB::connection($connection)->transaction(function () use ($connection, $charId, $slot, $itemId64, $user, $charName, $itemRow, $sourceType, &$webStorage) {
                if ($sourceType === WebStorageSourceTypeEnum::INVENTORY) {
                    $locked = DB::connection($connection)->selectOne(
                        'SELECT ItemID FROM dbo._Inventory WITH (UPDLOCK, ROWLOCK) WHERE CharID = ? AND Slot = ? AND ItemID = ?',
                        [$charId, $slot, $itemId64]
                    );
                    if (! $locked) {
                        throw new \RuntimeException('item_slot_changed');
                    }
                    // Set to 0 so the slot remains in the table for game-state consistency
                    DB::connection($connection)->statement(
                        'UPDATE dbo._Inventory SET ItemID = 0 WHERE CharID = ? AND Slot = ?',
                        [$charId, $slot]
                    );
                } else {
                    // _Chest: DELETE the row — chest rows only exist when occupied
                    $userJid = $this->getUserJid($connection, $charId);
                    $locked = DB::connection($connection)->selectOne(
                        'SELECT ItemID FROM dbo._Chest WITH (UPDLOCK, ROWLOCK) WHERE UserJID = ? AND Slot = ? AND ItemID = ?',
                        [$userJid, $slot, $itemId64]
                    );
                    if (! $locked) {
                        throw new \RuntimeException('item_slot_changed');
                    }
                    DB::connection($connection)->statement(
                        'DELETE FROM dbo._Chest WHERE UserJID = ? AND Slot = ?',
                        [$userJid, $slot]
                    );
                }

                $webStorage = $this->createWebStorageRecord($user, $charId, $charName, $itemId64, $itemRow, $sourceType);
            });

            $this->forgetInventoryCache($charId);
            event(new ItemTransferredToStorage($user, $webStorage));
            return ['success' => true, 'error' => null, 'web_storage' => $webStorage];
        } catch (Throwable $e) {
            $msg = $e->getMessage();
            report($e);
            return ['success' => false, 'error' => match (true) {
                str_contains($msg, 'item_slot_changed') => 'item_slot_changed',
                default => 'unexpected_error',
            }, 'web_storage' => null];
        }
    }

    /**
     * Transfer an item from web storage back to character inventory/storage.
     *
     * @return array{success: bool, error: string|null}
     */
    public function transferFromWebStorage(
        User $user,
        int $charId,
        string $charName,
        WebStorage $webStorageItem,
        WebStorageSourceTypeEnum $targetType,
    ): array {
        if (! (bool) Setting::get('web_storage_enabled', false)) {
            return ['success' => false, 'error' => 'web_storage_disabled'];
        }

        if ($webStorageItem->user_id !== $user->id) {
            return ['success' => false, 'error' => 'item_not_owned'];
        }

        if (! $this->userOwnsCharacter($user, $charId)) {
            return ['success' => false, 'error' => 'character_not_owned'];
        }

        if ($webStorageItem->isListed()) {
            return ['success' => false, 'error' => 'item_is_listed'];
        }

        if ((bool) Setting::get('web_market_require_logout', false)) {
            if ($this->isCharacterOnline($user, $charId)) {
                return ['success' => false, 'error' => 'character_must_be_offline'];
            }
        }

        $connection = DatabaseNameEnums::SRO_SHARD->value;

        $isIsro = config('silkpanel.version') === 'isro';
        $playerId = $isIsro ? (int) $user->pjid : (int) $user->jid;

        try {
            $procedureResult = $this->procedureManager->execute(
                actionKey: TransferFromWebStorageAction::ACTION_KEY,
                params: [
                    'player_id' => $playerId,
                    'character_id' => $charId,
                    'item_id64' => $webStorageItem->item_id64,
                    'target_type' => $targetType->value,
                ],
                context: ['user_id' => $user->id, 'web_storage_id' => $webStorageItem->id],
            );

            if ($procedureResult['handled']) {
                if (! $procedureResult['success'] && ! $procedureResult['fallback']) {
                    return ['success' => false, 'error' => 'procedure_failed'];
                }

                if ($procedureResult['success']) {
                    $webStorageItem->delete();
                    $this->forgetInventoryCache($charId);
                    event(new ItemTransferredFromStorage($user, $webStorageItem, $targetType->value));
                    return ['success' => true, 'error' => null];
                }
            }

            // Default: try inventory (_Inventory) first, then chest (_Chest).
            // _Inventory: only UPDATE existing rows with ItemID=0 — the game pre-initialises all
            //   bag slots, and inserting unknown rows corrupts character state on login.
            // _Chest: rows only exist when occupied (no pre-initialised empty rows), so we need
            //   a CTE gap-finder and then INSERT or UPDATE as appropriate.
            $placedType = null;

            DB::connection($connection)->transaction(function () use ($connection, $charId, $webStorageItem, &$placedType) {
                $result = $this->findBestSlot($connection, $charId);

                if ($result === null) {
                    throw new \RuntimeException('no_empty_slot');
                }

                $emptySlot = $result['slot'];
                $placedType = $result['type'];

                if ($placedType === WebStorageSourceTypeEnum::INVENTORY) {
                    $affected = DB::connection($connection)->affectingStatement(
                        'UPDATE dbo._Inventory SET ItemID = ? WHERE CharID = ? AND Slot = ? AND ItemID = 0',
                        [$webStorageItem->item_id64, $charId, $emptySlot]
                    );
                    if ($affected === 0) {
                        throw new \RuntimeException('no_empty_slot');
                    }
                } else {
                    // _Chest: the slot might exist (ItemID=0 from a previous clear) or be a new gap
                    $userJid = $this->getUserJid($connection, $charId);
                    $existingChestRow = DB::connection($connection)->selectOne(
                        'SELECT Slot FROM dbo._Chest WITH (UPDLOCK, ROWLOCK) WHERE UserJID = ? AND Slot = ?',
                        [$userJid, $emptySlot]
                    );

                    if ($existingChestRow) {
                        $affected = DB::connection($connection)->affectingStatement(
                            'UPDATE dbo._Chest SET ItemID = ? WHERE UserJID = ? AND Slot = ? AND (ItemID = 0 OR ItemID IS NULL)',
                            [$webStorageItem->item_id64, $userJid, $emptySlot]
                        );
                        if ($affected === 0) {
                            throw new \RuntimeException('no_empty_slot');
                        }
                    } else {
                        // New slot — game also inserts on first deposit into a slot
                        DB::connection($connection)->statement(
                            'INSERT INTO dbo._Chest (UserJID, Slot, ItemID) VALUES (?, ?, ?)',
                            [$userJid, $emptySlot, $webStorageItem->item_id64]
                        );
                    }
                }

                $webStorageItem->delete();
            });

            $usedType = $placedType ?? $targetType;
            $this->forgetInventoryCache($charId);
            event(new ItemTransferredFromStorage($user, $webStorageItem, $usedType->value));
            return ['success' => true, 'error' => null];
        } catch (Throwable $e) {
            $msg = $e->getMessage();
            report($e);
            return ['success' => false, 'error' => match (true) {
                str_contains($msg, 'no_empty_slot') => 'no_empty_slot',
                default => 'unexpected_error',
            }];
        }
    }

    /**
     * Create a WebStorage record from a raw item DB row.
     */
    private function createWebStorageRecord(
        User $user,
        int $charId,
        string $charName,
        int $itemId64,
        object $itemRow,
        WebStorageSourceTypeEnum $sourceType,
    ): WebStorage {
        return WebStorage::create([
            'user_id' => $user->id,
            'character_id' => $charId,
            'character_name' => $charName,
            'item_id64' => $itemId64,
            'ref_item_id' => (int) ($itemRow->RefItemId ?? 0),
            'item_name' => (string) ($itemRow->CodeName128 ?? ''),
            'source_type' => $sourceType,
            'opt_level' => (int) ($itemRow->OptLevel ?? 0),
            'quantity' => 1,
            'item_data' => [
                'code_name' => $itemRow->CodeName128 ?? null,
                'icon' => $itemRow->AssocFileIcon128 ?? null,
                'type_id2' => $itemRow->TypeID2 ?? null,
                'type_id3' => $itemRow->TypeID3 ?? null,
                'req_level' => $itemRow->ReqLevel1 ?? null,
                'opt_level' => $itemRow->OptLevel ?? 0,
                'variance' => $itemRow->Variance ?? null,
                'data_hex' => $itemRow->Data ? bin2hex((string) $itemRow->Data) : null,
                'can_trade' => (bool) ($itemRow->CanTrade ?? false),
            ],
        ]);
    }

    private function forgetInventoryCache(int $charId): void
    {
        // Only the _Inventory (bag) is cached by the Eloquent Inventory model.
        // _Chest (character storage) is queried raw and has no ORM cache to bust.
        Inventory::forgetInventoryCache($charId, 110, self::INVENTORY_SLOT_MIN, null);
    }

    /**
     * Find the first empty slot — inventory bag first, then character chest.
     *
     * Inventory (_Inventory): only targets existing rows with ItemID=0. The game
     * pre-initialises all bag slots; we must not insert unknown rows.
     *
     * Chest (_Chest): rows only exist when occupied (empty slots are absent from
     * the table). Uses a CTE to find the first slot gap, matching the game's own
     * item-add procedures (_ADD_ITEM_SILKPANEL_AUTO_*).
     *
     * @return array{slot: int, type: WebStorageSourceTypeEnum}|null
     */
    private function findBestSlot(string $connection, int $charId): ?array
    {
        // ── 1. Inventory (_Inventory, slots 13 to InventorySize) ──
        $invSize = $this->getInventorySize($connection, $charId);
        $invSlot = DB::connection($connection)->selectOne(
            'SELECT TOP 1 Slot FROM dbo._Inventory WITH (UPDLOCK, ROWLOCK)
             WHERE CharID = ? AND Slot >= ? AND Slot < ? AND ItemID = 0
             ORDER BY Slot ASC',
            [$charId, self::INVENTORY_SLOT_MIN, $invSize]
        );

        if ($invSlot) {
            return ['slot' => (int) $invSlot->Slot, 'type' => WebStorageSourceTypeEnum::INVENTORY];
        }

        // ── 2. Chest (_Chest, keyed by UserJID) ──
        $userJid = $this->getUserJid($connection, $charId);
        if ($userJid === null) {
            return null;
        }

        $chestSize = $this->getChestSize($connection, $userJid);

        // CTE enumerates slot numbers 0..ChestSize-1 and finds the first gap —
        // i.e. a slot with no occupied row (ItemID > 0) in _Chest for this user.
        // This covers both "never-used" slots (no row) and "cleared" slots (ItemID=0/NULL).
        $chestSlot = DB::connection($connection)->selectOne(
            ';WITH ChestSlots AS (
                SELECT 0 AS Slot
                UNION ALL
                SELECT Slot + 1 FROM ChestSlots WHERE Slot + 1 < ?
            )
            SELECT TOP 1 ChestSlots.Slot
            FROM ChestSlots
            WHERE NOT EXISTS (
                SELECT 1 FROM dbo._Chest ch WITH (NOLOCK)
                WHERE ch.UserJID = ?
                  AND ch.Slot = ChestSlots.Slot
                  AND ch.ItemID IS NOT NULL
                  AND ch.ItemID <> 0
            )
            ORDER BY ChestSlots.Slot
            OPTION (MAXRECURSION 200)',
            [$chestSize, $userJid]
        );

        if ($chestSlot) {
            return ['slot' => (int) $chestSlot->Slot, 'type' => WebStorageSourceTypeEnum::STORAGE];
        }

        return null;
    }

    private function getInventorySize(string $connection, int $charId): int
    {
        $row = DB::connection($connection)->selectOne(
            'SELECT InventorySize FROM dbo._Char WITH (NOLOCK) WHERE CharID = ?',
            [$charId]
        );
        return (int) ($row?->InventorySize ?? 45);
    }

    private function getUserJid(string $connection, int $charId): ?int
    {
        $row = DB::connection($connection)->selectOne(
            'SELECT UserJID FROM dbo._User WITH (NOLOCK) WHERE CharID = ?',
            [$charId]
        );
        return $row ? (int) $row->UserJID : null;
    }

    private function getChestSize(string $connection, int $userJid): int
    {
        $row = DB::connection($connection)->selectOne(
            'SELECT ChestSize FROM dbo._ChestInfo WITH (NOLOCK) WHERE JID = ?',
            [$userJid]
        );
        return (int) ($row?->ChestSize ?? 150);
    }

    private function userOwnsCharacter(User $user, int $charId): bool
    {
        return $user->shardUsers()->wherePivot('CharID', $charId)->exists();
    }

    private function isCharacterOnline(User $user, int $charId): bool
    {
        try {
            $char = $user->shardUsers()->wherePivot('CharID', $charId)->first();
            return (bool) ($char?->isOnline ?? false);
        } catch (Throwable) {
            return false;
        }
    }
}
