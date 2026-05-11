<?php

namespace App\Filament\Resources\Webmall\Pages;

use App\Filament\Resources\Webmall\WebmallResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditWebmallCategory extends EditRecord
{
    protected static string $resource = WebmallResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
