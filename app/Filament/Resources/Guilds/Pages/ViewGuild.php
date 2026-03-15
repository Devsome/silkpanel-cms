<?php

namespace App\Filament\Resources\Guilds\Pages;

use App\Filament\Resources\Guilds\GuildsResource;
use Filament\Resources\Pages\ViewRecord;

class ViewGuild extends ViewRecord
{
    protected static string $resource = GuildsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
