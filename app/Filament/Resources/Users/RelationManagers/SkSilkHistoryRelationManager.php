<?php

namespace App\Filament\Resources\Users\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SkSilkHistoryRelationManager extends RelationManager
{
    protected static string $relationship = 'getSkSilkHistory';

    protected static ?string $title = 'Silk History';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('Silk_Offset')
                    ->label(__('filament/silk.table.silk_offset')),
                TextColumn::make('BuyQuantity')
                    ->label(__('filament/silk.table.buy_quantity')),
                TextColumn::make('Silk_Remain')
                    ->label(__('filament/silk.table.silk_remain')),
                TextColumn::make('AuthDate')
                    ->label(__('filament/silk.table.auth_date'))
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label(__('filament/silk.table.sub_jid'))
                    ->description(fn($record): string => $record->user->email)
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('SlipPaper')
                    ->label(__('filament/silk.table.slip_paper'))
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('IP')
                    ->label(__('filament/silk.table.ip'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->emptyStateIcon('heroicon-o-banknotes')
            ->emptyStateHeading(__('filament/silk.empty'))
            ->emptyStateDescription(__('filament/silk.empty_description'))
            ->headerActions([])
            ->toolbarActions([])
            ->defaultSort('AuthDate', 'desc');
    }
}
