<?php

namespace App\Filament\Concerns;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use SilkPanel\SilkroadModels\Models\Account\AbstractItemNameDesc;

trait HasNameTranslation
{
    /**
     * Returns the cross-DB translation table name for ISRO, null for vSRO.
     * vSRO has no SILKROAD_R_ACCOUNT database or _Rigid_ItemNameDesc table.
     */
    protected static function transTable(): ?string
    {
        if (config('silkpanel.version') === 'isro') {
            return 'SILKROAD_R_ACCOUNT.dbo._Rigid_ItemNameDesc';
        }

        return null;
    }

    /**
     * Conditionally left-joins the translation table onto a query builder.
     * On vSRO the join is skipped and the ENG alias will not be available.
     */
    protected static function joinTranslation(Builder $query, string $alias, string $strIdCol): Builder
    {
        $trans = static::transTable();

        if ($trans) {
            $query->leftJoin("{$trans} as {$alias}", "{$alias}.StrID", '=', $strIdCol);
        }

        return $query;
    }

    /**
     * Returns a SELECT expression for the ENG name column.
     * On ISRO: "{$alias}.ENG as {$outputCol}"
     * On vSRO: "NULL as {$outputCol}" (the join was skipped, names resolved via resolveNames())
     */
    protected static function engSelect(string $alias, string $outputCol = 'NameENG'): \Illuminate\Database\Query\Expression|string
    {
        if (static::transTable() !== null) {
            return "{$alias}.ENG as {$outputCol}";
        }

        return \Illuminate\Support\Facades\DB::raw("NULL as {$outputCol}");
    }

    /**
     * Resolves English names for a collection of rows via AbstractItemNameDesc.
     * On ISRO rows already carry NameENG from the join — this is a no-op.
     * On vSRO the join was skipped, so we bulk-fetch from _ItemNameDesc and inject.
     *
     * @param  Collection  $rows          Rows with a NameStrID128 column and a target ENG column
     * @param  string      $strIdField    The NameStrID128 field on each row (default 'NameStrID128')
     * @param  string      $targetField   The field to set with the resolved name (e.g. 'NameENG', 'ItemNameENG')
     * @return Collection
     */
    protected static function resolveNames(
        Collection $rows,
        string $strIdField = 'NameStrID128',
        string $targetField = 'NameENG'
    ): Collection {
        if (static::transTable() !== null) {
            return $rows; // ISRO: already resolved via JOIN
        }

        $strIds = $rows->pluck($strIdField)->filter()->unique()->values()->all();

        if (empty($strIds)) {
            return $rows;
        }

        $names = resolve(AbstractItemNameDesc::class)->getItemNames($strIds);

        return $rows->each(function ($row) use ($names, $strIdField, $targetField) {
            $row->{$targetField} = $names[$row->{$strIdField}] ?? null;
        });
    }
}
