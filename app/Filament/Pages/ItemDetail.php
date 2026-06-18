<?php

namespace App\Filament\Pages;

use App\Helpers\WebmallItemIconHelper;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use SilkPanel\SilkroadModels\Models\Account\AbstractItemNameDesc;
use SilkPanel\SilkroadModels\Models\Shard\RefObjCommon;

class ItemDetail extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBox;

    protected static string|\UnitEnum|null $navigationGroup = 'Silkroad';

    protected string $view = 'filament.pages.item-detail';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'items/{id}';

    public int $id = 0;

    public function mount(int $id): void
    {
        $this->id = $id;
    }

    public static function getNavigationLabel(): string
    {
        return 'Item Detail';
    }

    public function getTitle(): string
    {
        $item = $this->getItem();
        if ($item) {
            $name = ($item->NameENG && $item->NameENG !== '0') ? $item->NameENG : $item->CodeName128;
            return $name;
        }
        return 'Item Detail';
    }

    public function getItem(): ?object
    {
        $refObj = RefObjCommon::with('getRefObjItem')
            ->where('TypeID1', 3)
            ->find($this->id);

        if (!$refObj) {
            return null;
        }

        $names = resolve(AbstractItemNameDesc::class)->getItemNames([$refObj->NameStrID128]);
        $refObj->NameENG = $names[$refObj->NameStrID128] ?? null;

        return $refObj;
    }

    public static function iconUrl(string $assocFile): string
    {
        $icon = WebmallItemIconHelper::resolveIcon($assocFile);
        return asset('images/silkroad/' . $icon);
    }

    public static function typeName(int $typeId2, int $typeId3 = 0, int $typeId4 = 0): string
    {
        return Items::typeName($typeId2, $typeId3, $typeId4);
    }

    public static function typeColor(int $typeId2, int $typeId3 = 0): string
    {
        return Items::typeColor($typeId2, $typeId3);
    }

    public static function soxLabel(string $codeName, int $itemClass): string
    {
        return Items::soxLabel($codeName, $itemClass);
    }

    public static function soxColor(string $sealLabel): string
    {
        return Items::soxColor($sealLabel);
    }

    public static function rarityLabel(int $rarity): string
    {
        return Items::rarityLabel($rarity);
    }

    public static function rarityColor(int $rarity): string
    {
        return Items::rarityColor($rarity);
    }

    public static function genderLabel(int $gender): string
    {
        return match($gender) {
            0 => 'Female only',
            1 => 'Male only',
            default => 'Any',
        };
    }

    public static function countryLabel(int $country): string
    {
        return match($country) {
            1 => 'Chinese',
            2 => 'European',
            3 => 'Both',
            default => 'Unknown',
        };
    }
}
