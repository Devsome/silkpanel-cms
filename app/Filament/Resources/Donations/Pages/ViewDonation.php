<?php

namespace App\Filament\Resources\Donations\Pages;

use App\Filament\Resources\Donations\DonationResource;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewDonation extends ViewRecord
{
    protected static string $resource = DonationResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament/donations.view_section_transaction'))
                    ->schema([
                        TextEntry::make('id')->label(__('filament/donations.view_label_id')),
                        TextEntry::make('user.name')->label(__('filament/donations.view_label_user')),
                        TextEntry::make('donationPackage.name')->label(__('filament/donations.view_label_package')),
                        TextEntry::make('payment_provider_slug')->label(__('filament/donations.view_label_provider'))->badge(),
                        TextEntry::make('transaction_id')->label(__('filament/donations.view_label_transaction_id')),
                        TextEntry::make('status')->label(__('filament/donations.view_label_status'))->badge(),
                    ])->columns(3),

                Section::make(__('filament/donations.view_section_payment'))
                    ->schema([
                        TextEntry::make('amount')->label(__('filament/donations.view_label_amount'))->money(fn($record) => $record->currency),
                        TextEntry::make('currency')->label(__('filament/donations.view_label_currency')),
                        TextEntry::make('silk_amount')->label(__('filament/donations.view_label_silk_amount')),
                        TextEntry::make('silk_type')->label(__('filament/donations.view_label_silk_type')),
                        TextEntry::make('ip_address')->label(__('filament/donations.view_label_ip_address')),
                    ])->columns(3),

                Section::make(__('filament/donations.view_section_timestamps'))
                    ->schema([
                        TextEntry::make('created_at')->label(__('filament/donations.view_label_created'))->dateTime(),
                        TextEntry::make('completed_at')->label(__('filament/donations.view_label_completed'))->dateTime(),
                        TextEntry::make('updated_at')->label(__('filament/donations.view_label_updated'))->dateTime(),
                    ])->columns(3),
            ]);
    }
}
