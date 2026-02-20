<?php

namespace App\Filament\Resources\Users\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ShardUsersRelationManager extends RelationManager
{
    protected static string $relationship = 'shardUsers';

    protected static ?string $title = 'Shard Users';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar')
                    ->imageHeight(30)
                    ->circular()
                    ->state(fn($record) => asset('images/silkroad/chars/' . $record->RefObjID . '.gif'))
                    ->extraImgAttributes([
                        'loading' => 'lazy',
                    ])
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('CharID')
                    ->label(__('filament/users.shard.charid'))
                    ->sortable(),
                TextColumn::make('CharName16')
                    ->label(__('filament/users.shard.charname'))
                    ->searchable(),
                TextColumn::make('CurLevel')
                    ->label(__('filament/users.shard.level'))
                    ->sortable(),
                TextColumn::make('NickName16')
                    ->label(__('filament/users.shard.job_nickname'))
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([])
            ->headerActions([])
            ->toolbarActions([]);
    }
}
