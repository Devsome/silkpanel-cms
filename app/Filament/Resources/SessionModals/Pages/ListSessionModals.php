<?php

namespace App\Filament\Resources\SessionModals\Pages;

use App\Filament\Resources\SessionModals\SessionModalResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSessionModals extends ListRecords
{
    protected static string $resource = SessionModalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
