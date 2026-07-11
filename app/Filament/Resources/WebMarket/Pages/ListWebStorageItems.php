<?php

namespace App\Filament\Resources\WebMarket\Pages;

use App\Filament\Resources\WebMarket\WebStorageResource;
use Filament\Resources\Pages\ListRecords;

class ListWebStorageItems extends ListRecords
{
    protected static string $resource = WebStorageResource::class;
}
