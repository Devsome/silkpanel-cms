<?php

namespace App\Filament\Resources\Guilds;

use App\Filament\Resources\Characters\CharacterResource;
use App\Filament\Resources\Guilds\Pages\ManageGuilds;
use App\Filament\Resources\Guilds\Pages\ViewGuild;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn as RepeatableTableColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\IconPosition;
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

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament/guilds.section.guild_title'))
                    ->description(__('filament/guilds.section.guild_information_description'))
                    ->schema([
                        TextEntry::make('Name')
                            ->label(__('filament/guilds.view.name')),
                        TextEntry::make('Lvl')
                            ->label(__('filament/guilds.view.lvl')),
                        TextEntry::make('MembersCount')
                            ->label(__('filament/guilds.view.members_count')),
                        TextEntry::make('FoundationDate')
                            ->label(__('filament/guilds.view.foundation_date'))
                            ->date(),
                        TextEntry::make('Gold')
                            ->label(__('filament/guilds.view.gold'))
                            ->numeric(),
                        TextEntry::make('MasterCommentTitle')
                            ->label(__('filament/guilds.view.master_comment_title'))
                            ->columnStart(1)
                            ->columnSpanFull(),
                        TextEntry::make('MasterComment')
                            ->label(__('filament/guilds.view.master_comment'))
                            ->columnStart(1)
                            ->columnSpanFull(),
                    ])
                    ->columnSpan(3)
                    ->columns(2),
                Section::make(__('filament/guilds.section.allied_clans_title'))
                    ->schema([
                        ViewEntry::make('allied_clans')
                            ->view('filament.guilds.allied-clans'),
                    ])
                    ->columnSpan(1),
                Section::make(__('filament/guilds.section.members_title'))
                    ->description(__('filament/guilds.section.members_information_description'))
                    ->schema([
                        RepeatableEntry::make('guildMembers')
                            ->hiddenLabel(true)
                            ->state(fn(Guild $record) => $record->guildMembers
                                ->sortBy([
                                    ['JoinDate', 'asc'],
                                ])
                                ->values())
                            ->table([
                                RepeatableTableColumn::make(__('filament/guilds.members.char_name')),
                                RepeatableTableColumn::make(__('filament/guilds.members.nickname')),
                                RepeatableTableColumn::make(__('filament/guilds.members.join_date')),
                            ])
                            ->schema([
                                TextEntry::make('CharName')
                                    ->label(__('filament/guilds.members.char_name'))
                                    ->icon('heroicon-o-arrow-top-right-on-square')
                                    ->iconPosition(IconPosition::Before)
                                    ->extraAttributes(fn($record) => ((int) ($record?->MemberClass ?? -1) === 0)
                                        ? ['class' => 'guild-master-row-marker']
                                        : ['class' => 'guild-hover-row-marker'])
                                    ->url(fn($record) => $record?->CharID
                                        ? CharacterResource::getUrl('view', ['record' => $record->CharID])
                                        : null)
                                    ->openUrlInNewTab(true)
                                    ->default('-'),
                                TextEntry::make('Nickname')
                                    ->label(__('filament/guilds.members.nickname'))
                                    ->default('-'),
                                TextEntry::make('JoinDate')
                                    ->label(__('filament/guilds.members.join_date'))
                                    ->dateTime(),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ])
            ->columns(4);
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
