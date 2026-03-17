<?php

namespace App\Filament\Resources\Users\Tables;

use App\Enums\UsergroupRoleEnums;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use SilkPanel\SilkroadModels\Models\Account\BlockedUser;

class UserTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('jid')
                    ->label(__('filament/users.table.jid'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('silkroad_id')
                    ->label(__('filament/users.table.silkroad_id'))
                    ->searchable(isIndividual: true)
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('filament/users.table.name'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('email')
                    ->label(__('filament/users.table.email'))
                    ->searchable(),
                TextColumn::make('shard_users_count')
                    ->label(__('filament/users.table.shard_users_count'))
                    ->state(fn($record) => $record->shardUsers->count())
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
                ImageColumn::make('shardUsers.RefObjID')
                    ->state(fn($record) => $record->shardUsers->map(
                        fn($char) => $char->avatar_url
                    )->toArray())
                    ->extraImgAttributes([
                        'loading' => 'lazy',
                    ])
                    ->circular()
                    ->stacked()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('roles.name')
                    ->label(__('filament/users.table.roles'))
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        UsergroupRoleEnums::CUSTOMER->value => 'gray',
                        UsergroupRoleEnums::SUPPORTER->value => 'warning',
                        UsergroupRoleEnums::ADMIN->value => 'primary',
                        default => 'gray',
                    }),
                TextColumn::make('email_verified_at')
                    ->label(__('filament/users.table.email_verified_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('roles')
                    ->label(__('filament/users.table.roles'))
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
                Filter::make('blocked')
                    ->label(__('filament/users.table.filter_blocked'))
                    ->query(function (Builder $query) {
                        $blockedJids = BlockedUser::query()
                            ->where('Type', 1)
                            ->where('timeEnd', '>', now())
                            ->pluck('UserJID');

                        return $query->whereIn('jid', $blockedJids);
                    })
                    ->toggle(),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
