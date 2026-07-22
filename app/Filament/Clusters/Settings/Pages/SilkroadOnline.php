<?php

namespace App\Filament\Clusters\Settings\Pages;

use BackedEnum;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class SilkroadOnline extends AbstractSettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeAlt;

    protected static ?int $navigationSort = 20;

    public static function getNavigationLabel(): string
    {
        return __('filament/settings.form.tabs.silkroad_online');
    }

    protected function getSettingKeys(): array
    {
        return [
            'sro_shard_id',
            'sro_max_player',
            'sro_cap',
            'sro_exp_sp',
            'sro_party_exp',
            'sro_gold_drop_rate',
            'sro_drop_rate',
            'sro_trade_rate',
            'sro_race',
            'sro_fortress_war',
            'sro_hwid_limit',
            'sro_ip_limit',
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make(__('filament/settings.form.silkroad_online.internal_settings'))
                ->schema([
                    TextInput::make('sro_shard_id')
                        ->label(__('filament/settings.form.silkroad_online.shard_id'))
                        ->helperText(__('filament/settings.form.silkroad_online.shard_id_description'))
                        ->numeric()
                        ->default(64),
                ])->columns(2)
                ->columnSpan(2)
                ->secondary(),
            Section::make(__('filament/settings.form.silkroad_online.general_settings'))
                ->schema([
                    TextInput::make('sro_max_player')
                        ->label(__('filament/settings.form.silkroad_online.max_player'))
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(10000)
                        ->default(500),

                    TextInput::make('sro_cap')
                        ->label(__('filament/settings.form.silkroad_online.cap'))
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(200)
                        ->default(110),
                ])->columns(2)
                ->columnSpan(2)
                ->secondary(),
            Section::make(__('filament/settings.form.silkroad_online.rate_settings'))
                ->schema([
                    TextInput::make('sro_exp_sp')
                        ->label(__('filament/settings.form.silkroad_online.exp_sp'))
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(10000)
                        ->default(1),

                    TextInput::make('sro_party_exp')
                        ->label(__('filament/settings.form.silkroad_online.party_exp'))
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(10000)
                        ->default(1),

                    TextInput::make('sro_gold_drop_rate')
                        ->label(__('filament/settings.form.silkroad_online.gold_drop_rate'))
                        ->numeric()
                        ->minValue(0.1)
                        ->maxValue(1000)
                        ->step(0.1)
                        ->default(1.0),

                    TextInput::make('sro_drop_rate')
                        ->label(__('filament/settings.form.silkroad_online.drop_rate'))
                        ->numeric()
                        ->minValue(0.1)
                        ->maxValue(1000)
                        ->step(0.1)
                        ->default(1.0),

                    TextInput::make('sro_trade_rate')
                        ->label(__('filament/settings.form.silkroad_online.trade_rate'))
                        ->numeric()
                        ->minValue(0.1)
                        ->maxValue(1000)
                        ->step(0.1)
                        ->default(1.0),
                ])->columns(2)
                ->columnSpan(2)
                ->secondary(),
            Section::make(__('filament/settings.form.silkroad_online.other_settings'))
                ->schema([
                    CheckboxList::make('sro_race')
                        ->label(__('filament/settings.form.silkroad_online.race'))
                        ->options([
                            'china' => 'China',
                            'europe' => 'Europe',
                        ])
                        ->default(['china', 'europe'])
                        ->columns(2),
                    CheckboxList::make('sro_fortress_war')
                        ->label(__('filament/settings.form.silkroad_online.fortress_war'))
                        ->options([
                            'bandit' => 'Bandit Fortress',
                            'hotan' => 'Hotan Fortress',
                            'jangan' => 'Jangan Fortress',
                            'constantinople' => 'Constantinople Fortress',
                        ])
                        ->default(['bandit', 'hotan', 'jangan', 'constantinople'])
                        ->columns(3),
                ])->columns(2)
                ->columnSpan(2)
                ->secondary(),
            Section::make(__('filament/settings.form.silkroad_online.ip_settings'))
                ->schema([
                    TextInput::make('sro_hwid_limit')
                        ->label(__('filament/settings.form.silkroad_online.hwid_limit'))
                        ->helperText(__('filament/settings.form.silkroad_online.hwid_limit_description'))
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(10)
                        ->default(3),

                    TextInput::make('sro_ip_limit')
                        ->label(__('filament/settings.form.silkroad_online.ip_limit'))
                        ->helperText(__('filament/settings.form.silkroad_online.ip_limit_description'))
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(10)
                        ->default(3),
                ])->columns(2)
                ->columnSpan(2)
                ->secondary(),
        ];
    }
}
