<?php

namespace App\Livewire\Rankings;

use App\Models\Setting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use RuntimeException;

class UniqueRanking extends Component
{
    use WithPagination;

    private const UNIQUE_JOIN_OUTPUT_COLUMN = '__join_output';
    private const UNIQUE_POINTS_OUTPUT_COLUMN = '__unique_points';

    public string $search = '';

    protected $queryString = ['search'];

    protected int $perPage = 25;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $title = Setting::get('ranking_unique_title', 'Unique Ranking');
        $columns = Setting::get('ranking_unique_columns', []);
        $limit = (int) Setting::get('ranking_unique_limit', 50);
        $cacheTtl = (int) Setting::get('ranking_unique_cache_ttl', 60);
        $table = trim((string) Setting::get('ranking_unique_table', ''));

        if ($table === '' || empty($columns)) {
            return view('template::livewire.rankings.unique-ranking', [
                'title' => $title,
                'columns' => [],
                'rows' => collect(),
                'paginate' => false,
                'startRank' => 1,
                'configured' => false,
            ]);
        }

        $paginate = $limit === 0;

        try {
            if ($paginate) {
                $query = $this->buildQuery(0);
                $this->applySearch($query, $columns);
                $rows = $query->paginate($this->perPage);
                $startRank = $rows->firstItem() ?? 1;
            } else {
                $cacheKey = 'ranking.unique.' . md5(json_encode([$columns, $limit]));
                $rows = Cache::remember($cacheKey, $cacheTtl * 60, function () use ($limit) {
                    return $this->buildQuery($limit)->get();
                });
                if ($this->search !== '') {
                    $search = mb_strtolower($this->search);
                    $rows = $rows->filter(function ($row) use ($search, $columns) {
                        foreach ($columns as $col) {
                            $colName = $col['column'] ?? '';
                            $value = $row->{$colName} ?? '';
                            if (str_contains(mb_strtolower((string) $value), $search)) {
                                return true;
                            }
                        }
                        return false;
                    })->values();
                }
                $startRank = 1;
            }
        } catch (\Throwable) {
            return view('template::livewire.rankings.unique-ranking', [
                'title' => $title,
                'columns' => [],
                'rows' => collect(),
                'paginate' => false,
                'startRank' => 1,
                'configured' => false,
            ]);
        }

        return view('template::livewire.rankings.unique-ranking', [
            'title' => $title,
            'columns' => $columns,
            'rows' => $rows,
            'paginate' => $paginate,
            'startRank' => $startRank,
            'configured' => true,
        ]);
    }

    private function buildQuery(int $limit)
    {
        $connection = (string) Setting::get('ranking_unique_connection', config('database.default'));
        $table = trim((string) Setting::get('ranking_unique_table', ''));
        $columns = Setting::get('ranking_unique_columns', []);
        $orderBy = (string) Setting::get('ranking_unique_order_by', '');
        $orderDirection = strtolower((string) Setting::get('ranking_unique_order_direction', 'desc')) === 'asc' ? 'asc' : 'desc';

        $joinEnabled = (bool) Setting::get('ranking_unique_join_enabled', false);
        $joinConnection = trim((string) Setting::get('ranking_unique_join_connection', ''));
        $joinTable = trim((string) Setting::get('ranking_unique_join_table', ''));
        $joinLocalKey = trim((string) Setting::get('ranking_unique_join_local_key', ''));
        $joinForeignKey = trim((string) Setting::get('ranking_unique_join_foreign_key', ''));
        $joinOutputColumn = trim((string) Setting::get('ranking_unique_join_output_column', ''));

        $pointsEnabled = (bool) Setting::get('ranking_unique_points_enabled', false);
        $pointsSourceColumn = trim((string) Setting::get('ranking_unique_points_source_column', ''));
        $pointsPlayerColumn = trim((string) Setting::get('ranking_unique_points_player_column', ''));
        $pointsMap = Setting::get('ranking_unique_points_map', []);

        $baseAlias = 'ranking_base';

        if ($table === '') {
            throw new RuntimeException('Unique ranking table not configured.');
        }

        $displayColumns = collect($columns)
            ->pluck('column')
            ->filter(fn($col) => is_string($col) && $col !== '')
            ->values()
            ->all();

        $baseColumns = array_values(array_filter(
            $displayColumns,
            fn(string $col) => !in_array($col, [self::UNIQUE_JOIN_OUTPUT_COLUMN, self::UNIQUE_POINTS_OUTPUT_COLUMN], true)
        ));

        $hasJoinOutput = in_array(self::UNIQUE_JOIN_OUTPUT_COLUMN, $displayColumns, true);
        $hasPointsColumn = in_array(self::UNIQUE_POINTS_OUTPUT_COLUMN, $displayColumns, true);

        $selectedColumns = array_map(
            fn(string $col) => DB::raw("{$baseAlias}.{$this->quoteSqlIdentifier($col)} as {$this->quoteSqlIdentifier($col)}"),
            $baseColumns
        );

        $query = DB::connection($connection)
            ->table("{$table} as {$baseAlias}")
            ->select($selectedColumns);

        $pointsRows = collect(is_array($pointsMap) ? $pointsMap : [])
            ->filter(fn($row) => is_array($row))
            ->values();

        if ($pointsEnabled && $pointsSourceColumn !== '' && $pointsPlayerColumn !== '' && $pointsRows->isNotEmpty()) {
            $query->select([]);

            $effectiveBaseColumns = $baseColumns;
            if (!in_array($pointsPlayerColumn, $effectiveBaseColumns, true)) {
                array_unshift($effectiveBaseColumns, $pointsPlayerColumn);
            }

            foreach ($effectiveBaseColumns as $col) {
                $quoted = $this->quoteSqlIdentifier($col);
                if ($col === $pointsPlayerColumn) {
                    $query->addSelect(DB::raw("{$baseAlias}.{$quoted} as {$quoted}"));
                    $query->groupBy(DB::raw("{$baseAlias}.{$quoted}"));
                } else {
                    $query->addSelect(DB::raw("MAX({$baseAlias}.{$quoted}) as {$quoted}"));
                }
            }

            [$pointsCaseSql, $pointsCaseBindings] = $this->buildPointsCaseExpression($pointsSourceColumn, $pointsRows, $baseAlias);

            if ($pointsCaseSql !== '') {
                $query->selectRaw("SUM({$pointsCaseSql}) as " . self::UNIQUE_POINTS_OUTPUT_COLUMN, $pointsCaseBindings);
            } else {
                $query->selectRaw('0 as ' . self::UNIQUE_POINTS_OUTPUT_COLUMN);
            }
        }

        if (!$pointsEnabled && $orderBy !== '') {
            $query->orderBy("{$baseAlias}.{$orderBy}", $orderDirection);
        }

        if ($joinEnabled && $joinTable !== '' && $joinLocalKey !== '' && $joinForeignKey !== '' && $joinOutputColumn !== '') {
            if ($joinConnection !== '' && $joinConnection !== $connection) {
                $joinDbConfig = config("database.connections.{$joinConnection}", []);
                $joinDatabase = is_array($joinDbConfig) ? trim((string) ($joinDbConfig['database'] ?? '')) : '';
                $qualifiedJoinTable = $joinDatabase !== '' ? "{$joinDatabase}.dbo.{$joinTable}" : $joinTable;
            } else {
                $qualifiedJoinTable = $joinTable;
            }

            $query->leftJoin("{$qualifiedJoinTable} as _ranking_join", "_ranking_join.{$joinForeignKey}", '=', "{$baseAlias}.{$joinLocalKey}");

            if ($hasJoinOutput) {
                if ($pointsEnabled) {
                    $quotedOutput = $this->quoteSqlIdentifier($joinOutputColumn);
                    $query->addSelect(DB::raw('MAX(_ranking_join.' . $quotedOutput . ') as ' . self::UNIQUE_JOIN_OUTPUT_COLUMN));
                } else {
                    $query->addSelect(DB::raw('_ranking_join.' . $joinOutputColumn . ' as ' . self::UNIQUE_JOIN_OUTPUT_COLUMN));
                }
            }
        }

        if ($pointsEnabled) {
            $query->orderByDesc(self::UNIQUE_POINTS_OUTPUT_COLUMN);
        }

        if ($limit > 0) {
            $query->limit($limit);
        }

        return $query;
    }

    private function applySearch($query, array $columns): void
    {
        if ($this->search === '') {
            return;
        }

        $baseColumns = collect($columns)
            ->pluck('column')
            ->filter(fn($col) => is_string($col) && $col !== '' && !in_array($col, [self::UNIQUE_JOIN_OUTPUT_COLUMN, self::UNIQUE_POINTS_OUTPUT_COLUMN], true))
            ->values()
            ->all();

        if (empty($baseColumns)) {
            return;
        }

        $search = '%' . $this->search . '%';
        $query->where(function ($q) use ($baseColumns, $search) {
            foreach ($baseColumns as $col) {
                $q->orWhereRaw("CAST(ranking_base.{$this->quoteSqlIdentifier($col)} AS NVARCHAR(255)) LIKE ?", [$search]);
            }
        });
    }

    private function buildPointsCaseExpression(string $sourceColumn, Collection $pointsRows, string $baseAlias): array
    {
        $cases = [];
        $bindings = [];
        $quotedSource = $this->quoteSqlIdentifier($sourceColumn);

        foreach ($pointsRows as $row) {
            $key = trim((string) ($row['key'] ?? ''));
            $id = trim((string) ($row['id'] ?? ''));
            $name = trim((string) ($row['name'] ?? ''));
            $points = max(0, (int) ($row['points'] ?? 0));

            $matchValues = array_values(array_unique(array_filter([$key, $id, $name], fn(string $v) => $v !== '')));
            if ($matchValues === []) {
                continue;
            }

            $placeholders = implode(', ', array_fill(0, count($matchValues), '?'));
            $cases[] = "WHEN TRY_CAST({$baseAlias}.{$quotedSource} AS NVARCHAR(255)) IN ({$placeholders}) THEN ?";

            foreach ($matchValues as $value) {
                $bindings[] = $value;
            }
            $bindings[] = $points;
        }

        if ($cases === []) {
            return ['', []];
        }

        return ['CASE ' . implode(' ', $cases) . ' ELSE 0 END', $bindings];
    }

    private function quoteSqlIdentifier(string $identifier): string
    {
        return '[' . str_replace(']', ']]', $identifier) . ']';
    }
}
