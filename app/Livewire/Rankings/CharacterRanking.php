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
use SilkPanel\SilkroadModels\Models\Shard\Inventory;
use SilkPanel\SilkroadModels\Models\Shard\Items;
use SilkPanel\SilkroadModels\Models\Shard\RefObjCommon;

class CharacterRanking extends Component
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
        $title = Setting::get('ranking_chars_title', 'Character Ranking');
        $columns = Setting::get('ranking_chars_columns', [
            ['column' => 'CharName16', 'label' => 'Character Name'],
            ['column' => 'CurLevel', 'label' => 'Level'],
            ['column' => 'GuildName', 'label' => 'Guild Name'],
            ['column' => 'ItemPoints', 'label' => 'Item Points'],
        ]);
        $limit = (int) Setting::get('ranking_chars_limit', 50);
        $cacheTtl = (int) Setting::get('ranking_chars_cache_ttl', 60);
        $excluded = Setting::get('ranking_chars_excluded', []);

        $paginate = $limit === 0;

        if ($paginate) {
            $query = $this->buildQuery(0, $excluded);
            if ($this->search !== '') {
                $query->where(function ($q) {
                    $q->where('chars.CharName16', 'like', '%' . $this->search . '%')
                        ->orWhere('g.Name', 'like', '%' . $this->search . '%');
                });
            }
            $rows = $query->paginate($this->perPage);
            $startRank = $rows->firstItem() ?? 1;
        } else {
            $cacheKey = 'ranking.chars.' . md5(json_encode([$columns, $limit, $excluded]));
            $rows = Cache::remember($cacheKey, $cacheTtl * 60, function () use ($limit, $excluded) {
                return $this->buildQuery($limit, $excluded)->get();
            });
            if ($this->search !== '') {
                $search = mb_strtolower($this->search);
                $rows = $rows->filter(function ($row) use ($search) {
                    return str_contains(mb_strtolower($row->CharName16 ?? ''), $search)
                        || str_contains(mb_strtolower($row->GuildName ?? ''), $search);
                })->values();
            }
            $startRank = 1;
        }

        return view('livewire.rankings.character-ranking', [
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

        $invTable = (new Inventory)->getTable();
        $itemTable = (new Items)->getTable();
        $refTable = (new RefObjCommon)->getTable();
        $bindTable = (new BindingOptionWithItem)->getTable();
        $charTable = $charModel->getTable();
        $guildTable = (new Guild)->getTable();

        $itemPointsSub = DB::connection($connection)
            ->table("$invTable as inv")
            ->selectRaw(
                'ISNULL(SUM(b.nOptValue), 0)'
                    . ' + ISNULL(SUM(i.OptLevel), 0)'
                    . ' + ISNULL(SUM(r.ReqLevel1), 0)'
                    . ' + ISNULL(SUM(CASE WHEN r.CodeName128 LIKE \'%_A_RARE%\' THEN 5 ELSE 0 END), 0)'
                    . ' + ISNULL(SUM(CASE WHEN r.CodeName128 LIKE \'%_B_RARE%\' THEN 10 ELSE 0 END), 0)'
                    . ' + ISNULL(SUM(CASE WHEN r.CodeName128 LIKE \'%_C_RARE%\' THEN 15 ELSE 0 END), 0)'
            )
            ->join("$itemTable as i", 'i.ID64', '=', 'inv.ItemID')
            ->join("$refTable as r", 'r.ID', '=', 'i.RefItemID')
            ->leftJoin("$bindTable as b", function ($join) {
                $join->on('b.nItemDBID', '=', 'i.ID64')
                    ->where('b.bOptType', '=', 2)
                    ->where('b.nOptValue', '>', 0);
            })
            ->whereColumn('inv.CharID', 'chars.CharID')
            ->where('inv.Slot', '<', 13)
            ->whereNotIn('inv.Slot', [7, 8])
            ->where('inv.ItemID', '>', 0);

        return DB::connection($connection)
            ->table("$charTable as chars")
            ->select([
                'chars.CharID',
                'chars.CharName16',
                'chars.CurLevel',
                'chars.RefObjID',
                'g.ID as GuildID',
                'g.Name as GuildName',
            ])
            ->selectSub($itemPointsSub, 'ItemPoints')
            ->leftJoin("$guildTable as g", function ($join) {
                $join->on('g.ID', '=', 'chars.GuildID')
                    ->where('g.ID', '>', 0);
            })
            ->where('chars.deleted', 0)
            ->when(!empty($excluded), fn($q) => $q->whereNotIn('chars.CharID', $excluded))
            ->orderByDesc('ItemPoints')
            ->orderByDesc('chars.CurLevel')
            ->when($limit > 0, fn($q) => $q->limit($limit));
    }
}
