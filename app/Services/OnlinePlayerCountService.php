<?php

namespace App\Services;

use App\Helpers\LicenseHelper;
use App\Helpers\SettingHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use SilkPanel\SilkroadModels\Models\Account\ShardCurrentUser;
use Throwable;

/**
 * Resolves the raw (real) online player count.
 *
 * By default the count is read from the standard `_ShardCurrentUser` log
 * table. Servers that keep their online count somewhere else (a custom
 * table, a launcher/stat database, a per-character "online" flag, …) can
 * override the source in the backend: pick a connection, a table and a
 * column, and choose how the column is aggregated.
 *
 * `_ShardCurrentUser` always remains the fallback: it is used when the
 * override is disabled, unlicensed, incompletely configured, or when the
 * custom query fails for any reason.
 */
class OnlinePlayerCountService
{
    public const ENABLED_KEY = 'online_players_source_enabled';

    public const CONNECTION_KEY = 'online_players_source_connection';

    public const TABLE_KEY = 'online_players_source_table';

    public const COLUMN_KEY = 'online_players_source_column';

    public const AGGREGATE_KEY = 'online_players_source_aggregate';

    public const ORDER_COLUMN_KEY = 'online_players_source_order_column';

    public const ORDER_DIRECTION_KEY = 'online_players_source_order_direction';

    public const FILTER_COLUMN_KEY = 'online_players_source_filter_column';

    public const FILTER_VALUE_KEY = 'online_players_source_filter_value';

    /** Read the column of a single row (newest first) — mirrors `_ShardCurrentUser`. */
    public const AGGREGATE_VALUE = 'value';

    /** COUNT(*) over the (optionally filtered) table. */
    public const AGGREGATE_COUNT_ROWS = 'count_rows';

    /** COUNT(column) — rows where the column is not null. */
    public const AGGREGATE_COUNT = 'count';

    /** COUNT(DISTINCT column). */
    public const AGGREGATE_COUNT_DISTINCT = 'count_distinct';

    /** SUM(column). */
    public const AGGREGATE_SUM = 'sum';

    /** MAX(column). */
    public const AGGREGATE_MAX = 'max';

    /** Aggregates that operate on a column and therefore require one. */
    public const AGGREGATES_REQUIRING_COLUMN = [
        self::AGGREGATE_VALUE,
        self::AGGREGATE_COUNT,
        self::AGGREGATE_COUNT_DISTINCT,
        self::AGGREGATE_SUM,
        self::AGGREGATE_MAX,
    ];

    /**
     * The real online player count.
     *
     * Never throws: any failure of the custom source degrades to the
     * `_ShardCurrentUser` fallback.
     */
    public function count(): int
    {
        if ($this->isOverrideEnabled()) {
            try {
                $count = $this->customCount();

                if ($count !== null) {
                    return $count;
                }
            } catch (Throwable $e) {
                Log::warning('Custom online player source failed, falling back to _ShardCurrentUser.', [
                    'exception' => $e->getMessage(),
                ]);
            }
        }

        return $this->fallbackCount();
    }

    /**
     * Whether the custom source is switched on and licensed.
     */
    public function isOverrideEnabled(): bool
    {
        return (bool) SettingHelper::get(self::ENABLED_KEY, false)
            && LicenseHelper::isValid();
    }

    /**
     * The standard `_ShardCurrentUser` count.
     */
    public function fallbackCount(): int
    {
        if (! class_exists(ShardCurrentUser::class)) {
            return 0;
        }

        try {
            return (int) (ShardCurrentUser::getOnlineCount() ?? 0);
        } catch (Throwable $e) {
            Log::warning('Unable to read the online player count from _ShardCurrentUser.', [
                'exception' => $e->getMessage(),
            ]);

            return 0;
        }
    }

    /**
     * Run the configured custom query.
     *
     * @param  array<string, mixed>|null  $configOverride  Unsaved form state, used by the backend preview.
     * @return int|null  Null when the source is not configured completely.
     *
     * @throws Throwable when the configured source cannot be queried.
     */
    public function customCount(?array $configOverride = null): ?int
    {
        $config = $this->config($configOverride);

        $connection = $config[self::CONNECTION_KEY];
        $table = $config[self::TABLE_KEY];
        $column = $config[self::COLUMN_KEY];
        $aggregate = $config[self::AGGREGATE_KEY];

        if ($connection === '' || $table === '') {
            return null;
        }

        if ($column === '' && in_array($aggregate, self::AGGREGATES_REQUIRING_COLUMN, true)) {
            return null;
        }

        $query = DB::connection($connection)->table($table);

        // A filter column without a value would silently match nothing
        // (`WHERE col = ''`), so both parts are required for it to apply.
        $filterColumn = $config[self::FILTER_COLUMN_KEY];
        $filterValue = $config[self::FILTER_VALUE_KEY];
        if ($filterColumn !== '' && $filterValue !== '') {
            $query->where($filterColumn, $filterValue);
        }

        $result = match ($aggregate) {
            self::AGGREGATE_COUNT_ROWS => $query->count(),
            self::AGGREGATE_COUNT => $query->count($column),
            self::AGGREGATE_COUNT_DISTINCT => $query->distinct()->count($column),
            self::AGGREGATE_SUM => $query->sum($column),
            self::AGGREGATE_MAX => $query->max($column),
            self::AGGREGATE_VALUE => $this->latestValue($query, $config),
            default => throw new RuntimeException("Unknown online player aggregate [{$aggregate}]."),
        };

        return max(0, (int) round((float) $result));
    }

    /**
     * Value of the configured column on a single row.
     *
     * Ordered by the optional order column so log-style tables (one row per
     * interval, like `_ShardCurrentUser`) yield their newest entry.
     *
     * @param  array<string, mixed>  $config
     */
    private function latestValue(\Illuminate\Database\Query\Builder $query, array $config): float|int
    {
        $orderColumn = $config[self::ORDER_COLUMN_KEY];

        if ($orderColumn !== '') {
            $query->orderBy($orderColumn, $config[self::ORDER_DIRECTION_KEY]);
        }

        return (float) ($query->value($config[self::COLUMN_KEY]) ?? 0);
    }

    /**
     * Normalized configuration, either from the settings or from unsaved state.
     *
     * @param  array<string, mixed>|null  $override
     * @return array<string, string>
     */
    public function config(?array $override = null): array
    {
        $read = fn(string $key, string $default = ''): string => trim((string) (
            $override !== null
                ? ($override[$key] ?? $default)
                : SettingHelper::get($key, $default)
        ));

        $direction = strtolower($read(self::ORDER_DIRECTION_KEY, 'desc'));

        return [
            self::CONNECTION_KEY => $read(self::CONNECTION_KEY),
            self::TABLE_KEY => $read(self::TABLE_KEY),
            self::COLUMN_KEY => $read(self::COLUMN_KEY),
            self::AGGREGATE_KEY => $read(self::AGGREGATE_KEY, self::AGGREGATE_VALUE) ?: self::AGGREGATE_VALUE,
            self::ORDER_COLUMN_KEY => $read(self::ORDER_COLUMN_KEY),
            self::ORDER_DIRECTION_KEY => $direction === 'asc' ? 'asc' : 'desc',
            self::FILTER_COLUMN_KEY => $read(self::FILTER_COLUMN_KEY),
            self::FILTER_VALUE_KEY => $read(self::FILTER_VALUE_KEY),
        ];
    }
}
