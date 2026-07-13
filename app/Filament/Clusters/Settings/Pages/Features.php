<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Icons\Heroicon;

class Features extends AbstractSettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRocketLaunch;

    protected static ?int $navigationSort = 50;

    public static function getNavigationLabel(): string
    {
        return __('filament/settings.form.tabs.features');
    }

    protected function getFormColumns(): int|array
    {
        return 1;
    }

    protected function getSettingKeys(): array
    {
        return [
            'registration_open',
            'email_verification_required',
            'maintenance_message',
            'tos_enabled',
            'login_with_name',
            'webmall_enabled',
            'custom_procedures_enabled',
            'history_unique_enabled',
            'history_global_enabled',
            'webmall_require_logout',
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            Toggle::make('registration_open')
                ->label(__('filament/settings.form.features.registration_open'))
                ->helperText(__('filament/settings.form.features.registration_open_description')),

            Toggle::make('email_verification_required')
                ->label(__('filament/settings.form.features.email_verification_required'))
                ->helperText(__('filament/settings.form.features.email_verification_required_description')),

            Textarea::make('maintenance_message')
                ->label(__('filament/settings.form.features.maintenance_message'))
                ->placeholder(__('filament/settings.form.features.maintenance_message_placeholder'))
                ->rows(4),

            Toggle::make('tos_enabled')
                ->label(__('filament/settings.form.features.tos_enabled'))
                ->helperText(__('filament/settings.form.features.tos_enabled_description'))
                ->live(),

            RichEditor::make('tos_text')
                ->label(__('filament/settings.form.features.tos_text'))
                ->toolbarButtons([
                    ['bold', 'italic', 'underline', 'strike', 'link'],
                    ['h2', 'h3'],
                    ['bulletList', 'orderedList'],
                    ['undo', 'redo'],
                ])
                ->columnSpanFull()
                ->dehydrated()
                ->visible(fn(Get $get) => (bool) $get('tos_enabled')),

            Toggle::make('login_with_name')
                ->label(__('filament/settings.form.features.login_with_name'))
                ->helperText(__('filament/settings.form.features.login_with_name_description')),

            Toggle::make('webmall_enabled')
                ->label(__('filament/settings.form.features.webmall_enabled'))
                ->helperText(__('filament/settings.form.features.webmall_enabled_description'))
                ->live(),

            Toggle::make('custom_procedures_enabled')
                ->label('Enable Custom Procedures')
                ->helperText('Enable execution of custom MSSQL procedures for supported CMS actions.'),

            Toggle::make('history_unique_enabled')
                ->label(__('filament/settings.form.features.history_unique_enabled'))
                ->helperText(__('filament/settings.form.features.history_unique_enabled_description'))
                ->default(true)
                ->visible(fn() => config('silkpanel.version') === 'isro'),

            Toggle::make('history_global_enabled')
                ->label(__('filament/settings.form.features.history_global_enabled'))
                ->helperText(__('filament/settings.form.features.history_global_enabled_description'))
                ->default(true)
                ->visible(fn() => config('silkpanel.version') === 'isro'),

            Toggle::make('webmall_require_logout')
                ->label(__('filament/settings.form.features.webmall_require_logout'))
                ->helperText(__('filament/settings.form.features.webmall_require_logout_description'))
                ->visible(fn(Get $get) => (bool) $get('webmall_enabled')),
        ];
    }

    protected function afterSave(array $data): void
    {
        if (array_key_exists('tos_text', $data)) {
            Setting::set('tos_text', $data['tos_text'] ?? '', null, null, null);
        }
    }
}
