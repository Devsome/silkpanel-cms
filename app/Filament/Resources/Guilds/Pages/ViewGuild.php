<?php

namespace App\Filament\Resources\Guilds\Pages;

use App\Filament\Resources\Guilds\GuildsResource;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\Characters\CharacterResource;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn as RepeatableTableColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\IconPosition;
use Filament\Schemas\Schema;
use SilkPanel\SilkroadModels\Models\Shard\Guild;

class ViewGuild extends ViewRecord
{
    protected static string $resource = GuildsResource::class;

    private function getGuildChestItems()
    {
        $guildChest = $this->record->guildChest()->get();
        if ($guildChest) {
            return $this->record->getGuildChestItems($this->record->ID);
        }

        return collect();
    }

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }

    public function infolist(Schema $schema): Schema
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
                Group::make([
                    Section::make(__('filament/guilds.section.allied_clans_title'))
                        ->schema([
                            ViewEntry::make('allied_clans')
                                ->view('filament.guilds.allied-clans'),
                        ])
                        ->columnSpan(1),
                    Section::make(__('filament/guilds.section.guild_chest_title'))
                        ->schema([
                            ViewEntry::make('guild_chest')
                                ->view('filament.guilds.guild-chest')
                                ->viewData(fn() => ['items' => $this->getGuildChestItems()]),
                        ])
                        ->compact(true)
                        ->columnSpan(1),
                ]),
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
}
