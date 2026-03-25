<?php

namespace App\Filament\Resources\PaymentProviders\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PaymentProviderTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('filament/donations.provider_table_provider'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->label(__('filament/donations.provider_table_slug'))
                    ->badge(),
                IconColumn::make('is_active')
                    ->label(__('filament/donations.provider_table_active'))
                    ->boolean()
                    ->sortable(),
                TextColumn::make('sort_order')
                    ->label(__('filament/donations.provider_table_order'))
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label(__('filament/donations.provider_table_last_updated'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->recordActions([
                EditAction::make(),
            ])
            ->emptyStateIcon('heroicon-o-credit-card')
            ->emptyStateHeading(__('filament/donations.provider_table_empty_heading'))
            ->emptyStateDescription(__('filament/donations.provider_table_empty_description'));
    }
}
