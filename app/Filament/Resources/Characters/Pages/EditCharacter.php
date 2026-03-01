<?php

namespace App\Filament\Resources\Characters\Pages;

use App\Filament\Resources\Characters\CharacterResource;
use Filament\Resources\Pages\EditRecord;

class EditCharacter extends EditRecord
{
    protected static string $resource = CharacterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
