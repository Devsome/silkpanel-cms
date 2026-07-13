<?php

namespace App\Filament\Clusters\Settings\Pages;

use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Support\Icons\Heroicon;

class Design extends AbstractSettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPaintBrush;

    protected static ?int $navigationSort = 40;

    public static function getNavigationLabel(): string
    {
        return __('filament/settings.form.tabs.design');
    }

    protected function getSettingKeys(): array
    {
        return [
            'logo',
            'favicon',
            'background_image',
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            FileUpload::make('logo')
                ->label(__('filament/settings.form.design.logo'))
                ->image()
                ->directory('settings/images')
                ->maxSize(5120),

            FileUpload::make('favicon')
                ->label(__('filament/settings.form.design.favicon'))
                ->helperText(__('filament/settings.form.design.favicon_description'))
                ->image()
                ->directory('settings/images')
                ->maxSize(2048)
                ->imageAspectRatio('1:1')
                ->automaticallyOpenImageEditorForAspectRatio()
                ->automaticallyResizeImagesToWidth(512)
                ->automaticallyResizeImagesToHeight(512),

            FileUpload::make('background_image')
                ->label(__('filament/settings.form.design.background_image'))
                ->image()
                ->directory('settings/images')
                ->maxSize(10240),
        ];
    }
}
