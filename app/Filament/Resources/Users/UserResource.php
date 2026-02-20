<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ManageUsers;
use App\Filament\Resources\Users\RelationManagers\ShardUsersRelationManager;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Models\User;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static string | \UnitEnum | null $navigationGroup = 'Administration';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationLabel(): string
    {
        return __('filament/users.navigation');
    }

    public function getTitle(): string | Htmlable
    {
        return __('filament/users.title');
    }

    public function getSubheading(): string|Htmlable|null
    {
        return __('filament/users.subheading');
    }

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('jid')
                    ->label(__('filament/users.table.jid'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('silkroad_id')
                    ->label(__('filament/users.table.silkroad_id'))
                    ->searchable()
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
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                ImageColumn::make('shardUsers.RefObjID')
                    ->imageHeight(30)
                    ->state(fn($record) => $record->shardUsers->map(
                        fn($char) => asset('images/silkroad/chars/' . $char->RefObjID . '.gif')
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
                    ->searchable(),
                TextColumn::make('email_verified_at')
                    ->label(__('filament/users.table.email_verified_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                ]),
            ])
            ->toolbarActions([
                //
            ])
            ->defaultSort('jid', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageUsers::route('/'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            ShardUsersRelationManager::class,
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['jid', 'name', 'email'];
    }
}
