<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Password;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('sendPasswordReset')
                ->label(__('filament/users.edit.reset_password'))
                ->icon('heroicon-o-lock-open')
                ->requiresConfirmation()
                ->modalHeading(__('filament/users.edit.modal_heading'))
                ->modalDescription(__('filament/users.edit.modal_description'))
                ->disabled(fn(): bool => empty($this->record?->email))
                ->action(function (): void {
                    $user = $this->record;
                    $status = Password::sendResetLink(['email' => $user->email]);

                    if ($status === Password::RESET_LINK_SENT) {
                        Notification::make()
                            ->title(__('filament/users.edit.success_message'))
                            ->success()
                            ->send();
                        return;
                    }

                    Notification::make()
                        ->title(__('filament/users.edit.error_message'))
                        ->danger()
                        ->send();
                }),
        ];
    }
}
