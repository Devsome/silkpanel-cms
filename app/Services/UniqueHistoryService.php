<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class UniqueHistoryService
{
    /**
     * The literal event-type values the frontend understands. The configurable
     * vSRO source normalizes its own values onto these so no view has to change.
     */
    private const KILL = 'KILL_UNIQUE_MONSTER';

    private const SPAWN = 'SPAWN_UNIQUE_MONSTER';

    /**
     * Whether the public Unique History page should be shown at all.
     *
     * iSRO: version + the `history_unique_enabled` feature toggle.
     * vSRO / custom: the dedicated source must be enabled *and* configured.
     */
    public static function isAvailable(): bool
    {
        if (config('silkpanel.version') === 'isro') {
            return (bool) Setting::get('history_unique_enabled', true);
        }

        return self::customConfig() !== null;
    }

    /**
     * Whether the Area column has usable data, so the frontend can hide it when
     * empty. iSRO always resolves an area via `_RefRegion`; vSRO only does when
     * the AreaName field is mapped to a resolvable column.
     */
    public static function areaAvailable(): bool
    {
        if (config('silkpanel.version') === 'isro') {
            return true;
        }

        $config = self::customConfig();

        if ($config === null) {
            return false;
        }

        return (new self())->resolveMappedColumn(
            (string) ($config['map']['AreaName'] ?? ''),
            'base',
            '_join',
            $config['join_enabled'],
        ) !== null;
    }

    /**
     * Build the query for the configurable vSRO / custom unique-history source.
     *
     * Always selects the frontend aliases (Value, ValueCodeName128, EventTime,
     * CharName16, CharID, RefObjID, AreaName; unmapped ones as NULL), ordered by
     * the mapped EventTime descending. No limit is applied — the caller
     * paginates. Returns null when disabled or incompletely configured.
     *
     * @param  bool  $showSpawns  false = kills only.
     * @param  array<string, mixed>|null  $configOverride  Raw settings to preview
     *         instead of the saved config (used by the Filament page).
     */
    public function customQuery(bool $showSpawns = true, ?array $configOverride = null): ?Builder
    {
        $config = $configOverride !== null
            ? self::normalizeConfig($configOverride)
            : self::customConfig();

        if ($config === null) {
            return null;
        }

        $baseAlias = 'base';
        $joinAlias = '_join';

        $expr = [];
        foreach (['Value', 'EventTime', 'EventType', 'CharName16', 'CharID', 'RefObjID', 'AreaName'] as $field) {
            $expr[$field] = $this->resolveMappedColumn(
                (string) ($config['map'][$field] ?? ''),
                $baseAlias,
                $joinAlias,
                $config['join_enabled'],
            );
        }

        // Value and EventTime are mandatory (guarded again in normalizeConfig()).
        if ($expr['Value'] === null || $expr['EventTime'] === null) {
            return null;
        }

        $query = DB::connection($config['connection'])
            ->table("{$config['table']} as {$baseAlias}");

        $selects = [
            DB::raw("{$expr['Value']} as {$this->quoteIdentifier('Value')}"),
            DB::raw("{$expr['EventTime']} as {$this->quoteIdentifier('EventTime')}"),
        ];

        foreach (['CharName16', 'CharID', 'RefObjID', 'AreaName'] as $field) {
            $selects[] = $expr[$field] !== null
                ? DB::raw("{$expr[$field]} as {$this->quoteIdentifier($field)}")
                : DB::raw("NULL as {$this->quoteIdentifier($field)}");
        }

        $query->select($selects);

        // ValueCodeName128 drives the kill/spawn badge and whether a killer is shown.
        [$eventTypeSelect, $eventTypeBindings] = $this->eventTypeSelect($expr['EventType'], $config['kill_value']);
        $query->selectRaw($eventTypeSelect, $eventTypeBindings);

        if ($config['join_enabled']) {
            $query->leftJoin(
                "{$config['join_qualified_table']} as {$joinAlias}",
                "{$joinAlias}.{$config['join_foreign_key']}",
                '=',
                "{$baseAlias}.{$config['join_local_key']}",
            );
        }

        // Optional filter to isolate unique-event rows.
        if ($config['filter_column'] !== '') {
            $query->where(
                DB::raw("{$baseAlias}.{$this->quoteIdentifier($config['filter_column'])}"),
                '=',
                $config['filter_value'],
            );
        }

        // Kills-only: filter on the mapped event-type expression. When the event
        // type is unmapped every row is treated as a kill, so no filter is needed.
        if (! $showSpawns && $expr['EventType'] !== null) {
            $killValue = $config['kill_value'] !== '' ? $config['kill_value'] : self::KILL;
            $query->where(DB::raw($expr['EventType']), '=', $killValue);
        }

        return $query->orderBy(DB::raw($expr['EventTime']), 'desc');
    }

    /**
     * Build the ValueCodeName128 select expression + its bindings.
     *
     * - event type unmapped   → constant KILL (so killers are shown for kill-only logs)
     * - mapped, with kill val → CASE normalizing to KILL / SPAWN literals
     * - mapped, no kill val   → raw column (assumes it already holds the literals)
     *
     * @return array{0: string, 1: array<int, mixed>}
     */
    private function eventTypeSelect(?string $eventTypeExpr, string $killValue): array
    {
        $alias = $this->quoteIdentifier('ValueCodeName128');

        if ($eventTypeExpr === null) {
            return ["'" . self::KILL . "' as {$alias}", []];
        }

        if ($killValue !== '') {
            return [
                "CASE WHEN {$eventTypeExpr} = ? THEN '" . self::KILL . "' ELSE '" . self::SPAWN . "' END as {$alias}",
                [$killValue],
            ];
        }

        return ["{$eventTypeExpr} as {$alias}", []];
    }

    /**
     * Read and validate the *saved* configurable vSRO source settings.
     *
     * @return array<string, mixed>|null
     */
    private static function customConfig(): ?array
    {
        $keys = [
            'unique_history_vsro_enabled',
            'unique_history_source_connection',
            'unique_history_source_table',
            'unique_history_source_filter_column',
            'unique_history_source_filter_value',
            'unique_history_source_kill_value',
            'unique_history_source_join_enabled',
            'unique_history_source_join_connection',
            'unique_history_source_join_table',
            'unique_history_source_join_local_key',
            'unique_history_source_join_foreign_key',
            'unique_history_source_map_value',
            'unique_history_source_map_eventtime',
            'unique_history_source_map_eventtype',
            'unique_history_source_map_charname',
            'unique_history_source_map_charid',
            'unique_history_source_map_refobjid',
            'unique_history_source_map_area',
        ];

        $raw = [];
        foreach ($keys as $key) {
            $raw[$key] = Setting::get($key);
        }

        return self::normalizeConfig($raw);
    }

    /**
     * Validate and normalize raw source settings into the shape the builder needs.
     *
     * Returns null when the source is disabled or missing a required piece
     * (connection, table, or the mandatory Value / EventTime mapping).
     *
     * @param  array<string, mixed>  $raw
     * @return array<string, mixed>|null
     */
    private static function normalizeConfig(array $raw): ?array
    {
        if (! (bool) ($raw['unique_history_vsro_enabled'] ?? false)) {
            return null;
        }

        $connection = trim((string) ($raw['unique_history_source_connection'] ?? ''));
        $table = trim((string) ($raw['unique_history_source_table'] ?? ''));

        if ($connection === '' || $table === '') {
            return null;
        }

        $map = [
            'Value'      => trim((string) ($raw['unique_history_source_map_value'] ?? '')),
            'EventTime'  => trim((string) ($raw['unique_history_source_map_eventtime'] ?? '')),
            'EventType'  => trim((string) ($raw['unique_history_source_map_eventtype'] ?? '')),
            'CharName16' => trim((string) ($raw['unique_history_source_map_charname'] ?? '')),
            'CharID'     => trim((string) ($raw['unique_history_source_map_charid'] ?? '')),
            'RefObjID'   => trim((string) ($raw['unique_history_source_map_refobjid'] ?? '')),
            'AreaName'   => trim((string) ($raw['unique_history_source_map_area'] ?? '')),
        ];

        // Value and EventTime are mandatory.
        if ($map['Value'] === '' || $map['EventTime'] === '') {
            return null;
        }

        $joinEnabled = (bool) ($raw['unique_history_source_join_enabled'] ?? false);
        $joinConnection = trim((string) ($raw['unique_history_source_join_connection'] ?? ''));
        $joinTable = trim((string) ($raw['unique_history_source_join_table'] ?? ''));
        $joinLocalKey = trim((string) ($raw['unique_history_source_join_local_key'] ?? ''));
        $joinForeignKey = trim((string) ($raw['unique_history_source_join_foreign_key'] ?? ''));

        // A join is only usable when fully specified.
        if ($joinEnabled && ($joinTable === '' || $joinLocalKey === '' || $joinForeignKey === '')) {
            $joinEnabled = false;
        }

        // Cross-database qualified join table name (same SQL Server instance).
        $joinQualifiedTable = $joinTable;
        if ($joinEnabled && $joinConnection !== '' && $joinConnection !== $connection) {
            $joinDbConfig = config("database.connections.{$joinConnection}", []);
            $joinDatabase = is_array($joinDbConfig) ? trim((string) ($joinDbConfig['database'] ?? '')) : '';
            if ($joinDatabase !== '') {
                $joinQualifiedTable = "{$joinDatabase}.dbo.{$joinTable}";
            }
        }

        return [
            'connection'          => $connection,
            'table'               => $table,
            'filter_column'       => trim((string) ($raw['unique_history_source_filter_column'] ?? '')),
            'filter_value'        => (string) ($raw['unique_history_source_filter_value'] ?? ''),
            'kill_value'          => trim((string) ($raw['unique_history_source_kill_value'] ?? '')),
            'join_enabled'        => $joinEnabled,
            'join_local_key'      => $joinLocalKey,
            'join_foreign_key'    => $joinForeignKey,
            'join_qualified_table' => $joinQualifiedTable,
            'map'                 => $map,
        ];
    }

    /**
     * Turn a mapping value ("base:Col" / "join:Col") into a qualified, quoted
     * SQL expression, or null when unmapped or (join value without a join).
     */
    private function resolveMappedColumn(string $value, string $baseAlias, string $joinAlias, bool $joinEnabled): ?string
    {
        if ($value === '' || ! str_contains($value, ':')) {
            return null;
        }

        [$prefix, $column] = explode(':', $value, 2);
        $column = trim($column);

        if ($column === '') {
            return null;
        }

        if ($prefix === 'join') {
            if (! $joinEnabled) {
                return null;
            }

            return "{$joinAlias}.{$this->quoteIdentifier($column)}";
        }

        return "{$baseAlias}.{$this->quoteIdentifier($column)}";
    }

    private function quoteIdentifier(string $identifier): string
    {
        return '[' . str_replace(']', ']]', $identifier) . ']';
    }
}
