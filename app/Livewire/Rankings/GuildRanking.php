<?php

namespace App\Livewire\Rankings;

use App\Helpers\CrestHelper;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use SilkPanel\SilkroadModels\Models\Shard\AbstractChar;
use SilkPanel\SilkroadModels\Models\Shard\BindingOptionWithItem;
use SilkPanel\SilkroadModels\Models\Shard\Guild;
use SilkPanel\SilkroadModels\Models\Shard\GuildCrest;
use SilkPanel\SilkroadModels\Models\Shard\GuildMember;
use SilkPanel\SilkroadModels\Models\Shard\Inventory;
use SilkPanel\SilkroadModels\Models\Shard\Items;
use SilkPanel\SilkroadModels\Models\Shard\RefObjCommon;

class GuildRanking extends Component
{
    use WithPagination;

    public string $search = '';

    protected $queryString = ['search'];

    protected int $perPage = 25;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $title = Setting::get('ranking_guilds_title', 'Guild Ranking');
        $columns = Setting::get('ranking_guilds_columns', [
            ['column' => 'Name', 'label' => 'Guild Name'],
            ['column' => 'Lvl', 'label' => 'Guild Level'],
            ['column' => 'LeaderName', 'label' => 'Leader Name'],
            ['column' => 'TotalMember', 'label' => 'Members'],
            ['column' => 'ItemPoints', 'label' => 'Item Points'],
        ]);
        $limit = (int) Setting::get('ranking_guilds_limit', 50);
        $cacheTtl = (int) Setting::get('ranking_guilds_cache_ttl', 60);
        $excluded = Setting::get('ranking_guilds_excluded', []);
        $search = trim($this->search);
        $hasSearch = $search !== '';
        $memberTable = (new GuildMember)->getTable();

        // Always search through the full dataset, even when a ranking limit is configured.
        $paginate = $limit === 0 || $hasSearch;

        if ($paginate) {
            $query = $this->buildQuery($hasSearch ? 0 : $limit, $excluded);
            if ($hasSearch) {
                $query->where(function ($q) use ($search, $memberTable) {
                    $q->where('guilds.Name', 'like', '%' . $search . '%')
                        ->orWhereExists(function ($exists) use ($memberTable, $search) {
                            $exists->select(DB::raw(1))
                                ->from("$memberTable as gm_search")
                                ->whereColumn('gm_search.GuildID', 'guilds.ID')
                                ->where('gm_search.MemberClass', 0)
                                ->where('gm_search.CharName', 'like', '%' . $search . '%');
                        });
                });
            }
            $rows = $query->paginate($this->perPage);
            $startRank = $rows->firstItem() ?? 1;
            $rows->getCollection()->transform(function ($row) {
                if (!empty($row->CrestIcon)) {
                    $row->CrestDataUri = CrestHelper::decodeHexToDataUri($row->CrestIcon);
                }
                return $row;
            });
        } else {
            $cacheKey = 'ranking.guilds.' . md5(json_encode([$columns, $limit, $excluded]));
            $rows = Cache::remember($cacheKey, $cacheTtl * 60, function () use ($limit, $excluded) {
                return $this->buildQuery($limit, $excluded)->get();
            });
            $rows = $rows->map(function ($row) {
                if (!empty($row->CrestIcon)) {
                    $row->CrestDataUri = CrestHelper::decodeHexToDataUri($row->CrestIcon);
                }
                return $row;
            });
            $startRank = 1;
        }

        return view('template::livewire.rankings.guild-ranking', [
            'title' => $title,
            'columns' => $columns,
            'rows' => $rows,
            'paginate' => $paginate,
            'startRank' => $startRank,
        ]);
    }

    private function buildQuery(int $limit, array $excluded)
    {
        /** @var AbstractChar $charModel */
        $charModel = app(AbstractChar::class);
        $connection = $charModel->getConnectionName();
        $version = (string) config('silkpanel.version', 'vsro');

        $guildTable = (new Guild)->getTable();
        $memberTable = (new GuildMember)->getTable();
        $invTable = (new Inventory)->getTable();
        $itemTable = (new Items)->getTable();
        $refTable = (new RefObjCommon)->getTable();
        $bindTable = (new BindingOptionWithItem)->getTable();
        $crestTable = (new GuildCrest)->getTable();

        $leaderIdSub = DB::connection($connection)
            ->table("$memberTable as gm")
            ->select('gm.CharID')
            ->whereColumn('gm.GuildID', 'guilds.ID')
            ->where('gm.MemberClass', 0)
            ->limit(1);

        $leaderNameSub = DB::connection($connection)
            ->table("$memberTable as gm")
            ->select('gm.CharName')
            ->whereColumn('gm.GuildID', 'guilds.ID')
            ->where('gm.MemberClass', 0)
            ->limit(1);

        $totalMemberSub = DB::connection($connection)
            ->table("$memberTable as gm")
            ->selectRaw('COUNT(*)')
            ->whereColumn('gm.GuildID', 'guilds.ID');

        $itemPointsSub = DB::connection($connection)
            ->table("$memberTable as gm")
            ->selectRaw(
                'ISNULL(SUM(b.nOptValue), 0)'
                    . ' + ISNULL(SUM(i.OptLevel), 0)'
                    . ' + ISNULL(SUM(r.ReqLevel1), 0)'
                    . ' + ISNULL(SUM(CASE WHEN r.CodeName128 LIKE \'%_A_RARE%\' THEN 5 ELSE 0 END), 0)'
                    . ' + ISNULL(SUM(CASE WHEN r.CodeName128 LIKE \'%_B_RARE%\' THEN 10 ELSE 0 END), 0)'
                    . ' + ISNULL(SUM(CASE WHEN r.CodeName128 LIKE \'%_C_RARE%\' THEN 15 ELSE 0 END), 0)'
            )
            ->join("$invTable as inv", 'inv.CharID', '=', 'gm.CharID')
            ->join("$itemTable as i", 'i.ID64', '=', 'inv.ItemID')
            ->join("$refTable as r", 'r.ID', '=', 'i.RefItemID')
            ->leftJoin("$bindTable as b", function ($join) {
                $join->on('b.nItemDBID', '=', 'i.ID64')
                    ->where('b.bOptType', '=', 2)
                    ->where('b.nOptValue', '>', 0);
            })
            ->whereColumn('gm.GuildID', 'guilds.ID')
            ->where('inv.Slot', '<', 13)
            ->whereNotIn('inv.Slot', [7, 8])
            ->where('inv.ItemID', '>', 0);

        $query = DB::connection($connection)
            ->table("$guildTable as guilds")
            ->select([
                'guilds.ID',
                'guilds.Name',
                'guilds.Lvl',
                'guilds.GatheredSP',
                'guilds.FoundationDate',
            ])
            ->selectSub($leaderIdSub, 'LeaderID')
            ->selectSub($leaderNameSub, 'LeaderName')
            ->selectSub($totalMemberSub, 'TotalMember')
            ->selectSub($itemPointsSub, 'ItemPoints')
            ->where('guilds.ID', '>', 0)
            ->when(!empty($excluded), fn($q) => $q->whereNotIn('guilds.ID', $excluded))
            ->orderByDesc('ItemPoints')
            ->orderByDesc('guilds.Lvl');

        if ($version === 'isro') {
            $query
                ->leftJoin("$crestTable as gc", 'gc.GuildID', '=', 'guilds.ID')
                ->addSelect(DB::raw('CONVERT(VARCHAR(MAX), gc.CrestBinary, 2) as CrestIcon'));
        }

        return $query->when($limit > 0, fn($q) => $q->limit($limit));
    }
}
