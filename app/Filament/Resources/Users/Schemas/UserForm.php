<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Forms\Components\DateTimePicker;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->label(__('filament/users.form.name'))
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label(__('filament/users.form.email'))
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        DateTimePicker::make('email_verified_at')
                            ->label(__('filament/users.form.email_verified_at')),

                        Select::make('roles')
                            ->label(__('filament/users.form.roles'))
                            ->multiple()
                            ->relationship(name: 'roles', titleAttribute: 'name')
                            ->preload()
                            ->searchable(),

                    ])
                    ->columns(2)
                    ->columnSpan(['lg' => fn(?User $record) => $record === null ? 3 : 2]),
                Section::make()
                    ->schema([
                        TextEntry::make('created_at')
                            ->label(__('filament/users.form.created_at'))
                            ->state(fn(User $record): ?string => $record->created_at?->diffForHumans()),

                        TextEntry::make('updated_at')
                            ->label(__('filament/users.form.last_modified_at'))
                            ->state(fn(User $record): ?string => $record->updated_at?->diffForHumans()),
                    ])
                    ->columnSpan(['lg' => 1])
                    ->hidden(fn(?User $record) => $record === null),
            ])
            ->columns(3);
    }
}
