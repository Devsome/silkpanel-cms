<?php

namespace App\Filament\Clusters\Settings\Pages;

use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;

class SocialMedia extends AbstractSettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShare;

    protected static ?int $navigationSort = 100;

    public static function getNavigationLabel(): string
    {
        return __('filament/settings.form.tabs.social_media');
    }

    protected function getSettingKeys(): array
    {
        return [
            'social_facebook',
            'social_twitter',
            'social_instagram',
            'social_discord',
            'discord_id',
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('social_facebook')
                ->label(__('filament/settings.form.social_media.social_facebook'))
                ->url(),

            TextInput::make('social_twitter')
                ->label(__('filament/settings.form.social_media.social_twitter'))
                ->url(),

            TextInput::make('social_instagram')
                ->label(__('filament/settings.form.social_media.social_instagram'))
                ->url(),

            TextInput::make('social_discord')
                ->label(__('filament/settings.form.social_media.social_discord'))
                ->url(),

            TextInput::make('discord_id')
                ->label(__('filament/settings.form.social_media.discord_id'))
                ->helperText(__('filament/settings.form.social_media.discord_id_description')),
        ];
    }
}
