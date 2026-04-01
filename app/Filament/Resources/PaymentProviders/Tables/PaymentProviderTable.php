<?php

namespace App\Filament\Resources\PaymentProviders\Tables;

use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Columns\ToggleColumn;
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
                ToggleColumn::make('is_active')
                    ->label(__('filament/donations.provider_table_active'))
                    ->onIcon(Heroicon::OutlinedCheck)
                    ->offIcon(Heroicon::OutlinedXMark)
                    ->sortable(),
                TextInputColumn::make('sort_order')
                    ->label(__('filament/donations.provider_table_order'))
                    ->rules(['required', 'numeric', 'min:0'])
                    ->sortable()
                    ->width(10),
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
