<?php

namespace App\Filament\Clusters\Settings\Pages;

use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;

class Contact extends AbstractSettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static ?int $navigationSort = 80;

    public static function getNavigationLabel(): string
    {
        return __('filament/settings.form.tabs.contact');
    }

    protected function getSettingKeys(): array
    {
        return [
            'contact_email',
            'contact_phone',
            'contact_address',
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('contact_email')
                ->label(__('filament/settings.form.contact.contact_email'))
                ->email(),

            TextInput::make('contact_phone')
                ->label(__('filament/settings.form.contact.contact_phone'))
                ->tel(),

            TextInput::make('contact_address')
                ->label(__('filament/settings.form.contact.contact_address')),
        ];
    }
}
