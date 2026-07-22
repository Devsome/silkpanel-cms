<?php

namespace App\Livewire\Histories;

use App\Enums\DatabaseNameEnums;
use App\Services\GlobalsService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class GlobalHistory extends Component
{
    use WithPagination;

    /**
     * Trade filter: '' = all, 'WTS' = want to sell, 'WTB' = want to buy.
     */
    public string $tradeFilter = '';

    protected int $perPage = 25;

    public function updatedTradeFilter(): void
    {
        // Only allow the whitelisted values; anything else falls back to "all".
        if (!in_array($this->tradeFilter, ['WTS', 'WTB'], true)) {
            $this->tradeFilter = '';
        }

        $this->resetPage();
    }

    public function render()
    {
        if (! GlobalsService::isAvailable()) {
            return $this->unavailable();
        }

        try {
            // simplePaginate avoids an expensive COUNT(*) over the (very large) log table.
            if (config('silkpanel.version') === 'isro') {
                $rows = $this->buildQuery()->simplePaginate($this->perPage);
            } else {
                // vSRO / custom: the admin-configured source (Filament: Global History VSRO).
                $query = app(GlobalsService::class)->customQuery($this->tradeFilter ?: null);

                if ($query === null) {
                    return $this->unavailable();
                }

                $rows = $query->simplePaginate($this->perPage);
            }
        } catch (\Throwable) {
            return $this->unavailable();
        }

        return view('template::livewire.histories.global-history', [
            'rows' => $rows,
            'available' => true,
        ]);
    }

    private function unavailable()
    {
        return view('template::livewire.histories.global-history', [
            'rows' => collect(),
            'available' => false,
        ]);
    }

    private function buildQuery()
    {
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
            ->when(in_array($this->tradeFilter, ['WTS', 'WTB'], true), function ($query) {
                $query->where('log.Comment', 'like', '%' . $this->tradeFilter . '%');
            })
            ->orderByDesc('log.EventTime');
    }
}
