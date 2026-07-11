<?php

namespace App\Filament\Resources\WebMarket;

use App\Filament\Resources\WebMarket\Pages\ListWebStorageItems;
use App\Models\WebStorage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class WebStorageResource extends Resource
{
    protected static ?string $model = WebStorage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBox;

    protected static string|\UnitEnum|null $navigationGroup = 'Player Web Market';

    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return 'Web Storage';
    }

    public static function getModelLabel(): string
    {
        return 'Web Storage Item';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Web Storage';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('item_name')->label('Item')->searchable()->limit(30),
                TextColumn::make('opt_level')->label('+'),
                TextColumn::make('quantity')->label('Qty'),
                TextColumn::make('character_name')->label('Character')->searchable(),
                TextColumn::make('user.silkroad_id')->label('Account'),
                TextColumn::make('source_type')->label('Source')->badge(),
                TextColumn::make('item_id64')->label('ID64'),
                TextColumn::make('ref_item_id')->label('Ref ID'),
                TextColumn::make('created_at')->label('Created')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('source_type')
                    ->options([
                        'inventory' => 'Inventory',
                        'storage' => 'Storage',
                    ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWebStorageItems::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('user');
    }
}
