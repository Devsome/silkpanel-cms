<?php

namespace App\Services;

use App\Enums\DatabaseNameEnums;
use App\Models\ItemLog;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Facades\Auth;
use PDO;
use Throwable;

class SilkroadItemService
{
    private const DESTINATION_LENGTH = 10;

    public function __construct(
        private readonly DatabaseManager $database,
    ) {}

    /**
     * @return array{success: bool, return_code: int, destination: string, slot: int, new_item_id: int}
     */
    public function addItemVsro(
        ?string $charName,
        ?int $charId,
        ?string $codeName,
        ?int $refItemId,
        int $data = 1,
        int $optLevel = 0,
    ): array {
        return $this->executeAddItemProcedure(
            procedureName: '_ADD_ITEM_SILKPANEL_AUTO_VSRO',
            charName: $charName,
            charId: $charId,
            codeName: $codeName,
            refItemId: $refItemId,
            data: $data,
            optLevel: $optLevel,
            variance: null,
        );
    }

    /**
     * @return array{success: bool, return_code: int, destination: string, slot: int, new_item_id: int}
     */
    public function addItemIsro(
        ?string $charName,
        ?int $charId,
        ?string $codeName,
        ?int $refItemId,
        int $data = 1,
        int $optLevel = 0,
        ?int $variance = null
    ): array {
        return $this->executeAddItemProcedure(
            procedureName: '_ADD_ITEM_SILKPANEL_AUTO_ISRO',
            charName: $charName,
            charId: $charId,
            codeName: $codeName,
            refItemId: $refItemId,
            data: $data,
            optLevel: $optLevel,
            variance: $variance,
        );
    }

    /**
     * @return array{success: bool, return_code: int, destination: string, slot: int, new_item_id: int}
     */
    private function executeAddItemProcedure(
        string $procedureName,
        ?string $charName,
        ?int $charId,
        ?string $codeName,
        ?int $refItemId,
        int $data,
        int $optLevel,
        ?int $variance,
    ): array {
        if (!$this->hasCharacterIdentifier($charName, $charId) || !$this->hasItemIdentifier($codeName, $refItemId)) {
            return $this->buildResult(returnCode: -2);
        }

        try {
            // Build the SQL Server-compatible parameterised query.
            // ODBC escape syntax {? = CALL ...} is not supported by pdo_sqlsrv / pdo_dblib.
            // Instead we declare local variables, EXEC into them and SELECT back the results.
            $withVariance = $variance !== null;

            $sql = $withVariance
                ? "DECLARE @_ret INT, @_dest VARCHAR(10) = '', @_slot INT = 0, @_itemId BIGINT = 0;
                   EXEC @_ret = [dbo].[{$procedureName}]
                       @CharName = ?, @CharID = ?, @CodeName = ?, @RefItemID = ?,
                       @Data = ?, @OptLevel = ?, @Variance = ?,
                       @Destination = @_dest OUTPUT, @Slot = @_slot OUTPUT, @NewItemID = @_itemId OUTPUT;
                   SELECT @_ret AS return_code, @_dest AS destination, @_slot AS slot, @_itemId AS new_item_id;"
                : "DECLARE @_ret INT, @_dest VARCHAR(10) = '', @_slot INT = 0, @_itemId BIGINT = 0;
                   EXEC @_ret = [dbo].[{$procedureName}]
                       @CharName = ?, @CharID = ?, @CodeName = ?, @RefItemID = ?,
                       @Data = ?, @OptLevel = ?,
                       @Destination = @_dest OUTPUT, @Slot = @_slot OUTPUT, @NewItemID = @_itemId OUTPUT;
                   SELECT @_ret AS return_code, @_dest AS destination, @_slot AS slot, @_itemId AS new_item_id;";

            $bindings = $withVariance
                ? [$charName, $charId, $codeName, $refItemId, $data, $optLevel, $variance]
                : [$charName, $charId, $codeName, $refItemId, $data, $optLevel];

            // Use raw PDO and iterate through all result sets with nextRowset().
            // Inner procedures called by the stored proc (e.g. _STRG_ADD_ITEM_INVENTORY_NoTX)
            // may produce their own result sets even when SET NOCOUNT ON is set on the outer
            // proc — NOCOUNT does not propagate to called procedures in SQL Server.
            // selectOne() only reads the first result set and would therefore return null.
            $pdo  = $this->database->connection(DatabaseNameEnums::SRO_SHARD->value)->getPdo();
            $stmt = $pdo->prepare($sql);
            $stmt->execute($bindings);

            $row = null;
            do {
                $rows = $stmt->fetchAll(PDO::FETCH_OBJ);
                if (!empty($rows) && property_exists($rows[0], 'return_code')) {
                    $row = $rows[0];
                    break;
                }
            } while ($stmt->nextRowset());

            $returnCode  = (int) ($row->return_code  ?? -1);
            $destination = (string) ($row->destination ?? '');
            $slot        = (int) ($row->slot           ?? 0);
            $newItemId   = (int) ($row->new_item_id    ?? 0);


            ItemLog::create([
                'user_id'     => Auth::id(),
                'char_id'     => $charId,
                'char_name'   => $charName,
                'procedure'   => $procedureName,
                'code_name'   => $codeName,
                'ref_item_id' => $refItemId,
                'data'        => $data,
                'opt_level'   => $optLevel,
                'variance'    => $variance,
                'success'     => $returnCode === 1,
                'return_code' => $returnCode,
                'destination' => $destination ?: null,
                'slot'        => $slot ?: null,
                'new_item_id' => $newItemId ?: null,
                'ip_address'  => request()->ip(),
            ]);
        } catch (Throwable $exception) {
            report($exception);

            return $this->buildResult(returnCode: -1);
        }

        return $this->buildResult(
            returnCode: $returnCode,
            destination: $destination,
            slot: $slot,
            newItemId: $newItemId,
        );
    }

    private function hasCharacterIdentifier(?string $charName, ?int $charId): bool
    {
        return ($charName !== null && $charName !== '') || $charId !== null;
    }

    private function hasItemIdentifier(?string $codeName, ?int $refItemId): bool
    {
        return ($codeName !== null && $codeName !== '') || $refItemId !== null;
    }

    /**
     * @return array{success: bool, return_code: int}
     */
    public function deleteItemVsro(
        string $ownerName,
        int $targetStorage,
        int $slot,
    ): array {
        return $this->executeDeleteItemProcedure(
            procedureName: '_SMC_DEL_ITEM_SILKPANEL_VSRO',
            ownerName: $ownerName,
            targetStorage: $targetStorage,
            slot: $slot,
        );
    }

    /**
     * @return array{success: bool, return_code: int}
     */
    public function deleteItemIsro(
        string $ownerName,
        int $targetStorage,
        int $slot,
        int $unknown = 0,
    ): array {
        return $this->executeDeleteItemProcedure(
            procedureName: '_SMC_DEL_ITEM_SILKPANEL_ISRO',
            ownerName: $ownerName,
            targetStorage: $targetStorage,
            slot: $slot,
            unknown: $unknown,
        );
    }

    /**
     * @return array{success: bool, return_code: int}
     */
    private function executeDeleteItemProcedure(
        string $procedureName,
        string $ownerName,
        int $targetStorage,
        int $slot,
        ?int $unknown = null,
    ): array {
        try {
            $hasUnknown = $unknown !== null;

            $sql = $hasUnknown
                ? "EXEC [dbo].[{$procedureName}] @TargetStorage = ?, @OwnerName = ?, @Unknown = ?, @Slot = ?;"
                : "EXEC [dbo].[{$procedureName}] @TargetStorage = ?, @OwnerName = ?, @Slot = ?;";

            $bindings = $hasUnknown
                ? [$targetStorage, $ownerName, $unknown, $slot]
                : [$targetStorage, $ownerName, $slot];

            // The delete procedures return a scalar result set (SELECT N; RETURN;) instead of
            // OUTPUT params. Use nextRowset() to find the first non-empty result set and read
            // its first column as the return code.
            $pdo  = $this->database->connection(DatabaseNameEnums::SRO_SHARD->value)->getPdo();
            $stmt = $pdo->prepare($sql);
            $stmt->execute($bindings);

            $returnCode = -1;
            do {
                $rows = $stmt->fetchAll(PDO::FETCH_NUM);
                if (!empty($rows) && isset($rows[0][0])) {
                    $returnCode = (int) $rows[0][0];
                    // Keep iterating — inner procs may emit earlier result sets.
                    // The outer procedure's SELECT (1 / -N) is always the last one.
                }
            } while ($stmt->nextRowset());

            ItemLog::create([
                'user_id'     => Auth::id(),
                'char_id'     => null,
                'char_name'   => $ownerName,
                'procedure'   => $procedureName,
                'code_name'   => null,
                'ref_item_id' => null,
                'data'        => null,
                'opt_level'   => null,
                'variance'    => null,
                'success'     => $returnCode === 1,
                'return_code' => $returnCode,
                'destination' => null,
                'slot'        => $slot,
                'new_item_id' => null,
                'ip_address'  => request()->ip(),
            ]);
        } catch (Throwable $exception) {
            report($exception);

            return ['success' => false, 'return_code' => -1];
        }

        return ['success' => $returnCode === 1, 'return_code' => $returnCode];
    }

    /**
     * @return array{success: bool, return_code: int, destination: string, slot: int, new_item_id: int}
     */
    private function buildResult(
        int $returnCode,
        string $destination = '',
        int $slot = 0,
        int $newItemId = 0,
    ): array {
        return [
            'success' => $returnCode === 1,
            'return_code' => $returnCode,
            'destination' => $destination,
            'slot' => $slot,
            'new_item_id' => $newItemId,
        ];
    }
}
