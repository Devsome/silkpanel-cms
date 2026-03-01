<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\SilkTypeEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Illuminate\Support\Str;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('filament/users.section.web'))
                            ->description(__('filament/users.section.web_description'))
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('filament/users.form.name'))
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('email')
                                    ->label(__('filament/users.form.email'))
                                    ->email()
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),
                                DateTimePicker::make('email_verified_at')
                                    ->label(__('filament/users.form.email_verified_at')),
                                TextInput::make('reflink')
                                    ->label(__('filament/users.form.reflink'))
                                    ->suffixAction(
                                        Action::make('generate')
                                            ->label(__('filament/users.form.generate_reflink'))
                                            ->action(function ($state, callable $set) {
                                                $set('reflink', (string) Str::uuid());
                                            })
                                            ->icon('heroicon-m-arrow-path')
                                    ),
                                Select::make('roles')
                                    ->label(__('filament/users.form.roles'))
                                    ->multiple()
                                    ->relationship(name: 'roles', titleAttribute: 'name')
                                    ->preload()
                                    ->searchable(),
                            ])
                            ->columns(1)
                            ->columnSpan(['lg' => 2]),
                    ])->columnSpan(['lg' => 2]),
                Group::make()
                    ->schema([
                        Section::make(__('filament/users.section.game'))
                            ->schema([
                                TextEntry::make('silkroad_id')
                                    ->label(__('filament/users.form.silkroad_id')),
                                TextEntry::make('jid')
                                    ->label(__('filament/users.form.jid'))
                                    ->weight(FontWeight::Medium),
                                TextEntry::make('tbuser.AccPlayTime')
                                    ->formatStateUsing(fn($state) => round($state / 60) . ' ' . __('filament/users.form.minutes'))
                                    ->label(__('filament/users.form.acc_play_time')),
                                TextEntry::make('tbuser.sec_primary')
                                    ->label(__('filament/users.form.sec_primary')),
                                TextEntry::make('tbuser.sec_content')
                                    ->label(__('filament/users.form.sec_content')),
                                TextEntry::make('is_gamemaster')
                                    ->label(__('filament/users.form.is_gamemaster'))
                                    ->badge()
                                    ->state(function ($record) {
                                        return $record->tbuser->isGamemaster() ?
                                            __('filament/users.form.is_gamemaster_yes') : __('filament/users.form.is_gamemaster_no');
                                    })
                                    ->color(fn(string $state): string => match ($state) {
                                        __('filament/users.form.is_gamemaster_yes') => 'primary',
                                        __('filament/users.form.is_gamemaster_no') => 'gray',
                                        default => 'gray',
                                    }),

                                Section::make()
                                    ->schema([
                                        IconEntry::make('blocked')
                                            ->label(__('filament/users.form.blocked'))
                                            ->state(fn($record) => $record->tbuser->activeBlock ? true : false)
                                            ->trueIcon('heroicon-o-check-circle')
                                            ->falseIcon('heroicon-o-x-circle')
                                            ->trueColor('success')
                                            ->falseColor('gray')
                                            ->columnSpanFull(),
                                        TextEntry::make('block_reason')
                                            ->state(function ($record) {
                                                return $record->tbuser->activeBlock ? $record->tbuser->activeBlock->punishment->Guide : '-';
                                            })
                                            ->label(__('filament/users.form.blocked_reason')),
                                        TextEntry::make('block_description')
                                            ->state(function ($record) {
                                                return $record->tbuser->activeBlock ? $record->tbuser->activeBlock->punishment->Description : '-';
                                            })
                                            ->label(__('filament/users.form.blocked_description')),
                                        TextEntry::make('block_start')
                                            ->state(function ($record) {
                                                return \Carbon\Carbon::parse($record->tbuser->activeBlock?->punishment->BlockStartTime)->format('d.m.Y H:i');
                                            })
                                            ->label(__('filament/users.form.blocked_start')),
                                        TextEntry::make('block_end')
                                            ->state(function ($record) {
                                                return \Carbon\Carbon::parse($record->tbuser->activeBlock?->punishment->BlockEndTime)->format('d.m.Y H:i');
                                            })
                                            ->label(__('filament/users.form.blocked_end')),
                                    ])
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->secondary()
                                    ->footerActions([
                                        Action::make('unblock')
                                            ->label(__('filament/users.form.unblock'))
                                            ->icon('heroicon-o-lock-open')
                                            ->color('gray')
                                            ->visible(fn($record) => $record->tbuser->activeBlock ? true : false)
                                            ->action(function ($record) {
                                                $block = $record->tbuser->activeBlock;
                                                if ($block) {
                                                    $block->timeEnd = now();
                                                    $block->save();
                                                    Notification::make()
                                                        ->title(__('filament/users.notifications.unblock_success_title'))
                                                        ->body(__('filament/users.notifications.unblock_success_message'))
                                                        ->success()
                                                        ->send();
                                                }
                                            }),
                                    ])
                                    ->visible(fn($record) => $record->tbuser->activeBlock ? true : false),

                            ])
                            ->columns(3)
                            ->columnSpan(['lg' => 2]),
                        Section::make(__('filament/users.section.silk'))
                            ->schema([
                                Section::make()
                                    ->schema([
                                        TextEntry::make('getSkSilk.silk_own')
                                            ->label(__('filament/users.form.silk_own'))
                                            ->state(fn($record) => number_format($record->getSkSilk->silk_own, 0, ',', '.'))
                                            ->weight(FontWeight::Bold),
                                        TextEntry::make('getSkSilk.silk_gift')
                                            ->label(__('filament/users.form.silk_gift'))
                                            ->state(fn($record) => number_format($record->getSkSilk->silk_gift, 0, ',', '.'))
                                            ->weight(FontWeight::Medium),
                                        TextEntry::make('getSkSilk.silk_point')
                                            ->label(__('filament/users.form.silk_point'))
                                            ->state(fn($record) => number_format($record->getSkSilk->silk_point, 0, ',', '.'))
                                            ->weight(FontWeight::Medium),
                                    ])
                                    ->columns(3)
                                    ->columnSpan(['lg' => 2])
                                    ->secondary(),
                                TextInput::make('silk_amount')
                                    ->label(__('filament/users.form.silk_amount'))
                                    ->helperText(__('filament/users.form.silk_amount_helper'))
                                    ->integer(),
                                Select::make('silk_type')
                                    ->label(__('filament/users.form.silk_type'))
                                    ->options(SilkTypeEnum::class, 'label', 'value'),
                            ])
                            ->footer([
                                Action::make('custom_action')
                                    ->label(__('filament/users.form.silk_action'))
                                    ->action(function ($state, $get) {
                                        $silkAmount = $get('silk_amount');
                                        if (empty($silkAmount)) {
                                            Notification::make()
                                                ->title(__('filament/users.notifications.silk_amount_empty'))
                                                ->danger()
                                                ->send();
                                            return;
                                        }
                                        if (!in_array($get('silk_type')?->value, SilkTypeEnum::values())) {
                                            Notification::make()
                                                ->title(__('filament/users.notifications.silk_type_invalid'))
                                                ->danger()
                                                ->send();
                                            return;
                                        }
                                        try {
                                            \App\Helpers\SilkHelper::addSilk(
                                                data_get($state, 'jid') ?? data_get($state, 'PortalJID'),
                                                $silkAmount,
                                                $get('silk_type')->value,
                                                request()->ip()
                                            );
                                        } catch (\Exception $e) {
                                            Notification::make()
                                                ->title(__('filament/users.notifications.silk_added_title'))
                                                ->body($e->getMessage())
                                                ->danger()
                                                ->send();
                                            return;
                                        }

                                        Notification::make()
                                            ->title(__('filament/users.notifications.silk_added_title'))
                                            ->body(__('filament/users.notifications.silk_added_message', ['amount' => $silkAmount, 'name' => $get('name')]))
                                            ->success()
                                            ->send();
                                    })
                                    ->icon('heroicon-o-arrow-up-right')
                                    ->color('gray'),
                            ])
                            ->columns(2)
                            ->columnSpan(['lg' => 2]),
                    ])->columnSpan(['lg' => 2]),
            ])
            ->columns(4);
    }
}
