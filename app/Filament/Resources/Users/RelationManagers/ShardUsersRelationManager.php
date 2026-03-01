<?php

namespace App\Filament\Resources\Users\RelationManagers;

use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ShardUsersRelationManager extends RelationManager
{
    protected static string $relationship = 'shardUsers';

    protected static ?string $title = 'Shard Characters';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar')
                    ->circular()
                    ->state(fn($record) => asset('images/silkroad/chars_avatar/' . $record->RefObjID . '.png'))
                    ->extraImgAttributes([
                        'loading' => 'lazy',
                    ])
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('CharID')
                    ->label(__('filament/users.shard.charid'))
                    ->sortable(),
                TextColumn::make('CharName16')
                    ->label(__('filament/users.shard.charname')),
                TextColumn::make('CurLevel')
                    ->label(__('filament/users.shard.level'))
                    ->sortable(),
                TextColumn::make('NickName16')
                    ->label(__('filament/users.shard.job_nickname'))
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->emptyStateIcon('heroicon-o-users')
            ->emptyStateHeading(__('filament/users.shard.empty'))
            ->emptyStateDescription(__('filament/users.shard.empty_description'))
            ->recordActions([
                ViewAction::make()
                    ->label(__('filament/users.shard.view'))
                    ->url(fn($record) => route('filament.admin.resources.characters.view', $record->CharID))
                    ->openUrlInNewTab(),
            ])
            ->headerActions([])
            ->toolbarActions([]);
    }
}
