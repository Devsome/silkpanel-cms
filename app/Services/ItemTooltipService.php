<?php

namespace App\Services;

use App\Enums\DatabaseNameEnums;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use SilkPanel\SilkroadModels\Models\Account\AbstractItemNameDesc;
use SilkPanel\SilkroadModels\Models\Account\MagOptDesc;

/**
 * Builds the same tooltip Collection that InventoryService::getItemInfo() produces,
 * but accepts either a list of item_id64 values (for web storage / market listings whose
 * items live in _Items) or a collection of already-loaded raw Eloquent/stdClass objects
 * (for game inventory items that came from getInventory()).
 *
 * The result is keyed by integer item_id64 and is compatible with the
 * <x-characters.inventory-tooltip> Blade component.
 */
class ItemTooltipService
{
    /**
     * Resolve a single human-readable item name for the given item_id64.
     * Returns null when the item is not found or the name cannot be resolved.
     */
    public function getItemName(int $itemId64): ?string
    {
        $connection = DatabaseNameEnums::SRO_SHARD->value;

        $nameStrId = DB::connection($connection)
            ->table('_Items as it')
            ->join('_RefObjCommon as roc', 'roc.ID', '=', 'it.RefItemId')
            ->where('it.ID64', $itemId64)
            ->value('roc.NameStrID128');

        if (! $nameStrId) {
            return null;
        }

        $names = $this->loadNames(collect([$nameStrId]));
        return $names[$nameStrId] ?? null;
    }

    /**
     * Query _Items JOIN _RefObjCommon JOIN _RefObjItem LEFT JOIN _BindingOptionWithItem
     * for the given item_id64 list and return tooltip data keyed by item_id64.
     *
     * @param  int[]  $itemId64s
     */
    public function forItemIds(array $itemId64s): Collection
    {
        $itemId64s = array_filter(array_map('intval', $itemId64s));
        if (empty($itemId64s)) {
            return collect();
        }

        $connection = DatabaseNameEnums::SRO_SHARD->value;

        $rows = DB::connection($connection)
            ->table('_Items as it')
            ->join('_RefObjCommon as roc', 'roc.ID', '=', 'it.RefItemId')
            ->leftJoin('_RefObjItem as roi', 'roc.Link', '=', 'roi.ID')
            ->leftJoin('_BindingOptionWithItem as bowi', function ($join) {
                $join->on('bowi.nItemDBID', '=', 'it.ID64')
                     ->where('bowi.bOptType', '=', 2);
            })
            ->whereIn('it.ID64', $itemId64s)
            ->get();

        $itemNames = $this->loadNames($rows->pluck('NameStrID128'));

        $result = collect();
        foreach ($rows as $row) {
            try {
                $result[(int) $row->ID64] = $this->buildTooltip($row, $itemNames);
            } catch (\Throwable) {
                // skip items that fail to build a tooltip
            }
        }
        return $result;
    }

    /**
     * Process raw inventory items that have already been loaded by getInventory()
     * (they carry all needed columns from the joined query). Keyed by ID64.
     */
    public function fromRawItems($items): Collection
    {
        $items = collect($items);
        if ($items->isEmpty()) {
            return collect();
        }

        $itemNames = $this->loadNames($items->pluck('NameStrID128'));

        $result = collect();
        foreach ($items as $row) {
            try {
                $id = (int) ($row->ID64 ?? 0);
                if ($id > 0) {
                    $result[$id] = $this->buildTooltip($row, $itemNames);
                }
            } catch (\Throwable) {
                // skip items that fail to build a tooltip
            }
        }
        return $result;
    }

    // ── private ──────────────────────────────────────────────────────────────

    private function loadNames($nameIds): array
    {
        $ids = collect($nameIds)->filter()->unique()->values()->all();
        if (empty($ids)) {
            return [];
        }
        try {
            return resolve(AbstractItemNameDesc::class)->getItemNames($ids);
        } catch (\Throwable) {
            return [];
        }
    }

    private function buildTooltip(object $row, array $itemNames): Collection
    {
        $aSpecialInfo = [];
        $optLevel     = (int) ($row->OptLevel ?? 0);
        $variance     = (int) ($row->Variance ?? 0);

        return collect([
            'ID64'             => $row->ID64,
            'RefItemID'        => $row->RefItemId ?? $row->RefItemID ?? null,
            'Serial64'         => $row->Serial64 ?? null,
            'CodeName128'      => $row->CodeName128,
            'ItemName'         => $itemNames[$row->NameStrID128] ?? $row->NameStrID128 ?? 'Unknown',
            'TypeID1'          => $row->TypeID1 ?? 0,
            'TypeID2'          => $row->TypeID2 ?? 0,
            'TypeID3'          => $row->TypeID3 ?? 0,
            'TypeID4'          => $row->TypeID4 ?? 0,
            'OptLevel'         => $optLevel,
            'nOptValue'        => (int) ($row->nOptValue ?? 0),
            'ReqLevel1'        => $row->ReqLevel1 ?? null,
            'Country'          => ((int) ($row->Country ?? 0)) === 0 ? 'Chinese' : 'European',
            'Gender'           => $this->resolveGender($row),
            'SoxType'          => $this->getSoxType($row),
            'SoxName'          => $this->getSoxName($row),
            'Degree'           => isset($row->ItemClass) ? (int) ceil($row->ItemClass / 3) : null,
            'JobDegree'        => config('item.job_degree')[$row->ItemClass] ?? null,
            'Type'             => config('item.types')[$row->TypeID1][$row->TypeID2][$row->TypeID3][$row->TypeID4] ?? null,
            'Detail'           => config('item.detail')[$row->Slot ?? 0] ?? null,
            'MaxMagicOptCount' => $row->MaxMagicOptCount ?? null,
            'ChildItemCount'   => $row->ChildItemCount ?? 0,
            'Amount'           => (($row->MaxStack ?? 0) > 1) ? ($row->Data ?? 0) : 0,
            'Slot'             => $row->Slot ?? 0,
            'MagParamNum'      => $row->MagParamNum ?? 0,
            'MagParam1'        => $row->MagParam1 ?? 0,
            'WhiteInfo'        => $this->getWhiteInfo($row, $optLevel, $variance),
            'BlueInfo'         => MagOptDesc::getBlueStats($row, $aSpecialInfo),
            'TimeEnd'          => null,
            'DevilMaxHP'       => null,
        ]);
    }

    private function resolveGender(object $row): ?string
    {
        // Gender only applies to armor pieces (TypeID2=1, TypeID3 1–3 Chinese, 9–11 European)
        if ((int) ($row->TypeID2 ?? 0) !== 1) {
            return null;
        }

        if (! in_array((int) ($row->TypeID3 ?? 0), [1, 2, 3, 9, 10, 11], true)) {
            return null;
        }

        return ((int) ($row->ReqGender ?? 0)) === 0 ? 'Female' : 'Male';
    }

    private function getSoxType(object $item): string
    {
        $config = config('item.sox_type', []);
        foreach ($config as $itemClass => $codeNames) {
            if (($item->ItemClass ?? 0) > $itemClass) {
                foreach ($codeNames as $key => $value) {
                    if (str_contains($item->CodeName128 ?? '', $key)) {
                        return $value;
                    }
                }
            }
        }
        return 'Normal';
    }

    private function getSoxName(object $item): ?string
    {
        $config = config('item.sox_name', []);
        foreach ($config as $key => $values) {
            if (str_contains($item->CodeName128 ?? '', $key)) {
                return $values[$item->Slot ?? 0] ?? '';
            }
        }
        return null;
    }

    private function getWhiteInfo(object $item, int $optLevel, int $variance): Collection
    {
        $pct = fn(int $v, int $i) => (int) floor(((int) ($v / pow(32, $i)) & 0x1F) * 3.23);

        return collect([
            'PAtack' => ($item->PAttackMin_L ?? 0) > 0 && ($item->PAttackMax_L ?? 0) > 0
                ? sprintf('Phy. atk. pwr. %d ~ %d (+%d%%)',
                    round(($item->PAttackMin_L + $item->PAttackInc * $optLevel) + (($item->PAttackMin_U - $item->PAttackMin_L) * $pct($variance, 4) / 100)),
                    round(($item->PAttackMax_L + $item->PAttackInc * $optLevel) + (($item->PAttackMax_U - $item->PAttackMax_L) * $pct($variance, 4) / 100)),
                    $pct($variance, 4))
                : '',

            'MAtack' => ($item->MAttackMin_L ?? 0) > 0 && ($item->MAttackMax_L ?? 0) > 0
                ? sprintf('Mag. atk. pwr. %d ~ %d (+%d%%)',
                    (int)(($item->MAttackMin_L + $item->MAttackInc * $optLevel) + (($item->MAttackMin_U - $item->MAttackMin_L) * $pct($variance, 5) / 100)),
                    (int)(($item->MAttackMax_L + $item->MAttackInc * $optLevel) + (($item->MAttackMax_U - $item->MAttackMax_L) * $pct($variance, 5) / 100)),
                    $pct($variance, 5))
                : '',

            'PDefance' => ($item->PD_L ?? 0) > 0
                ? sprintf('Phy. def. pwr. %.1f (+%d%%)',
                    round(($item->PD_L + $item->PDInc * $optLevel) + (($item->PD_U - $item->PD_L) * $pct($variance, 3) / 100), 1),
                    $pct($variance, 3))
                : '',

            'MDefance' => ($item->MD_L ?? 0) > 0
                ? sprintf('Mag. def. pwr. %.1f (+%d%%)',
                    round(($item->MD_L + $item->MDInc * $optLevel) + (($item->MD_U - $item->MD_L) * $pct($variance, 4) / 100), 1),
                    $pct($variance, 4))
                : '',

            'Durability' => ($item->Dur_U ?? 0) > 0
                ? sprintf('Durability %d/%d (+%d%%)', $item->Data ?? 0, $item->Data ?? 0, $pct($variance, 0))
                : '',

            'BlockRate' => ($item->BR_L ?? 0) > 0
                ? sprintf('Block Rate %d (+%d%%)',
                    (int)(($item->BR_L) + (($item->BR_U - $item->BR_L) * $pct($variance, 3) / 100)),
                    $pct($variance, 3))
                : '',

            'AtackDist' => ($item->Range ?? 0) > 0
                ? sprintf('Attack distance %.1f m', $item->Range / 10)
                : '',

            'AtackRate' => ($item->HR_L ?? 0) > 0
                ? sprintf('Attack rate %d (+%d%%)',
                    (int)(($item->HR_L + $item->HRInc * $optLevel) + (($item->HR_U - $item->HR_L) * $pct($variance, 3) / 100)),
                    $pct($variance, 3))
                : '',

            'Critical' => ($item->CHR_L ?? 0) > 0
                ? sprintf('Critical %d (+%d%%)',
                    (int)(($item->CHR_L) + (($item->CHR_U - $item->CHR_L) * $pct($variance, 6) / 100)),
                    $pct($variance, 6))
                : '',

            'ParryRate' => ($item->ER_L ?? 0) > 0
                ? sprintf('Parry rate %d (+%d%%)',
                    (int)(($item->ER_L + $item->ERInc * $optLevel) + (($item->ER_U - $item->ER_L) * $pct($variance, 5) / 100)),
                    $pct($variance, 5))
                : '',

            'PReinforceWep' => ($item->PAStrMin_L ?? 0) > 0 && ($item->PAStrMax_L ?? 0) > 0
                ? sprintf('Phy. reinforce %.1f ~ %.1f (+%d%%)',
                    (float)(($item->PAStrMin_L) + (($item->PAStrMin_U - $item->PAStrMin_L) * $pct($variance, 1) / 100)) / 10,
                    (float)(($item->PAStrMax_L) + (($item->PAStrMax_U - $item->PAStrMax_L) * $pct($variance, 1) / 100)) / 10,
                    $pct($variance, 1))
                : '',

            'MReinforceWep' => ($item->MAInt_Min_L ?? 0) > 0 && ($item->MAInt_Max_L ?? 0) > 0
                ? sprintf('Mag. reinforce %.1f ~ %.1f (+%d%%)',
                    (float)(($item->MAInt_Min_L) + (($item->MAInt_Min_U - $item->MAInt_Min_L) * $pct($variance, 2) / 100)) / 10,
                    (float)(($item->MAInt_Max_L) + (($item->MAInt_Max_U - $item->MAInt_Max_L) * $pct($variance, 2) / 100)) / 10,
                    $pct($variance, 2))
                : '',

            'PReinforceSet' => ($item->PDStr_L ?? 0) > 0
                ? sprintf('Phy. reinforce %.1f (+%d%%)',
                    (float)(($item->PDStr_L) + (($item->PDStr_U - $item->PDStr_L) * $pct($variance, 1) / 100)) / 10,
                    $pct($variance, 1))
                : '',

            'MReinforceSet' => ($item->MDInt_L ?? 0) > 0
                ? sprintf('Mag. reinforce %.1f (+%d%%)',
                    (float)(($item->MDInt_L) + (($item->MDInt_U - $item->MDInt_L) * $pct($variance, 2) / 100)) / 10,
                    $pct($variance, 2))
                : '',

            'Pabsorp' => ($item->PAR_L ?? 0) > 0
                ? sprintf('Phy. absorption %.1f (+%d%%)',
                    round(($item->PAR_L + $item->PARInc * $optLevel) + (($item->PAR_U - $item->PAR_L) * $pct($variance, 0) / 100), 1),
                    $pct($variance, 0))
                : '',

            'Mabsorp' => ($item->MAR_L ?? 0) > 0
                ? sprintf('Mag. absorption %.1f (+%d%%)',
                    round(($item->MAR_L + $item->MARInc * $optLevel) + (($item->MAR_U - $item->MAR_L) * $pct($variance, 1) / 100), 1),
                    $pct($variance, 1))
                : '',
        ]);
    }
}
