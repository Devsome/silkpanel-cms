<?php

namespace App\Filament\Resources\Donations;

use App\Filament\Resources\Donations\Pages\ListDonations;
use App\Filament\Resources\Donations\Pages\ViewDonation;
use App\Filament\Resources\Donations\Tables\DonationTable;
use App\Models\Donation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DonationResource extends Resource
{
    protected static ?string $model = Donation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?int $navigationSort = 26;

    public static function getNavigationGroup(): ?string
    {
        return __('filament/donations.navigation_group');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament/donations.navigation_transactions');
    }

    public static function table(Table $table): Table
    {
        return DonationTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDonations::route('/'),
            'view' => ViewDonation::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
