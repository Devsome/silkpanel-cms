<?php

namespace App\Filament\Pages;

use App\Enums\DatabaseNameEnums;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MonsterDrops extends Page
{
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

        $trans = 'SILKROAD_R_ACCOUNT.dbo._Rigid_ItemNameDesc';

        return DB::connection(DatabaseNameEnums::SRO_SHARD->value)
            ->table('_RefObjCommon as m')
            ->join('_RefObjChar as c', 'c.ID', '=', 'm.Link')
            ->leftJoin("{$trans} as mn", 'mn.StrID', '=', 'm.NameStrID128')
            ->where('m.TypeID1', 1) // TypeID1=1 = Monster/NPC/Char objects
            ->where(function ($q) {
                $q->where('m.CodeName128', 'like', "%{$this->search}%")
                  ->orWhere('m.ObjName128', 'like', "%{$this->search}%")
                  ->orWhere('mn.ENG', 'like', "%{$this->search}%");
            })
            ->select(
                'm.ID',
                'm.CodeName128',
                'm.ObjName128',
                'm.Rarity',
                'mn.ENG as NameENG',
                'c.Lvl',
            )
            ->orderBy('c.Lvl')
            ->limit(50)
            ->get();
    }

    public static function getDetailUrl(string $code): string
    {
        return route('filament.admin.pages.monster-drop-detail', ['code' => $code]);
    }
}
