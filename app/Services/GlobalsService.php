<?php

namespace App\Services;

use App\Enums\DatabaseNameEnums;
use App\Models\Setting;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class GlobalsService
{
    private const CACHE_TTL = 60;

    /**
     * The frontend fields every global-history view expects per row. The
     * configurable vSRO source maps its own columns onto these aliases so no
     * view has to change. `Comment` and `EventTime` are mandatory.
     */
    private const OUTPUT_FIELDS = ['Comment', 'CharName', 'EventTime', 'CharID', 'RefObjID'];

    /**
     * Whether the public Global History surfaces (page, homepage widget and
     * character panel) should be shown at all.
     *
     * iSRO: version + the `history_global_enabled` feature toggle.
     * vSRO / custom: the dedicated source must be enabled *and* configured.
     */
    public static function isAvailable(): bool
    {
        if (config('silkpanel.version') === 'isro') {
            return (bool) Setting::get('history_global_enabled', true);
        }

        return self::customConfig() !== null;
    }

    /**
     * The latest global (yell) chat messages.
     *
     * @return Collection<int, object>
     */
    public function latest(int $limit = 10): Collection
    {
        $limit = max(1, min($limit, 50));

        if (config('silkpanel.version') === 'isro') {
            return $this->isroGlobals($limit);
        }

        return $this->customSource($limit);
    }

    /**
     * The latest global (yell) messages sent by a single character.
     *
     * @return Collection<int, object>
     */
    public function forCharacter(string $charName, int $limit = 10): Collection
    {
        $charName = trim($charName);
        $limit = max(1, min($limit, 50));

        if ($charName === '') {
            return collect();
        }

        if (config('silkpanel.version') === 'isro') {
            return $this->isroForCharacter($charName, $limit);
        }

        $query = $this->customQuery(null, $charName);

        if ($query === null) {
            return collect();
        }

        try {
            return Cache::remember('globals_custom_char_' . md5($charName) . "_{$limit}", $this->customCacheTtl(), function () use ($query, $limit) {
                return $query->limit($limit)->get();
            });
        } catch (\Throwable) {
            return collect();
        }
    }

    /**
     * Build the query for the configurable vSRO / custom global source.
     *
     * Returns a query builder that always selects the five frontend aliases
     * ({@see self::OUTPUT_FIELDS}; unmapped ones as NULL), ordered by the
     * mapped EventTime column descending. No limit is applied — the caller
     * limits or paginates. Returns null when the source is disabled or
     * incompletely configured.
     *
     * @param  string|null  $tradeFilter  'WTS' / 'WTB' — filters on the mapped Comment column.
     * @param  string|null  $charName     Filters on the mapped CharName column.
     * @param  array<string, mixed>|null  $configOverride  Raw settings (keyed by the
     *         `global_history_source_*` keys) to preview instead of the saved
     *         config — used by the Filament page to preview unsaved changes.
     */
    public function customQuery(?string $tradeFilter = null, ?string $charName = null, ?array $configOverride = null): ?Builder
    {
        $config = $configOverride !== null
            ? self::normalizeConfig($configOverride)
            : self::customConfig();

        if ($config === null) {
            return null;
        }

        $baseAlias = 'base';
        $joinAlias = '_join';

        // Resolve every mapping value (e.g. "base:Comment" / "join:CharName16")
        // to a fully-qualified SQL expression or null when unmapped.
        $expr = [];
        foreach (self::OUTPUT_FIELDS as $field) {
            $expr[$field] = $this->resolveMappedColumn(
                (string) ($config['map'][$field] ?? ''),
                $baseAlias,
                $joinAlias,
                $config['join_enabled'],
            );
        }

        // Comment and EventTime are mandatory (guarded again in customConfig()).
        if ($expr['Comment'] === null || $expr['EventTime'] === null) {
            return null;
        }

        $selects = [];
        foreach (self::OUTPUT_FIELDS as $field) {
            $selects[] = $expr[$field] !== null
                ? DB::raw("{$expr[$field]} as {$this->quoteIdentifier($field)}")
                : DB::raw("NULL as {$this->quoteIdentifier($field)}");
        }

        $query = DB::connection($config['connection'])
            ->table("{$config['table']} as {$baseAlias}")
            ->select($selects);

        if ($config['join_enabled']) {
            $query->leftJoin(
                "{$config['join_qualified_table']} as {$joinAlias}",
                "{$joinAlias}.{$config['join_foreign_key']}",
                '=',
                "{$baseAlias}.{$config['join_local_key']}",
            );
        }

        // Optional filter to isolate yell / global rows (iSRO ships TargetName = '[YELL]').
        if ($config['filter_column'] !== '') {
            $query->where(
                DB::raw("{$baseAlias}.{$this->quoteIdentifier($config['filter_column'])}"),
                '=',
                $config['filter_value'],
            );
        }

        if (in_array($tradeFilter, ['WTS', 'WTB'], true)) {
            $query->where(DB::raw($expr['Comment']), 'like', '%' . $tradeFilter . '%');
        }

        if ($charName !== null && $charName !== '' && $expr['CharName'] !== null) {
            $query->where(DB::raw($expr['CharName']), '=', $charName);
        }

        return $query->orderBy(DB::raw($expr['EventTime']), 'desc');
    }

    /**
     * iSRO: read the latest yell messages from `_LogChatMessage`.
     *
     * @return Collection<int, object>
     */
    private function isroGlobals(int $limit): Collection
    {
        try {
            return Cache::remember("globals_widget_{$limit}", self::CACHE_TTL, function () use ($limit) {
                $shardDb = DB::connection(DatabaseNameEnums::SRO_SHARD->value)->getDatabaseName();

                return DB::connection(DatabaseNameEnums::SRO_LOG->value)
                    ->table('dbo._LogChatMessage as log')
                    ->select([
                        'c.CharID',
                        'c.RefObjID',
                        'log.CharName',
                        'log.EventTime',
                        'log.Comment',
                    ])
                    ->leftJoin(DB::raw("{$shardDb}.dbo._Char as c"), function ($join) {
                        $join->on(
                            DB::raw('c.CharName16 COLLATE Latin1_General_CI_AS'),
                            '=',
                            DB::raw('log.CharName COLLATE Latin1_General_CI_AS'),
                        );
                    })
                    ->where('log.TargetName', '[YELL]')
                    ->orderByDesc('log.EventTime')
                    ->limit($limit)
                    ->get();
            });
        } catch (\Throwable) {
            return collect();
        }
    }

    /**
     * iSRO: the latest yell messages for a single character.
     *
     * @return Collection<int, object>
     */
    private function isroForCharacter(string $charName, int $limit): Collection
    {
        try {
            return Cache::remember('globals_char_' . md5($charName) . "_{$limit}", self::CACHE_TTL, function () use ($charName, $limit) {
                return DB::connection(DatabaseNameEnums::SRO_LOG->value)
                    ->table('dbo._LogChatMessage')
                    ->select(['CharName', 'EventTime', 'Comment'])
                    ->where('TargetName', '[YELL]')
                    ->where('CharName', $charName)
                    ->orderByDesc('EventTime')
                    ->limit($limit)
                    ->get();
            });
        } catch (\Throwable) {
            return collect();
        }
    }

    /**
     * vSRO / custom servers: read from the admin-configured source.
     *
     * @return Collection<int, object>
     */
    private function customSource(int $limit): Collection
    {
        $query = $this->customQuery();

        if ($query === null) {
            return collect();
        }

        try {
            return Cache::remember("globals_custom_widget_{$limit}", $this->customCacheTtl(), function () use ($query, $limit) {
                return $query->limit($limit)->get();
            });
        } catch (\Throwable) {
            return collect();
        }
    }

    /**
     * Read and validate the *saved* configurable vSRO source settings.
     *
     * @return array<string, mixed>|null
     */
    private static function customConfig(): ?array
    {
        $keys = [
            'global_history_vsro_enabled',
            'global_history_source_connection',
            'global_history_source_table',
            'global_history_source_filter_column',
            'global_history_source_filter_value',
            'global_history_source_cache_ttl',
            'global_history_source_join_enabled',
            'global_history_source_join_connection',
            'global_history_source_join_table',
            'global_history_source_join_local_key',
            'global_history_source_join_foreign_key',
            'global_history_source_map_comment',
            'global_history_source_map_eventtime',
            'global_history_source_map_charname',
            'global_history_source_map_charid',
            'global_history_source_map_refobjid',
        ];

        $raw = [];
        foreach ($keys as $key) {
            $raw[$key] = Setting::get($key);
        }

        return self::normalizeConfig($raw);
    }

    /**
     * Validate and normalize raw source settings (keyed by the
     * `global_history_source_*` keys) into the shape the builder needs.
     *
     * Returns null when the source is disabled or missing a required piece
     * (connection, table, or the mandatory Comment / EventTime mapping).
     *
     * @param  array<string, mixed>  $raw
     * @return array<string, mixed>|null
     */
    private static function normalizeConfig(array $raw): ?array
    {
        if (! (bool) ($raw['global_history_vsro_enabled'] ?? false)) {
            return null;
        }

        $connection = trim((string) ($raw['global_history_source_connection'] ?? ''));
        $table = trim((string) ($raw['global_history_source_table'] ?? ''));

        if ($connection === '' || $table === '') {
            return null;
        }

        $map = [
            'Comment'  => trim((string) ($raw['global_history_source_map_comment'] ?? '')),
            'EventTime' => trim((string) ($raw['global_history_source_map_eventtime'] ?? '')),
            'CharName' => trim((string) ($raw['global_history_source_map_charname'] ?? '')),
            'CharID'   => trim((string) ($raw['global_history_source_map_charid'] ?? '')),
            'RefObjID' => trim((string) ($raw['global_history_source_map_refobjid'] ?? '')),
        ];

        // Comment and EventTime are mandatory.
        if ($map['Comment'] === '' || $map['EventTime'] === '') {
            return null;
        }

        $joinEnabled = (bool) ($raw['global_history_source_join_enabled'] ?? false);
        $joinConnection = trim((string) ($raw['global_history_source_join_connection'] ?? ''));
        $joinTable = trim((string) ($raw['global_history_source_join_table'] ?? ''));
        $joinLocalKey = trim((string) ($raw['global_history_source_join_local_key'] ?? ''));
        $joinForeignKey = trim((string) ($raw['global_history_source_join_foreign_key'] ?? ''));

        // A join is only usable when fully specified.
        if ($joinEnabled && ($joinTable === '' || $joinLocalKey === '' || $joinForeignKey === '')) {
            $joinEnabled = false;
        }

        // Build a cross-database qualified join table name (same SQL Server instance).
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
            'filter_column'       => trim((string) ($raw['global_history_source_filter_column'] ?? '')),
            'filter_value'        => (string) ($raw['global_history_source_filter_value'] ?? ''),
            'join_enabled'        => $joinEnabled,
            'join_local_key'      => $joinLocalKey,
            'join_foreign_key'    => $joinForeignKey,
            'join_qualified_table' => $joinQualifiedTable,
            'map'                 => $map,
        ];
    }

    /**
     * Turn a mapping value ("base:Col" / "join:Col") into a qualified,
     * quoted SQL expression, or null when unmapped or (join without a join).
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

    private function customCacheTtl(): int
    {
        $ttl = (int) Setting::get('global_history_source_cache_ttl', self::CACHE_TTL);

        return $ttl > 0 ? $ttl : self::CACHE_TTL;
    }
}
