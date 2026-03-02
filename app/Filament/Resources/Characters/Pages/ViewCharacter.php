<?php

namespace App\Filament\Resources\Characters\Pages;

use App\Filament\Resources\Characters\CharacterResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Filament\Support\Enums\IconSize;
use Filament\Support\Icons\Heroicon;
use SilkPanel\SilkroadModels\Helper\SilkroadLocationsHelper;

class ViewCharacter extends ViewRecord
{
    protected static string $resource = CharacterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('users')
                ->label(__('filament/characters.view.users'))
                ->url(fn($record) => route('filament.admin.resources.users.edit', $record->getAccountUser->getWebAccountUser->id))
                ->color('gray'),
            EditAction::make(),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('filament/characters.section.character_title'))
                            ->description(__('filament/characters.section.character_information_description'))
                            ->schema([
                                TextEntry::make('CharID')
                                    ->label(__('filament/characters.view.charid')),
                                TextEntry::make('CharName16')
                                    ->label(__('filament/characters.view.charname')),
                                TextEntry::make('CurLevel')
                                    ->label(__('filament/characters.view.curlevel')),
                                TextEntry::make('Strength')
                                    ->label(__('filament/characters.view.strength')),
                                TextEntry::make('Intellect')
                                    ->label(__('filament/characters.view.intellect')),
                                TextEntry::make('current_percentage')
                                    ->state(function ($record) {
                                        return $record->getCharRefLevelExperience() . '%';
                                    })
                                    ->label(__('filament/characters.view.experience')),
                                TextEntry::make('RemainGold')
                                    ->label(__('filament/characters.view.remaingold')),
                                TextEntry::make('RemainSkillPoint')
                                    ->label(__('filament/characters.view.remainskillpoint')),
                                TextEntry::make('GuildID')
                                    ->label(__('filament/characters.view.guildid')),
                                TextEntry::make('HP')
                                    ->label(__('filament/characters.view.hp')),
                                TextEntry::make('MP')
                                    ->label(__('filament/characters.view.mp')),
                                IconEntry::make('Deleted')
                                    ->label(__('filament/characters.view.deleted'))
                                    ->true('heroicon-o-trash', 'success')
                                    ->false('heroicon-o-trash', 'gray')
                                    ->size(IconSize::Medium),
                            ])->columns(3),
                    ])->columnSpan(['lg' => 3]),
                Group::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                IconEntry::make('isOnline')
                                    ->label(__('filament/characters.view.online'))
                                    ->true('heroicon-m-check-circle', 'success')
                                    ->false('heroicon-m-x-circle', 'danger')
                                    ->extraAttributes([
                                        'class' => 'animate-pulse',
                                    ])
                                    ->size(IconSize::Medium),
                                TextEntry::make('LastLogout')
                                    ->label(__('filament/characters.view.lastlogout')),
                            ]),
                        Section::make(__('filament/characters.section.position_title'))
                            ->description(__('filament/characters.section.position_information_description'))
                            ->schema([
                                TextEntry::make('PosX')
                                    ->label(__('filament/characters.view.pos_x')),
                                TextEntry::make('PosY')
                                    ->label(__('filament/characters.view.pos_y')),
                                TextEntry::make('PosZ')
                                    ->label(__('filament/characters.view.pos_z')),
                                TextEntry::make('LatestRegion')
                                    ->label(__('filament/characters.view.latest_region')),
                            ])->footerActions([
                                Action::make('unstuck')
                                    ->label(__('filament/characters.view.unstuck'))
                                    ->schema([
                                        Select::make('unstuck_position')
                                            ->label(__('filament/characters.view.unstuck_position'))
                                            ->helperText(__('filament/characters.view.unstuck_position_helper'))
                                            ->options([
                                                'jangan' => 'Jangan',
                                                'donwhang' => 'Donwhang',
                                                'hotan' => 'Hotan',
                                                'samarkand' => 'Samarkand',
                                                'constantinople' => 'Constantinople',
                                            ])
                                            ->required()
                                    ])
                                    ->color('gray')
                                    ->requiresConfirmation()
                                    ->modalHeading(__('filament/characters.view.unstuck_modal_heading'))
                                    ->modalDescription(__('filament/characters.view.unstuck_modal_description'))
                                    ->visible(fn($record) => !$record->isOnline && !$record->hasJobSuit)
                                    ->action(function ($record, $data) {
                                        $position = $data['unstuck_position'] ?? 'default';
                                        if ($record->IsOnline) {
                                            Notification::make()
                                                ->title(__('filament/characters.view.unstuck_error_title'))
                                                ->body(__('filament/characters.view.unstuck_error_online_message'))
                                                ->danger()
                                                ->send();
                                            return;
                                        }
                                        if ($record->HasJobSuit) {
                                            Notification::make()
                                                ->title(__('filament/characters.view.unstuck_error_title'))
                                                ->body(__('filament/characters.view.unstuck_error_job_suit_message'))
                                                ->danger()
                                                ->send();
                                            return;
                                        }
                                        $record->unstuckCharacter($position);
                                        Notification::make()
                                            ->title(__('filament/characters.view.unstuck_success_title'))
                                            ->body(__('filament/characters.view.unstuck_success_message'))
                                            ->success()
                                            ->send();
                                    }),
                            ])
                            ->columns(2),

                        Section::make(__('filament/characters.section.job_title'))
                            ->description(__('filament/characters.section.job_information_description'))
                            ->schema([
                                TextEntry::make('NickName16')
                                    ->label(__('filament/characters.view.nickname')),
                                TextEntry::make('JobPenaltyTime')
                                    ->label(__('filament/characters.view.jobpenaltytime')),
                                ViewEntry::make('job-information')
                                    ->view('filament.characters.partials.job-information')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                    ])->columnSpan(['lg' => 2]),
            ])->columns(5);
    }
}
