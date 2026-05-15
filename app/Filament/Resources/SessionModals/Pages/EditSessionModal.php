<?php

namespace App\Filament\Resources\SessionModals\Pages;

use App\Filament\Resources\SessionModals\SessionModalResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditSessionModal extends EditRecord
{
    protected static string $resource = SessionModalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('preview')
                ->label('Preview')
                ->icon('heroicon-o-eye')
                ->color('gray')
                ->url(fn() => route('admin.session-modals.preview', $this->record))
                ->openUrlInNewTab(),
        ];
    }
}
