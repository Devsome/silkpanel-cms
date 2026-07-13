<?php

namespace App\Filament\Clusters\Settings\Pages;

use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;

class Partners extends AbstractSettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?int $navigationSort = 60;

    public static function getNavigationLabel(): string
    {
        return __('filament/settings.form.tabs.partners');
    }

    protected function getFormColumns(): int|array
    {
        return 1;
    }

    protected function getSettingKeys(): array
    {
        return [
            'partners',
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            Repeater::make('partners')
                ->label(__('filament/settings.form.partners.partners'))
                ->schema([
                    TextInput::make('name')
                        ->label(__('filament/settings.form.partners.partner_name'))
                        ->required(),
                    TextInput::make('url')
                        ->label(__('filament/settings.form.partners.partner_url'))
                        ->url(),
                    FileUpload::make('logo')
                        ->label(__('filament/settings.form.partners.partner_logo'))
                        ->image()
                        ->directory('settings/partners'),
                    Textarea::make('description')
                        ->label(__('filament/settings.form.partners.partner_description'))
                        ->rows(3),
                ])
                ->columns(2)
                ->collapsible(),
        ];
    }
}
