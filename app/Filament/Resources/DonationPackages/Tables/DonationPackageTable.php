<?php

namespace App\Filament\Resources\DonationPackages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DonationPackageTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('filament/donations.package_table_name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('silk_amount')
                    ->label(__('filament/donations.package_table_silk'))
                    ->sortable()
                    ->formatStateUsing(fn($record) => number_format($record->silk_amount) . ' (' . $record->silk_type . ')'),
                TextColumn::make('price')
                    ->label(__('filament/donations.package_table_price'))
                    ->money(fn($record) => $record->currency)
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label(__('filament/donations.package_table_active'))
                    ->boolean()
                    ->sortable(),
                TextColumn::make('sort_order')
                    ->label(__('filament/donations.package_table_order'))
                    ->sortable(),
                ImageColumn::make('image')
                    ->label(__('filament/donations.package_table_image'))
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-gift')
            ->emptyStateHeading(__('filament/donations.package_table_empty_heading'))
            ->emptyStateDescription(__('filament/donations.package_table_empty_description'));
    }
}
