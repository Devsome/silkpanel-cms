<?php

namespace App\Filament\Resources\WebMarket;

use App\Filament\Resources\WebMarket\Pages\ListMarketTransactions;
use App\Models\MarketTransaction;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MarketTransactionResource extends Resource
{
    protected static ?string $model = MarketTransaction::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static string|\UnitEnum|null $navigationGroup = 'Player Web Market';

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return 'Transactions';
    }

    public static function getModelLabel(): string
    {
        return 'Transaction';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Transactions';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('item_name')->label('Item')->searchable()->limit(30),
                TextColumn::make('opt_level')->label('+'),
                TextColumn::make('seller.silkroad_id')->label('Seller'),
                TextColumn::make('seller_character_name')->label('Seller Char'),
                TextColumn::make('buyer.silkroad_id')->label('Buyer'),
                TextColumn::make('buyer_character_name')->label('Buyer Char'),
                TextColumn::make('price_amount')
                    ->label('Price')
                    ->sortable()
                    ->formatStateUsing(fn($state, $record) => number_format($state) . ' ' . $record->price_type),
                TextColumn::make('fee_amount')->label('Fee')->sortable(),
                TextColumn::make('net_amount')->label('Net')->sortable(),
                TextColumn::make('created_at')->label('Date')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMarketTransactions::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['seller', 'buyer']);
    }
}
