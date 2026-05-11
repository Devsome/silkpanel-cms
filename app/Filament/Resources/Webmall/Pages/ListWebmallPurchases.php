<?php

namespace App\Filament\Resources\Webmall\Pages;

use App\Filament\Resources\Webmall\WebmallPurchasesResource;
use Filament\Resources\Pages\ListRecords;

class ListWebmallPurchases extends ListRecords
{
    protected static string $resource = WebmallPurchasesResource::class;
}
