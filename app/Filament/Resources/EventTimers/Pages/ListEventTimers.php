<?php

namespace App\Filament\Resources\EventTimers\Pages;

use App\Filament\Resources\EventTimers\EventTimerResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEventTimers extends ListRecords
{
    protected static string $resource = EventTimerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
