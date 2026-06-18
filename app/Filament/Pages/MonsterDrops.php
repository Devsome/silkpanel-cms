<?php

namespace App\Filament\Pages;

use App\Enums\DatabaseNameEnums;
use App\Filament\Concerns\HasNameTranslation;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MonsterDrops extends Page
{
    use HasNameTranslation;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCubeTransparent;

    protected static string|\UnitEnum|null $navigationGroup = 'Silkroad';

    protected static ?int $navigationSort = 11;

    protected string $view = 'filament.pages.monster-drops';

    public string $search = '';

    public static function getNavigationLabel(): string
    {
        return __('Monster Drops');
    }

    public function getTitle(): string
    {
        return __('Monster Drops');
    }

    public static function rarityLabel(int $rarity): string
    {
        return match($rarity) {
            3       => 'Unique',
            5       => 'Giant',
            default => '',
        };
    }

    public static function rarityColor(int $rarity): string
    {
        return match($rarity) {
            3       => 'warning',
            5       => 'danger',
            default => 'gray',
        };
    }

    public function getStats(): array
    {
        $db = DB::connection(DatabaseNameEnums::SRO_SHARD->value);

        $namedGroupItems    = $db->table('_RefDropItemGroup')->count();
        $disabledGroupItems = $db->table('_RefDropItemGroup as dg')
            ->join('_RefObjCommon as i', 'i.ID', '=', 'dg.RefItemID')
            ->where('i.CanDrop', 0)->count();

        return [
            'monsters_with_direct'  => $db->table('_RefMonster_AssignedItemDrop')->distinct()->count('RefMonsterID'),
            'monsters_with_groups'  => $db->table('_RefMonster_AssignedItemRndDrop')->distinct()->count('RefMonsterID'),
            'named_groups'          => $db->table('_RefDropItemGroup')->distinct()->count('RefItemGroupID'),
            'named_group_items'     => $namedGroupItems,
            'named_group_disabled'  => $disabledGroupItems,
            'tiered_pools'          => $db->table('_RefDropItemAssign')->where('AssignedGroup', '>', 0)->distinct()->count('AssignedGroup'),
            'tiered_pool_items'     => $db->table('_RefDropItemAssign')->where('AssignedGroup', '>', 0)->count(),
        ];
    }

    public function getMonsters(): Collection
    {
        if (strlen($this->search) < 2) {
            return collect();
        }

        $search = $this->search;
        $isro   = static::transTable() !== null;

        $q = DB::connection(DatabaseNameEnums::SRO_SHARD->value)
            ->table('_RefObjCommon as m')
            ->join('_RefObjChar as c', 'c.ID', '=', 'm.Link')
            ->where('m.TypeID1', 1); // TypeID1=1 = Monster/NPC/Char objects

        static::joinTranslation($q, 'mn', 'm.NameStrID128');

        $q->where(function ($inner) use ($search, $isro) {
            $inner->where('m.CodeName128', 'like', "%{$search}%")
                  ->orWhere('m.ObjName128', 'like', "%{$search}%");
            if ($isro) {
                $inner->orWhere('mn.ENG', 'like', "%{$search}%");
            }
        });

        $nameCol = $isro ? 'mn.ENG as NameENG' : DB::raw('NULL as NameENG');

        $rows = $q->select(
                'm.ID',
                'm.CodeName128',
                'm.NameStrID128',
                'm.Rarity',
                $nameCol,
                'c.Lvl',
            )
            ->orderBy('c.Lvl')
            ->limit(50)
            ->get();

        return static::resolveNames($rows, 'NameStrID128', 'NameENG');
    }

    public static function getDetailUrl(string $code): string
    {
        return route('filament.admin.pages.monster-drop-detail', ['code' => $code]);
    }
}
