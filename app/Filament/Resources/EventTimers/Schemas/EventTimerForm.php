<?php

namespace App\Filament\Resources\EventTimers\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EventTimerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament/event-timers.section.details'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('filament/event-timers.form.name'))
                            ->required()
                            ->maxLength(100),

                        Select::make('type')
                            ->label(__('filament/event-timers.form.type'))
                            ->options([
                                'hourly' => __('filament/event-timers.type.hourly'),
                                'weekly' => __('filament/event-timers.type.weekly'),
                                'static' => __('filament/event-timers.type.static'),
                            ])
                            ->default('hourly')
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                if ($state === 'hourly') {
                                    $set('hour', null);
                                    $set('days', null);
                                    $set('time', null);
                                }
                                if ($state === 'weekly') {
                                    $set('hours', null);
                                    $set('time', null);
                                }
                                if ($state === 'static') {
                                    $set('hours', null);
                                    $set('hour', null);
                                    $set('days', null);
                                    $set('min', 0);
                                }
                            }),

                        Select::make('icon')
                            ->label(__('filament/event-timers.form.icon'))
                            ->options(self::getIconOptions())
                            ->searchable(),

                        FileUpload::make('image')
                            ->label(__('filament/event-timers.form.image'))
                            ->helperText(__('filament/event-timers.form.image_helper'))
                            ->image()
                            ->maxSize(2048)
                            ->directory('event-timers')
                            ->visibility('public'),

                        TextInput::make('sort_order')
                            ->label(__('filament/event-timers.form.sort_order'))
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                    ])->columns(2),

                Section::make(__('filament/event-timers.section.hourly'))
                    ->description(__('filament/event-timers.section.hourly_description'))
                    ->icon('heroicon-o-arrow-path')
                    ->schema([
                        CheckboxList::make('hours')
                            ->label(__('filament/event-timers.form.hours'))
                            ->options(
                                collect(range(0, 23))->mapWithKeys(fn($h) => [
                                    $h => str_pad($h, 2, '0', STR_PAD_LEFT) . ':00',
                                ])->toArray()
                            )
                            ->columns(6)
                            ->required()
                            ->columnSpanFull(),

                        TextInput::make('min')
                            ->label(__('filament/event-timers.form.min'))
                            ->helperText(__('filament/event-timers.form.min_helper'))
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(59)
                            ->default(0)
                            ->required(),
                    ])
                    ->visible(fn($get) => $get('type') === 'hourly'),

                Section::make(__('filament/event-timers.section.weekly'))
                    ->description(__('filament/event-timers.section.weekly_description'))
                    ->icon('heroicon-o-calendar-days')
                    ->schema([
                        CheckboxList::make('days')
                            ->label(__('filament/event-timers.form.days'))
                            ->options([
                                'Monday' => __('filament/event-timers.days.monday'),
                                'Tuesday' => __('filament/event-timers.days.tuesday'),
                                'Wednesday' => __('filament/event-timers.days.wednesday'),
                                'Thursday' => __('filament/event-timers.days.thursday'),
                                'Friday' => __('filament/event-timers.days.friday'),
                                'Saturday' => __('filament/event-timers.days.saturday'),
                                'Sunday' => __('filament/event-timers.days.sunday'),
                            ])
                            ->columns(4)
                            ->required()
                            ->columnSpanFull(),

                        TextInput::make('hour')
                            ->label(__('filament/event-timers.form.hour'))
                            ->helperText(__('filament/event-timers.form.hour_helper'))
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(23)
                            ->required(),

                        TextInput::make('min')
                            ->label(__('filament/event-timers.form.min'))
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(59)
                            ->default(0)
                            ->required(),
                    ])->columns(2)
                    ->visible(fn($get) => $get('type') === 'weekly'),

                Section::make(__('filament/event-timers.section.static'))
                    ->description(__('filament/event-timers.section.static_description'))
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        TextInput::make('time')
                            ->label(__('filament/event-timers.form.time'))
                            ->placeholder('Saturday 12:00 - 23:00')
                            ->maxLength(100)
                            ->required(),
                    ])
                    ->visible(fn($get) => $get('type') === 'static'),
            ]);
    }

    private static function getIconOptions(): array
    {
        return [
            'clock' => 'Clock',
            'fire' => 'Fire',
            'bolt' => 'Bolt',
            'star' => 'Star',
            'trophy' => 'Trophy',
            'shield-check' => 'Shield Check',
            'flag' => 'Flag',
            'map' => 'Map',
            'globe-alt' => 'Globe',
            'user-group' => 'User Group',
            'sword' => 'Sword',
            'skull' => 'Skull',
            'sparkles' => 'Sparkles',
            'rocket-launch' => 'Rocket Launch',
            'puzzle-piece' => 'Puzzle Piece',
            'gift' => 'Gift',
            'heart' => 'Heart',
            'exclamation-triangle' => 'Warning',
            'bell-alert' => 'Bell Alert',
            'cube' => 'Cube',
        ];
    }
}
