<?php

namespace App\Filament\Resources\Characters\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CharactersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->where('CharID', '!=', 0)->where('CharName16', '!=', 'dummy'))
            ->columns([
                ImageColumn::make('avatar')
                    ->circular()
                    ->state(fn($record) => asset('images/silkroad/chars_avatar/' . $record->RefObjID . '.png'))
                    ->extraImgAttributes([
                        'loading' => 'lazy',
                    ])
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('CharID')
                    ->label(__('filament/characters.table.char_id'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('CharName16')
                    ->label(__('filament/characters.table.char_name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('CurLevel')
                    ->label(__('filament/characters.table.cur_level'))
                    ->sortable(),
                TextColumn::make('NickName16')
                    ->label(__('filament/characters.table.nick_name'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('LastLogout')
                    ->label(__('filament/characters.table.last_logout'))
                    ->formatStateUsing(fn($state) => $state ? \Carbon\Carbon::parse($state)->diffForHumans() : '-')
                    ->sortable(),

            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                ]),
            ])
            ->toolbarActions([
                //
            ])
            ->defaultSort('CharID', 'desc');
    }
}
