<?php

namespace App\Livewire\Rankings;

use App\Models\Setting;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;
use Throwable;

class CustomRanking extends Component
{
    use WithPagination;

    public ?string $rankingKey = null;

    protected int $defaultPerPage = 25;

    public function mount(?string $rankingKey = null): void
    {
        if (is_string($rankingKey) && trim($rankingKey) !== '') {
            $this->rankingKey = trim($rankingKey);
        }
    }

    public function updatingRankingKey(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $rankings = collect(Setting::get('ranking_custom_rankings', []))
            ->filter(fn(mixed $row): bool => is_array($row))
            ->map(fn(array $row): array => $this->normalizeRanking($row))
            ->filter(fn(array $row): bool => $row['enabled'] && $row['key'] !== '' && $row['connection'] !== '' && $this->hasExecutableSource($row))
            ->values();

        if ($rankings->isEmpty()) {
            return view('template::livewire.rankings.custom-ranking', [
                'configured' => false,
                'title' => 'Custom Ranking',
                'rankings' => [],
                'columns' => [],
                'rows' => collect(),
                'paginate' => false,
                'startRank' => 1,
                'hasSorting' => false,
            ]);
        }

        $rankingOptions = $rankings
            ->mapWithKeys(fn(array $row): array => [$row['key'] => $row['title'] !== '' ? $row['title'] : $row['key']])
            ->all();

        $activeRanking = $rankings->first(fn(array $row): bool => $row['key'] === $this->rankingKey);
        if (! is_array($activeRanking)) {
            $activeRanking = $rankings->first();
            $this->rankingKey = (string) ($activeRanking['key'] ?? '');
        }

        if (! is_array($activeRanking)) {
            return view('template::livewire.rankings.custom-ranking', [
                'configured' => false,
                'title' => 'Custom Ranking',
                'rankings' => [],
                'columns' => [],
                'rows' => collect(),
                'paginate' => false,
                'startRank' => 1,
                'hasSorting' => false,
            ]);
        }

        $cacheSeconds = max(0, (int) $activeRanking['cache_ttl_hours']) * 3600;
        $title = $activeRanking['title'] !== '' ? $activeRanking['title'] : 'Custom Ranking';

        try {
            if ($activeRanking['pagination_enabled']) {
                $page = max(1, (int) $this->getPage());
                $perPage = max(1, (int) $activeRanking['per_page']);

                $cacheKey = 'ranking.custom.paginated.' . md5(json_encode([
                    $activeRanking['key'],
                    $activeRanking['connection'],
                    $activeRanking['source_type'],
                    $activeRanking['query'],
                    $activeRanking['procedure_name'],
                    $activeRanking['procedure_params'],
                    $activeRanking['sorting_enabled'],
                    $activeRanking['default_sort_column'],
                    $activeRanking['default_sort_direction'],
                    $page,
                    $perPage,
                ]));

                $result = $cacheSeconds > 0
                    ? Cache::remember($cacheKey, $cacheSeconds, fn(): array => $this->runPaginatedQuery($activeRanking, $page, $perPage))
                    : $this->runPaginatedQuery($activeRanking, $page, $perPage);

                $rowsCollection = collect($result['items'])->map(fn(array $row): object => (object) $row);

                $rows = new LengthAwarePaginator(
                    $rowsCollection,
                    (int) $result['total'],
                    $perPage,
                    $page,
                    [
                        'path' => request()->url(),
                        'pageName' => 'page',
                    ]
                );

                $startRank = $rows->firstItem() ?? 1;
                $paginate = true;
                $columns = $this->mapColumns($result['columns']);
            } else {
                $cacheKey = 'ranking.custom.static.' . md5(json_encode([
                    $activeRanking['key'],
                    $activeRanking['connection'],
                    $activeRanking['source_type'],
                    $activeRanking['query'],
                    $activeRanking['procedure_name'],
                    $activeRanking['procedure_params'],
                    $activeRanking['sorting_enabled'],
                    $activeRanking['default_sort_column'],
                    $activeRanking['default_sort_direction'],
                    $activeRanking['limit'],
                ]));

                $result = $cacheSeconds > 0
                    ? Cache::remember($cacheKey, $cacheSeconds, fn(): array => $this->runStaticQuery($activeRanking))
                    : $this->runStaticQuery($activeRanking);

                $rows = collect($result['items'])->map(fn(array $row): object => (object) $row);
                $startRank = 1;
                $paginate = false;
                $columns = $this->mapColumns($result['columns']);
            }
        } catch (Throwable) {
            return view('template::livewire.rankings.custom-ranking', [
                'configured' => false,
                'title' => $title,
                'rankings' => $rankingOptions,
                'columns' => [],
                'rows' => collect(),
                'paginate' => false,
                'startRank' => 1,
                'hasSorting' => false,
            ]);
        }

        return view('template::livewire.rankings.custom-ranking', [
            'configured' => true,
            'title' => $title,
            'rankings' => $rankingOptions,
            'columns' => $columns,
            'rows' => $rows,
            'paginate' => $paginate,
            'startRank' => $startRank,
            'hasSorting' => (bool) $activeRanking['sorting_enabled'],
        ]);
    }

    /**
     * @param array<string, mixed> $ranking
     * @return array{items: array<int, array<string, mixed>>, total: int, columns: array<int, string>}
     */
    private function runPaginatedQuery(array $ranking, int $page, int $perPage): array
    {
        if ($ranking['source_type'] === 'procedure') {
            return $this->runCollectionPaginatedResult($ranking, $page, $perPage);
        }

        $connection = (string) $ranking['connection'];
        $baseSql = trim((string) $ranking['query']);

        $countSql = "SELECT COUNT(1) AS aggregate FROM ({$baseSql}) AS ranking_custom_count";
        $countRow = DB::connection($connection)->selectOne($countSql);
        $total = (int) (($countRow->aggregate ?? 0));

        $offset = max(0, ($page - 1) * $perPage);
        $orderBySql = $this->buildOrderBySql($ranking, allowFallback: true);

        $dataSql = "SELECT * FROM ({$baseSql}) AS ranking_custom {$orderBySql} OFFSET {$offset} ROWS FETCH NEXT {$perPage} ROWS ONLY";
        $rows = DB::connection($connection)->select($dataSql);

        $items = collect($rows)
            ->map(fn(object $row): array => (array) $row)
            ->values()
            ->all();

        $columns = array_keys($items[0] ?? []);

        return [
            'items' => $items,
            'total' => $total,
            'columns' => $columns,
        ];
    }

    /**
     * @param array<string, mixed> $ranking
     * @return array{items: array<int, array<string, mixed>>, columns: array<int, string>}
     */
    private function runStaticQuery(array $ranking): array
    {
        if ($ranking['source_type'] === 'procedure') {
            return $this->runCollectionStaticResult($ranking);
        }

        $connection = (string) $ranking['connection'];
        $baseSql = trim((string) $ranking['query']);
        $limit = max(0, (int) $ranking['limit']);

        $topSql = $limit > 0 ? 'TOP ' . $limit . ' ' : '';
        $orderBySql = $this->buildOrderBySql($ranking, allowFallback: false);

        $dataSql = "SELECT {$topSql}* FROM ({$baseSql}) AS ranking_custom {$orderBySql}";
        $rows = DB::connection($connection)->select($dataSql);

        $items = collect($rows)
            ->map(fn(object $row): array => (array) $row)
            ->values()
            ->all();

        $columns = array_keys($items[0] ?? []);

        return [
            'items' => $items,
            'columns' => $columns,
        ];
    }

    /**
     * @param array<string, mixed> $ranking
     * @return array{items: array<int, array<string, mixed>>, total: int, columns: array<int, string>}
     */
    private function runCollectionPaginatedResult(array $ranking, int $page, int $perPage): array
    {
        $rows = $this->fetchProcedureRows($ranking);
        $sortedRows = $this->applyCollectionSorting($rows, $ranking);

        $total = $sortedRows->count();
        $items = $sortedRows
            ->slice(max(0, ($page - 1) * $perPage), $perPage)
            ->values()
            ->all();

        return [
            'items' => $items,
            'total' => $total,
            'columns' => array_keys($items[0] ?? []),
        ];
    }

    /**
     * @param array<string, mixed> $ranking
     * @return array{items: array<int, array<string, mixed>>, columns: array<int, string>}
     */
    private function runCollectionStaticResult(array $ranking): array
    {
        $rows = $this->fetchProcedureRows($ranking);
        $sortedRows = $this->applyCollectionSorting($rows, $ranking);
        $limit = max(0, (int) $ranking['limit']);

        $items = ($limit > 0 ? $sortedRows->take($limit) : $sortedRows)
            ->values()
            ->all();

        return [
            'items' => $items,
            'columns' => array_keys($items[0] ?? []),
        ];
    }

    /**
     * @param array<string, mixed> $ranking
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    private function fetchProcedureRows(array $ranking)
    {
        $connection = (string) $ranking['connection'];
        $procedureName = trim((string) $ranking['procedure_name']);
        $procedureParams = trim((string) $ranking['procedure_params']);

        $sql = 'EXEC ' . $procedureName;
        if ($procedureParams !== '') {
            $sql .= ' ' . $procedureParams;
        }

        return collect(DB::connection($connection)->select($sql))
            ->map(fn(object $row): array => (array) $row)
            ->values();
    }

    /**
     * @param \Illuminate\Support\Collection<int, array<string, mixed>> $rows
     * @param array<string, mixed> $ranking
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    private function applyCollectionSorting($rows, array $ranking)
    {
        $sortingEnabled = (bool) ($ranking['sorting_enabled'] ?? false);
        $column = trim((string) ($ranking['default_sort_column'] ?? ''));
        $direction = strtolower((string) ($ranking['default_sort_direction'] ?? 'desc')) === 'asc' ? 'asc' : 'desc';

        if (! $sortingEnabled || $column === '') {
            return $rows->values();
        }

        return $rows
            ->sortBy(fn(array $row): mixed => $row[$column] ?? null, options: SORT_NATURAL, descending: $direction === 'desc')
            ->values();
    }

    /**
     * @param array<string, mixed> $ranking
     */
    private function hasExecutableSource(array $ranking): bool
    {
        return $ranking['source_type'] === 'procedure'
            ? trim((string) ($ranking['procedure_name'] ?? '')) !== ''
            : trim((string) ($ranking['query'] ?? '')) !== '';
    }

    /**
     * @param array<string, mixed> $ranking
     */
    private function buildOrderBySql(array $ranking, bool $allowFallback): string
    {
        $sortingEnabled = (bool) ($ranking['sorting_enabled'] ?? false);
        $column = trim((string) ($ranking['default_sort_column'] ?? ''));
        $direction = strtolower((string) ($ranking['default_sort_direction'] ?? 'desc')) === 'asc' ? 'asc' : 'desc';

        if ($sortingEnabled && $column !== '') {
            return 'ORDER BY ' . $this->quoteSqlIdentifier($column) . ' ' . $direction;
        }

        return $allowFallback ? 'ORDER BY (SELECT 1)' : '';
    }

    /**
     * @param array<int, string> $columns
     * @return array<int, array{column: string, label: string}>
     */
    private function mapColumns(array $columns): array
    {
        return collect($columns)
            ->filter(fn(mixed $column): bool => is_string($column) && $column !== '')
            ->map(fn(string $column): array => [
                'column' => $column,
                'label' => Str::of($column)->replace('_', ' ')->headline()->toString(),
            ])
            ->values()
            ->all();
    }

    /**
     * @param array<string, mixed> $row
     * @return array<string, mixed>
     */
    private function normalizeRanking(array $row): array
    {
        return [
            'key' => trim((string) ($row['key'] ?? '')),
            'title' => trim((string) ($row['title'] ?? '')),
            'enabled' => (bool) ($row['enabled'] ?? true),
            'source_type' => in_array(($row['source_type'] ?? 'query'), ['query', 'procedure'], true) ? (string) $row['source_type'] : 'query',
            'connection' => trim((string) ($row['connection'] ?? '')),
            'query' => trim((string) ($row['query'] ?? '')),
            'procedure_name' => trim((string) ($row['procedure_name'] ?? '')),
            'procedure_params' => trim((string) ($row['procedure_params'] ?? '')),
            'sorting_enabled' => (bool) ($row['sorting_enabled'] ?? true),
            'default_sort_column' => trim((string) ($row['default_sort_column'] ?? '')),
            'default_sort_direction' => strtolower((string) ($row['default_sort_direction'] ?? 'desc')) === 'asc' ? 'asc' : 'desc',
            'pagination_enabled' => (bool) ($row['pagination_enabled'] ?? true),
            'per_page' => max(1, min(200, (int) ($row['per_page'] ?? $this->defaultPerPage))),
            'limit' => max(0, (int) ($row['limit'] ?? 100)),
            'cache_ttl_hours' => max(0, (int) ($row['cache_ttl_hours'] ?? 1)),
        ];
    }

    private function quoteSqlIdentifier(string $identifier): string
    {
        return '[' . str_replace(']', ']]', $identifier) . ']';
    }
}
