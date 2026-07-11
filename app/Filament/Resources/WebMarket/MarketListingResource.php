<?php

namespace App\Filament\Resources\WebMarket;

use App\Enums\MarketListingStatusEnum;
use App\Filament\Resources\WebMarket\Pages\ListMarketListings;
use App\Filament\Resources\WebMarket\Pages\ViewMarketListing;
use App\Models\MarketListing;
use App\Services\MarketListingService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MarketListingResource extends Resource
{
    protected static ?string $model = MarketListing::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static string|\UnitEnum|null $navigationGroup = 'Player Web Market';

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return 'Listings';
    }

    public static function getModelLabel(): string
    {
        return 'Listing';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Listings';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('item_name')->label('Item')->searchable()->limit(30),
                TextColumn::make('opt_level')->label('+')->sortable(),
                TextColumn::make('character_name')->label('Seller Char')->searchable(),
                TextColumn::make('user.silkroad_id')->label('Account'),
                TextColumn::make('price_amount')
                    ->label('Price')
                    ->sortable()
                    ->formatStateUsing(fn($state, $record) => number_format($state) . ' ' . $record->priceTypeLabel()),
                TextColumn::make('fee_amount')->label('Fee')->sortable()->numeric(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
                TextColumn::make('expires_at')->label('Expires')->dateTime()->sortable(),
                TextColumn::make('created_at')->label('Created')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(MarketListingStatusEnum::class),
            ])
            ->actions([
                Action::make('force_expire')
                    ->label('Force Expire')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn($record) => $record->status === MarketListingStatusEnum::ACTIVE)
                    ->action(function ($record) {
                        $record->update([
                            'status' => MarketListingStatusEnum::EXPIRED,
                            'expires_at' => now(),
                        ]);
                        app(MarketListingService::class)->processExpiredListings();
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMarketListings::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('user');
    }
}
