<?php

namespace App\Filament\Resources\PaymentProviders;

use App\Filament\Resources\PaymentProviders\Pages\ListPaymentProviders;
use App\Filament\Resources\PaymentProviders\Pages\EditPaymentProvider;
use App\Filament\Resources\PaymentProviders\Schemas\PaymentProviderForm;
use App\Filament\Resources\PaymentProviders\Tables\PaymentProviderTable;
use App\Models\PaymentProvider;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PaymentProviderResource extends Resource
{
    protected static ?string $model = PaymentProvider::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 25;

    public static function getNavigationGroup(): ?string
    {
        return __('filament/donations.navigation_group');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament/donations.navigation_payment_providers');
    }

    public static function form(Schema $schema): Schema
    {
        return PaymentProviderForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PaymentProviderTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPaymentProviders::route('/'),
            'edit' => EditPaymentProvider::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
