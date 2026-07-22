<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Enums\SilkTypeEnum;
use App\Enums\SilkTypeIsroEnum;
use App\Helpers\LicenseHelper;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Icons\Heroicon;

class Referral extends AbstractSettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserPlus;

    protected static ?int $navigationSort = 70;

    public static function getNavigationLabel(): string
    {
        return __('filament/settings.form.tabs.referral');
    }

    public function isLocked(): bool
    {
        return ! LicenseHelper::isValid();
    }

    public function getLockedDescription(): string
    {
        return __('filament/settings.locked.license_required_referral');
    }

    protected function getSettingKeys(): array
    {
        return [
            'referral_enabled',
            'referral_min_level',
            'referral_silk_reward',
            'referral_silk_type',
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            Toggle::make('referral_enabled')
                ->label(__('filament/settings.form.referral.enabled'))
                ->helperText(__('filament/settings.form.referral.enabled_description'))
                ->live()
                ->columnSpanFull(),

            TextInput::make('referral_min_level')
                ->label(__('filament/settings.form.referral.min_level'))
                ->helperText(__('filament/settings.form.referral.min_level_description'))
                ->numeric()
                ->minValue(1)
                ->maxValue(200)
                ->default(20)
                ->visible(fn(Get $get) => (bool) $get('referral_enabled')),

            TextInput::make('referral_silk_reward')
                ->label(__('filament/settings.form.referral.silk_reward'))
                ->helperText(__('filament/settings.form.referral.silk_reward_description'))
                ->numeric()
                ->minValue(1)
                ->default(50)
                ->visible(fn(Get $get) => (bool) $get('referral_enabled')),

            Select::make('referral_silk_type')
                ->label(__('filament/settings.form.referral.silk_type'))
                ->options(fn() => match (config('silkpanel.version')) {
                    'isro' => SilkTypeIsroEnum::class,
                    default => SilkTypeEnum::class,
                })
                ->visible(fn(Get $get) => (bool) $get('referral_enabled')),
        ];
    }
}
