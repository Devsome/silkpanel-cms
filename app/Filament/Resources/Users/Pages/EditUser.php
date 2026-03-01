<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Password;
use SilkPanel\SilkroadModels\Models\Account\Shard;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->getSaveFormAction()
                ->formId('form'),
            Action::make('sendPasswordReset')
                ->label(__('filament/users.edit.reset_password'))
                ->color('gray')
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
            Action::make('block_user')
                ->label(__('filament/users.edit.block_user'))
                ->schema([
                    TextInput::make('reason')
                        ->label(__('filament/users.edit.block_reason'))
                        ->required()
                        ->maxLength(255),
                    TextInput::make('description')
                        ->label(__('filament/users.edit.block_description'))
                        ->helperText(__('filament/users.edit.block_description_helper'))
                        ->required()
                        ->maxLength(255),
                    Select::make('shard')
                        ->label(__('filament/users.edit.block_shard'))
                        ->options(Shard::query()->pluck('szName', 'nID')),
                    DateTimePicker::make('duration')
                        ->label(__('filament/users.edit.block_duration'))
                        ->helperText(__('filament/users.edit.block_duration_helper'))
                        ->required(),
                ])
                ->color('danger')
                ->icon('heroicon-o-shield-exclamation')
                ->requiresConfirmation()
                ->modalHeading(__('filament/users.edit.block_modal_heading'))
                ->modalDescription(__('filament/users.edit.block_modal_description'))
                ->visible(fn($record) => !$record->tbuser->activeBlock)
                ->action(function ($record, $data) {
                    $reason = $data['reason'];
                    $description = $data['description'];
                    $duration = $data['duration'];
                    $shard = $data['shard'] ?? null;
                    $jid = $record->jid;
                    $record->tbuser?->blockUser(jid: $jid, reason: $reason, description: $description, timeEnd: $duration, shard: $shard);
                    Notification::make()
                        ->title(__('filament/users.edit.block_success_message'))
                        ->success()
                        ->send();
                }),
        ];
    }

    protected function getFormActions(): array
    {
        return [];
    }
}
