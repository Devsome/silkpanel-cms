<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Enums\Languages;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;

class General extends AbstractSettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInformationCircle;

    protected static ?int $navigationSort = 10;

    public static function getNavigationLabel(): string
    {
        return __('filament/settings.form.tabs.general');
    }

    protected function getSettingKeys(): array
    {
        return [
            'site_title',
            'site_description',
            'site_keywords',
            'frontend_languages',
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('site_title')
                ->label(__('filament/settings.form.page_info.site_title'))
                ->placeholder(__('filament/settings.form.page_info.site_title_placeholder'))
                ->maxLength(255),

            Textarea::make('site_description')
                ->label(__('filament/settings.form.page_info.site_description'))
                ->placeholder(__('filament/settings.form.page_info.site_description_placeholder'))
                ->rows(3),

            TextInput::make('site_keywords')
                ->label(__('filament/settings.form.page_info.site_keywords'))
                ->placeholder(__('filament/settings.form.page_info.site_keywords_placeholder'))
                ->maxLength(255),

            Select::make('frontend_languages')
                ->label(__('filament/settings.form.page_info.frontend_languages'))
                ->helperText(__('filament/settings.form.page_info.frontend_languages_description'))
                ->multiple()
                ->options(Languages::class)
                ->searchable()
                ->preload()
                ->default(['en'])
                ->required(),
        ];
    }
}
