<?php

namespace App\Filament\Resources\DonationPackages;

use App\Filament\Resources\DonationPackages\Pages\ListDonationPackages;
use App\Filament\Resources\DonationPackages\Pages\EditDonationPackage;
use App\Filament\Resources\DonationPackages\Pages\CreateDonationPackage;
use App\Filament\Resources\DonationPackages\Schemas\DonationPackageForm;
use App\Filament\Resources\DonationPackages\Tables\DonationPackageTable;
use App\Models\DonationPackage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DonationPackageResource extends Resource
{
    protected static ?string $model = DonationPackage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGift;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 27;

    public static function getNavigationGroup(): ?string
    {
        return __('filament/donations.navigation_group');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament/donations.navigation_packages');
    }

    public static function form(Schema $schema): Schema
    {
        return DonationPackageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DonationPackageTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDonationPackages::route('/'),
            'create' => CreateDonationPackage::route('/create'),
            'edit' => EditDonationPackage::route('/{record}/edit'),
        ];
    }
}
