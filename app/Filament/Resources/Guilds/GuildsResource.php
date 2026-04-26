<?php

namespace App\Filament\Resources\Guilds;

use App\Filament\Resources\Guilds\Pages\ManageGuilds;
use App\Filament\Resources\Guilds\Pages\ViewGuild;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use SilkPanel\SilkroadModels\Models\Shard\Guild;

class GuildsResource extends Resource
{
    protected static ?string $model = Guild::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string | \UnitEnum | null $navigationGroup = 'Silkroad';

    protected static ?string $recordTitleAttribute = 'Name';

    protected static ?int $navigationSort = 7;

    public static function getNavigationLabel(): string
    {
        return __('filament/guilds.navigation');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Name')
            ->columns([
                TextColumn::make('Name')
                    ->label(__('filament/guilds.table.name'))
                    ->description(fn(Guild $record) => __('filament/guilds.table.member_master', ['charname' => $record->memberMaster?->CharName ?? '-']))
                    ->searchable(),
                TextColumn::make('Lvl')
                    ->label(__('filament/guilds.table.lvl'))
                    ->sortable(),
                TextColumn::make('MembersCount')
                    ->label(__('filament/guilds.table.members_count'))
                    ->counts('guildMembers'),
                TextColumn::make('FoundationDate')
                    ->label(__('filament/guilds.table.foundation_date'))
                    ->date()
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                ]),
            ])
            ->toolbarActions([
                //
            ])
            ->defaultSort('ID', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageGuilds::route('/'),
            'view' => ViewGuild::route('/{record}'),
        ];
    }
}
