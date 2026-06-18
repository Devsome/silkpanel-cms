<?php

namespace App\Filament\Pages;

use App\Enums\DatabaseNameEnums;
use App\Filament\Concerns\HasNameTranslation;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Support\Htmlable;

class MonsterDropDetail extends Page
{
    use HasNameTranslation;
    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.pages.monster-drop-detail';

    public string $code = '';

    // ProbGroup number → _RefDropItemGroup.RefItemGroupID (specific named pools)
    private const DROP_CLASS_ITEM_GROUP = [
        '_RefDropClassSel_Equip'               => 'Equipment',
        '_RefDropClassSel_RareEquip'            => 'Rare Equipment',
        '_RefDropClassSel_Coin'                 => 'Coin / Token',
        '_RefDropClassSel_Specialty'            => 'Specialty',
        '_RefDropClassSel_COSEquip'             => 'COS Equipment',
        '_RefDropClassSel_COSMagicStone'        => 'COS MagicStone',
        '_RefDropClassSel_COSPotion'            => 'COS Potion',
        '_RefDropClassSel_COSSkill'             => 'COS Skill',
        '_RefDropClassSel_COSSkillRemove'       => 'COS Skill Remove',
    ];

    // ProbGroup number → _RefDropItemAssign.AssignedGroup (tiered consumable/alchemy pools)
    private const DROP_CLASS_ITEM_ASSIGN = [
        '_RefDropClassSel_Ammo'                 => 'Ammunition',
        '_RefDropClassSel_Recover'              => 'Recovery (HP/MP)',
        '_RefDropClassSel_Cure'                 => 'Cure',
        '_RefDropClassSel_NewCure'              => 'New Cure',
        '_RefDropClassSel_Scroll'               => 'Scrolls',
        '_RefDropClassSel_Reinforce'            => 'Reinforce',
        '_RefDropClassSel_LimitReinforce'       => 'Limit Reinforce',
        '_RefDropClassSel_Alchemy_ATTRStone'    => 'Alchemy (ATTRStone)',
        '_RefDropClassSel_Alchemy_Enhancer'     => 'Alchemy (Enhancer)',
        '_RefDropClassSel_Alchemy_MagicStone'   => 'Alchemy (MagicStone)',
        '_RefDropClassSel_Alchemy_Tablet'       => 'Alchemy (Tablet)',
        '_RefDropClassSel_Alchemy_UpgradeStone' => 'Alchemy (UpgradeStone)',
    ];

    protected static ?string $slug = 'monster-drops/{code}';

    public static function routes(\Filament\Panel $panel, ?\Filament\Pages\PageConfiguration $configuration = null): void
    {
        $middleware = static::getRouteMiddleware($panel);

        \Illuminate\Support\Facades\Route::get('/monster-drops/{code}', static::class)
            ->middleware($middleware)
            ->withoutMiddleware(static::getWithoutRouteMiddleware($panel))
            ->name('monster-drop-detail');
    }

    public function mount(string $code): void
    {
        $this->code = $code;
    }

    public function getTitle(): string|Htmlable
    {
        $monster = $this->getMonster();
        if (!$monster) return 'Monster not found';

        $name = ($monster->NameENG && $monster->NameENG !== '0') ? $monster->NameENG : $monster->CodeName128;
        return "Drops: {$name}";
    }

    public function getMonster(): ?object
    {
        $q = DB::connection(DatabaseNameEnums::SRO_SHARD->value)
            ->table('_RefObjCommon as m')
            ->join('_RefObjChar as c', 'c.ID', '=', 'm.Link')
            ->where('m.CodeName128', $this->code);

        static::joinTranslation($q, 'mn', 'm.NameStrID128');

        $row = $q->select('m.ID', 'm.CodeName128', 'm.ObjName128', 'm.Rarity', 'm.NameStrID128', static::engSelect('mn', 'NameENG'), 'c.Lvl', 'c.MaxHP', 'c.ExpToGive')
            ->first();

        if ($row) {
            static::resolveNames(collect([$row]), 'NameStrID128', 'NameENG');
        }

        return $row;
    }

    /** Direct assigned drops (special/unique materials) */
    public function getAssignedDrops(): Collection
    {
        $monster = $this->getMonster();
        if (!$monster) return collect();

        $q = DB::connection(DatabaseNameEnums::SRO_SHARD->value)
            ->table('_RefMonster_AssignedItemDrop as d')
            ->join('_RefObjCommon as i', 'i.ID', '=', 'd.RefItemID')
            ->where('d.RefMonsterID', $monster->ID);

        static::joinTranslation($q, 'itn', 'i.NameStrID128');

        $rows = $q->select(
                'i.CodeName128 as ItemCode',
                'i.NameStrID128 as NameStrID128',
                static::engSelect('itn', 'ItemNameENG'),
                'd.DropRatio',
                'd.DropAmountMin',
                'd.DropAmountMax',
            )
            ->orderByDesc('d.DropRatio')
            ->get();

        return static::resolveNames($rows, 'NameStrID128', 'ItemNameENG');
    }

    /**
     * Random group drops assigned directly to this monster (_RefMonster_AssignedItemRndDrop).
     * Returns groups with their items and the group-level drop probability.
     */
    public function getAssignedGroupDrops(): array
    {
        $monster = $this->getMonster();
        if (!$monster) return [];

        $db = DB::connection(DatabaseNameEnums::SRO_SHARD->value);

        $assignments = $db->table('_RefMonster_AssignedItemRndDrop')
            ->where('RefMonsterID', $monster->ID)
            ->get();

        if ($assignments->isEmpty()) return [];

        $result = [];

        foreach ($assignments as $assignment) {
            $q = $db->table('_RefDropItemGroup as dg')
                ->join('_RefObjCommon as i', 'i.ID', '=', 'dg.RefItemID')
                ->where('dg.RefItemGroupID', $assignment->RefItemGroupID);

            static::joinTranslation($q, 'itn', 'i.NameStrID128');

            $items = $q
                ->select(
                    'dg.CodeName128 as GroupCode',
                    'i.CodeName128 as ItemCode',
                    'i.NameStrID128 as NameStrID128',
                    static::engSelect('itn', 'ItemNameENG'),
                    'dg.SelectRatio',
                    'i.CanDrop',
                )
                ->orderByDesc('dg.SelectRatio')
                ->get();

            static::resolveNames($items, 'NameStrID128', 'ItemNameENG');

            if ($items->isEmpty()) continue;

            $totalItems    = $items->count();
            $disabledItems = $items->where('CanDrop', 0)->count();
            $status = match(true) {
                $disabledItems === $totalItems => 'disabled',
                $disabledItems > 0             => 'partial',
                default                        => 'active',
            };

            $groupCode = $items->first()->GroupCode ?? $assignment->ItemGroupCodeName128;
            $result[] = [
                'category'      => $this->formatGroupName($groupCode),
                'groupCode'     => $groupCode,
                'prob'          => (float) $assignment->DropRatio,
                'groupId'       => $assignment->RefItemGroupID,
                'amountMin'     => $assignment->DropAmountMin,
                'amountMax'     => $assignment->DropAmountMax,
                'items'         => $items,
                'status'        => $status,
                'disabledCount' => $disabledItems,
                'totalCount'    => $totalItems,
            ];
        }

        usort($result, fn($a, $b) => $b['prob'] <=> $a['prob']);

        return $result;
    }

    /** Gold drop info for this monster's level */
    public function getGoldDrop(): ?object
    {
        $monster = $this->getMonster();
        if (!$monster) return null;

        $gold = DB::connection(DatabaseNameEnums::SRO_SHARD->value)
            ->table('_RefDropGold')
            ->where('MonLevel', $monster->Lvl)
            ->first();

        if (!$gold || $gold->DropProb <= 0) return null;
        return $gold;
    }

    /**
     * Drop groups from all _RefDropClassSel_* tables for the monster's level.
     *
     * Two pool systems exist:
     *  - DROP_CLASS_ITEM_GROUP:  ProbGroupN → _RefDropItemGroup.RefItemGroupID  (coins, equipment, COS)
     *  - DROP_CLASS_ITEM_ASSIGN: ProbGroupN → _RefDropItemAssign.AssignedGroup  (potions, elixirs, scrolls, alchemy)
     *
     * Both are deduplicated by their respective group key so the same pool isn't listed twice
     * when multiple category tables reference it at the same monster level.
     */
    public function getDropGroups(): array
    {
        $monster = $this->getMonster();
        if (!$monster) return [];

        $db    = DB::connection(DatabaseNameEnums::SRO_SHARD->value);

        // Collect [ groupId => maxProb ] per pool type
        $itemGroupProbs  = []; // RefItemGroupID => max prob
        $itemAssignProbs = []; // AssignedGroup  => max prob

        $scanTables = static function (array $tables, array &$probs) use ($db, $monster): void {
            foreach (array_keys($tables) as $table) {
                try {
                    $row = $db->table($table)->where('MonLevel', $monster->Lvl)->first();
                } catch (\Exception $e) {
                    continue;
                }
                if (!$row) continue;
                foreach ((array) $row as $col => $prob) {
                    if (!str_starts_with($col, 'ProbGroup') || $prob <= 0) continue;
                    $id = (int) str_replace('ProbGroup', '', $col);
                    if (!isset($probs[$id]) || $prob > $probs[$id]) {
                        $probs[$id] = $prob;
                    }
                }
            }
        };

        $scanTables(self::DROP_CLASS_ITEM_GROUP,  $itemGroupProbs);
        $scanTables(self::DROP_CLASS_ITEM_ASSIGN, $itemAssignProbs);

        $result = [];

        // --- _RefDropItemGroup pools ---
        foreach ($itemGroupProbs as $groupId => $prob) {
            $q = $db->table('_RefDropItemGroup as dg')
                ->join('_RefObjCommon as i', 'i.ID', '=', 'dg.RefItemID')
                ->where('dg.RefItemGroupID', $groupId);

            static::joinTranslation($q, 'itn', 'i.NameStrID128');

            $items = $q->select('dg.CodeName128 as GroupCode', 'i.CodeName128 as ItemCode', 'i.NameStrID128 as NameStrID128', static::engSelect('itn', 'ItemNameENG'), 'dg.SelectRatio', 'i.CanDrop')
                ->orderByDesc('dg.SelectRatio')
                ->get();

            static::resolveNames($items, 'NameStrID128', 'ItemNameENG');

            if ($items->isEmpty()) continue;

            $disabled = $items->where('CanDrop', 0)->count();
            $total    = $items->count();
            $groupCode = $items->first()->GroupCode ?? "Group {$groupId}";

            $result[] = [
                'category'      => $this->formatGroupName($groupCode),
                'groupCode'     => $groupCode,
                'prob'          => $prob,
                'groupId'       => $groupId,
                'items'         => $items,
                'ratioField'    => 'SelectRatio',
                'status'        => $disabled === $total ? 'disabled' : ($disabled > 0 ? 'partial' : 'active'),
                'disabledCount' => $disabled,
                'totalCount'    => $total,
            ];
        }

        // --- _RefDropItemAssign pools ---
        foreach ($itemAssignProbs as $assignGroup => $prob) {
            $q = $db->table('_RefDropItemAssign as a')
                ->join('_RefObjCommon as i', 'i.ID', '=', 'a.RefItemID')
                ->where('a.AssignedGroup', $assignGroup);

            static::joinTranslation($q, 'itn', 'i.NameStrID128');

            $items = $q->select('i.CodeName128 as ItemCode', 'i.NameStrID128 as NameStrID128', static::engSelect('itn', 'ItemNameENG'), 'a.Prob_Relative as SelectRatio', 'a.DropCount', 'i.CanDrop')
                ->orderByDesc('a.Prob_Relative')
                ->get();

            static::resolveNames($items, 'NameStrID128', 'ItemNameENG');

            if ($items->isEmpty()) continue;

            $disabled = $items->where('CanDrop', 0)->count();
            $total    = $items->count();

            $result[] = [
                'category'      => "Tier {$assignGroup} Pool",
                'groupCode'     => "AssignedGroup={$assignGroup}",
                'prob'          => $prob,
                'groupId'       => $assignGroup,
                'items'         => $items,
                'ratioField'    => 'SelectRatio',
                'status'        => $disabled === $total ? 'disabled' : ($disabled > 0 ? 'partial' : 'active'),
                'disabledCount' => $disabled,
                'totalCount'    => $total,
            ];
        }

        usort($result, fn($a, $b) => $b['prob'] <=> $a['prob']);

        return $result;
    }

    private function formatGroupName(string $code): string
    {
        // Convert ITEM_ROCSET_DROPGROUP → "Rocset Dropgroup"
        // Convert ITEM_ETC_DROPGROUP_CURE → "Etc Dropgroup Cure"
        $name = preg_replace('/^ITEM_/', '', $code);
        $name = str_replace('_', ' ', $name);
        return ucwords(strtolower($name));
    }
}
