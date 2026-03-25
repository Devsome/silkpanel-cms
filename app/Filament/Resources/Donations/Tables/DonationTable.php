<?php

namespace App\Filament\Resources\Donations\Tables;

use App\Enums\DonationStatusEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DonationTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(__('filament/donations.donation_table_id'))
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label(__('filament/donations.donation_table_user'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('donationPackage.name')
                    ->label(__('filament/donations.donation_table_package'))
                    ->sortable(),
                TextColumn::make('payment_provider_slug')
                    ->label(__('filament/donations.donation_table_provider'))
                    ->badge(),
                TextColumn::make('amount')
                    ->label(__('filament/donations.donation_table_amount'))
                    ->money(fn($record) => $record->currency)
                    ->sortable(),
                TextColumn::make('silk_amount')
                    ->label(__('filament/donations.donation_table_silk'))
                    ->formatStateUsing(fn($record) => number_format($record->silk_amount)),
                TextColumn::make('status')
                    ->label(__('filament/donations.donation_table_status'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('transaction_id')
                    ->label(__('filament/donations.donation_table_transaction_id'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                TextColumn::make('ip_address')
                    ->label(__('filament/donations.donation_table_ip'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('filament/donations.donation_table_date'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(DonationStatusEnum::class),
                SelectFilter::make('payment_provider_slug')
                    ->label(__('filament/donations.donation_filter_provider'))
                    ->options([
                        'paypal' => 'PayPal',
                        'stripe' => 'Stripe',
                        'hipopay' => 'HipoPay',
                        'hipocard' => 'HipoCard',
                    ]),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-banknotes')
            ->emptyStateHeading(__('filament/donations.donation_table_empty_heading'))
            ->emptyStateDescription(__('filament/donations.donation_table_empty_description'));
    }
}
