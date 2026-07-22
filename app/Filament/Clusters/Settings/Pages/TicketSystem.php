<?php

namespace App\Filament\Clusters\Settings\Pages;

use BackedEnum;
use Filament\Forms\Components\Toggle;
use Filament\Support\Icons\Heroicon;

class TicketSystem extends AbstractSettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTicket;

    protected static ?int $navigationSort = 110;

    public static function getNavigationLabel(): string
    {
        return __('filament/settings.form.tabs.ticket_system');
    }

    protected function getSettingKeys(): array
    {
        return [
            'is_ticket_system_enabled',
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            Toggle::make('is_ticket_system_enabled')
                ->label(__('filament/settings.form.ticket_system.enabled'))
                ->helperText(__('filament/settings.form.ticket_system.enabled_description')),
        ];
    }
}
