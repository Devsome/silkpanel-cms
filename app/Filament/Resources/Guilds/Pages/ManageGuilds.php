<?php

namespace App\Filament\Resources\Guilds\Pages;

use App\Filament\Resources\Guilds\GuildsResource;
use Filament\Resources\Pages\ManageRecords;

class ManageGuilds extends ManageRecords
{
    protected static string $resource = GuildsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
