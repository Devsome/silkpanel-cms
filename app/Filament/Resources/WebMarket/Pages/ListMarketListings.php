<?php

namespace App\Filament\Resources\WebMarket\Pages;

use App\Filament\Resources\WebMarket\MarketListingResource;
use App\Jobs\ProcessExpiredMarketListingsJob;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListMarketListings extends ListRecords
{
    protected static string $resource = MarketListingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('process_expired')
                ->label('Process Expired Listings')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->action(fn() => dispatch(new ProcessExpiredMarketListingsJob())),
        ];
    }
}
