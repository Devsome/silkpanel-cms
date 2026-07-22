<?php

namespace App\Livewire\Histories;

use App\Enums\DatabaseNameEnums;
use App\Services\UniqueHistoryService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class UniqueHistory extends Component
{
    use WithPagination;

    /**
     * KILL_UNIQUE_MONSTER only (false) or KILL + SPAWN events (true).
     */
    public bool $showSpawns = true;

    protected int $perPage = 25;

    public function toggleSpawns(): void
    {
        $this->showSpawns = !$this->showSpawns;
        $this->resetPage();
    }

    public function render()
    {
        if (! UniqueHistoryService::isAvailable()) {
            return $this->unavailable();
        }

        // The unique display names come from config on iSRO; on vSRO the source
        // may already store readable names (the view falls back to the raw value).
        $uniques = collect(config('silkpanel.uniques', []))
            ->filter(fn ($cfg, $code) => is_array($cfg) && is_string($code) && $code !== '')
            ->all();

        try {
            // simplePaginate avoids an expensive COUNT(*) over the (very large) log table.
            if (config('silkpanel.version') === 'isro') {
                if ($uniques === []) {
                    return $this->unavailable();
                }

                $rows = $this->buildQuery(array_keys($uniques))->simplePaginate($this->perPage);
            } else {
                // vSRO / custom: the admin-configured source (Filament: Unique History VSRO).
                $query = app(UniqueHistoryService::class)->customQuery($this->showSpawns);

                if ($query === null) {
                    return $this->unavailable();
                }

                $rows = $query->simplePaginate($this->perPage);
            }
        } catch (\Throwable) {
            return $this->unavailable();
        }

        return view('template::livewire.histories.unique-history', [
            'uniques' => $uniques,
            'rows' => $rows,
            'available' => true,
            'showArea' => UniqueHistoryService::areaAvailable(),
        ]);
    }

    private function unavailable()
    {
        return view('template::livewire.histories.unique-history', [
            'uniques' => [],
            'rows' => collect(),
            'available' => false,
            'showArea' => true,
        ]);
    }

    private function buildQuery(array $uniqueCodes)
    {
        $shardDb = DB::connection(DatabaseNameEnums::SRO_SHARD->value)->getDatabaseName();

        return DB::connection(DatabaseNameEnums::SRO_LOG->value)
            ->table('dbo._LogInstanceWorldInfo as log')
            ->select([
                'log.CharID',
                'c.CharName16',
                'c.RefObjID',
                'c.CurLevel',
                'log.ValueCodeName128',
                'log.Value',
                'log.WorldID',
                'r.AreaName',
                'log.EventTime',
            ])
            ->leftJoin("{$shardDb}.dbo._Char as c", 'c.CharID', '=', 'log.CharID')
            ->leftJoin("{$shardDb}.dbo._RefRegion as r", 'r.wRegionID', '=', 'log.WorldID')
            ->whereIn('log.Value', $uniqueCodes)
            ->when(
                $this->showSpawns,
                fn ($query) => $query->whereIn('log.ValueCodeName128', ['KILL_UNIQUE_MONSTER', 'SPAWN_UNIQUE_MONSTER']),
                fn ($query) => $query->where('log.ValueCodeName128', 'KILL_UNIQUE_MONSTER'),
            )
            ->orderByDesc('log.EventTime');
    }
}
