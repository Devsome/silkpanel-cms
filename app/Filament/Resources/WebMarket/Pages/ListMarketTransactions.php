<?php

namespace App\Filament\Resources\WebMarket\Pages;

use App\Filament\Resources\WebMarket\MarketTransactionResource;
use Filament\Resources\Pages\ListRecords;

class ListMarketTransactions extends ListRecords
{
    protected static string $resource = MarketTransactionResource::class;
}
