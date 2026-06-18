<?php

namespace App\Filament\Pages;

use App\Helpers\WebmallItemIconHelper;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Pagination\LengthAwarePaginator;
use SilkPanel\SilkroadModels\Models\Account\AbstractItemNameDesc;
use SilkPanel\SilkroadModels\Models\Shard\RefObjCommon;

class Items extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBox;

    protected static string|\UnitEnum|null $navigationGroup = 'Silkroad';

    protected static ?int $navigationSort = 12;

    protected string $view = 'filament.pages.items';

    public string $search = '';
    public string $typeFilter = '';
    public int $perPage = 50;
    public int $currentPage = 1;

    public static function getNavigationLabel(): string
    {
        return __('filament/items.navigation');
    }

    public function getTitle(): string
    {
        return __('filament/items.title');
    }

    public function updatedSearch(): void
    {
        $this->currentPage = 1;
    }

    public function updatedTypeFilter(): void
    {
        $this->currentPage = 1;
    }

    public function getItems(): LengthAwarePaginator
    {
        $query = RefObjCommon::where('TypeID1', 3)
            ->where('Service', 1)
            ->select([
                'ID', 'CodeName128', 'NameStrID128',
                'TypeID2', 'TypeID3', 'TypeID4', 'Rarity',
                'ReqLevel1', 'Price', 'CashItem',
                'AssocFileIcon128',
            ]);

        if ($this->typeFilter !== '') {
            [$t2, $t3] = explode(':', $this->typeFilter . ':');
            $query->where('TypeID2', (int) $t2);
            if ($t3 !== '') {
                $query->where('TypeID3', (int) $t3);
            }
        }

        if (strlen($this->search) >= 2) {
            $search = $this->search;
            $query->where('CodeName128', 'like', "%{$search}%");
        }

        $query->orderBy('TypeID2')->orderBy('TypeID3')->orderBy('ReqLevel1')->orderBy('ID');

        $total = $query->count();
        $items = $query
            ->with('getRefObjItem:ID,ItemClass')
            ->offset(($this->currentPage - 1) * $this->perPage)
            ->limit($this->perPage)
            ->get();

        // Resolve English names in bulk
        $nameStrIds = $items->pluck('NameStrID128')->filter()->unique()->values()->all();
        $names = resolve(AbstractItemNameDesc::class)->getItemNames($nameStrIds);

        $items->each(function ($item) use ($names) {
            $item->NameENG = $names[$item->NameStrID128] ?? null;
        });

        return new LengthAwarePaginator(
            $items,
            $total,
            $this->perPage,
            $this->currentPage,
            ['path' => request()->url()]
        );
    }

    public function getStats(): array
    {
        return [
            'total'       => RefObjCommon::where('TypeID1', 3)->where('Service', 1)->count(),
            'weapons'     => RefObjCommon::where('TypeID1', 3)->where('Service', 1)->where('TypeID2', 1)->where('TypeID3', 6)->count(),
            'armors'      => RefObjCommon::where('TypeID1', 3)->where('Service', 1)->where('TypeID2', 1)->whereIn('TypeID3', [1, 2, 3, 4, 9, 10, 11])->count(),
            'accessories' => RefObjCommon::where('TypeID1', 3)->where('Service', 1)->where('TypeID2', 1)->whereIn('TypeID3', [5, 12])->count(),
        ];
    }

    public static function iconUrl(string $assocFile): string
    {
        $icon = WebmallItemIconHelper::resolveIcon($assocFile);
        return asset('images/silkroad/' . $icon);
    }

    /**
     * Resolves the display name using config('item.types')[TypeID1][TypeID2][TypeID3][TypeID4].
     * Falls back through levels if a specific TypeID4 name isn't available.
     */
    public static function typeName(int $typeId2, int $typeId3 = 0, int $typeId4 = 0): string
    {
        $map = config('item.types.3', []);

        $t3map = $map[$typeId2][$typeId3] ?? null;
        if ($t3map === null) {
            return 'Other';
        }

        // TypeID4-specific name (e.g. Sword, Blade, Earring, Necklace)
        if ($typeId4 > 0 && isset($t3map[$typeId4])) {
            return $t3map[$typeId4];
        }

        // Fall back to first value in the TypeID3 group as the category label
        return reset($t3map) ?: 'Other';
    }

    public static function typeColor(int $typeId2, int $typeId3 = 0): string
    {
        if ($typeId2 === 1) {
            return match($typeId3) {
                6       => 'danger',   // Weapon
                4       => 'warning',  // Shield
                5, 12   => 'warning',  // Jewel
                13, 14  => 'success',  // Avatar
                default => 'primary',  // Armor
            };
        }

        return match($typeId2) {
            2 => 'success',
            3 => 'info',
            4 => 'gray',
            5 => 'warning',
            default => 'gray',
        };
    }

    public static function rarityLabel(int $rarity): string
    {
        return match($rarity) {
            1 => 'Rare',
            2 => 'Rare+',
            3 => 'Unique',
            default => '',
        };
    }

    public static function rarityColor(int $rarity): string
    {
        return match($rarity) {
            1 => 'info',
            2 => 'warning',
            3 => 'danger',
            default => 'gray',
        };
    }

    /**
     * Returns the Seal/Sox label based on CodeName128 and ItemClass.
     * sox_type config keys are thresholds: ItemClass >= threshold → use that group.
     */
    public static function soxLabel(string $codeName, int $itemClass): string
    {
        $soxTypes = config('item.sox_type', []);

        // Check highest threshold first (Nova = 30)
        krsort($soxTypes);

        foreach ($soxTypes as $threshold => $keywords) {
            if ($itemClass >= $threshold) {
                foreach ($keywords as $keyword => $label) {
                    if (str_contains($codeName, (string) $keyword)) {
                        return $label;
                    }
                }
            }
        }

        return '';
    }

    public static function soxColor(string $sealLabel): string
    {
        return match($sealLabel) {
            'Seal of Nova' => 'warning',
            'Seal of Star' => 'info',
            'Seal of Moon' => 'gray',
            'Seal of Sun'  => 'danger',
            default        => 'gray',
        };
    }

    public static function getDetailUrl(int $id): string
    {
        return ItemDetail::getUrl(['id' => $id]);
    }

    public function previousPage(): void
    {
        if ($this->currentPage > 1) {
            $this->currentPage--;
        }
    }

    public function nextPage(int $lastPage): void
    {
        if ($this->currentPage < $lastPage) {
            $this->currentPage++;
        }
    }

    public function goToPage(int $page): void
    {
        $this->currentPage = $page;
    }
}
