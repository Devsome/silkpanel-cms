<?php

namespace App\Models;

use App\Enums\DatabaseNameEnums;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MonsterDrop extends Model
{
    protected $connection = DatabaseNameEnums::SRO_SHARD->value;
    protected $table = '_RefMonster_AssignedItemDrop';
    public $timestamps = false;

    // Cross-database name translation table (sro_account)
    private const TRANS_TABLE = 'SILKROAD_R_ACCOUNT.dbo._Rigid_ItemNameDesc';

    public function scopeWithNames(Builder $query): Builder
    {
        $t = $this->table;
        $trans = self::TRANS_TABLE;

        return $query
            ->from("{$t} as d")
            ->join('_RefObjCommon as monster', 'monster.ID', '=', 'd.RefMonsterID')
            ->join('_RefObjCommon as item', 'item.ID', '=', 'd.RefItemID')
            ->leftJoin("{$trans} as mn", 'mn.StrID', '=', 'monster.NameStrID128')
            ->leftJoin("{$trans} as itn", 'itn.StrID', '=', 'item.NameStrID128')
            ->select(
                'd.RefMonsterID',
                'd.RefItemID',
                'd.DropRatio',
                'd.DropAmountMin',
                'd.DropAmountMax',
                'monster.CodeName128 as MonsterCode',
                'monster.ObjName128 as MonsterNameRaw',
                'mn.ENG as MonsterNameENG',
                'item.CodeName128 as ItemCode',
                'item.ObjName128 as ItemNameRaw',
                'itn.ENG as ItemNameENG',
            );
    }

    public function scopeForMonster(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('monster.CodeName128', 'like', "%{$search}%")
              ->orWhere('monster.ObjName128', 'like', "%{$search}%")
              ->orWhere('mn.ENG', 'like', "%{$search}%");
        });
    }
}
