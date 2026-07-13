<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Helpers\LicenseHelper;
use App\Services\FakePlayerService;
use BackedEnum;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Icons\Heroicon;

class FakePlayers extends AbstractSettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?int $navigationSort = 30;

    public static function getNavigationLabel(): string
    {
        return __('filament/settings.form.tabs.fake_players');
    }

    public function isLocked(): bool
    {
        return ! LicenseHelper::isValid();
    }

    public function getLockedDescription(): string
    {
        return __('filament/settings.locked.license_required');
    }

    protected function getSettingKeys(): array
    {
        return [
            'fake_players_enabled',
            'fake_players_interval',
            'fake_player_rules',
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            Toggle::make('fake_players_enabled')
                ->label(__('filament/settings.form.fake_players.enabled'))
                ->helperText(__('filament/settings.form.fake_players.enabled_description'))
                ->live()
                ->columnSpanFull(),

            TextInput::make('fake_players_interval')
                ->label(__('filament/settings.form.fake_players.interval'))
                ->helperText(__('filament/settings.form.fake_players.interval_description'))
                ->numeric()
                ->minValue(1)
                ->maxValue(1440)
                ->default(FakePlayerService::DEFAULT_INTERVAL)
                ->suffix(__('filament/settings.form.fake_players.interval_suffix'))
                ->visible(fn(Get $get) => (bool) $get('fake_players_enabled')),

            Repeater::make('fake_player_rules')
                ->label(__('filament/settings.form.fake_players.rules'))
                ->helperText(__('filament/settings.form.fake_players.rules_description'))
                ->schema([
                    TextInput::make('real_min')
                        ->label(__('filament/settings.form.fake_players.real_min'))
                        ->numeric()
                        ->minValue(0)
                        ->required(),
                    TextInput::make('real_max')
                        ->label(__('filament/settings.form.fake_players.real_max'))
                        ->numeric()
                        ->minValue(0)
                        ->required(),
                    TextInput::make('fake_min')
                        ->label(__('filament/settings.form.fake_players.fake_min'))
                        ->numeric()
                        ->minValue(0)
                        ->required(),
                    TextInput::make('fake_max')
                        ->label(__('filament/settings.form.fake_players.fake_max'))
                        ->numeric()
                        ->minValue(0)
                        ->required(),
                ])
                ->columns(4)
                ->reorderable()
                ->collapsible()
                ->itemLabel(fn(array $state): ?string => isset($state['real_min'], $state['real_max'])
                    ? __('filament/settings.form.fake_players.rule_label', [
                        'min' => $state['real_min'],
                        'max' => $state['real_max'],
                    ])
                    : null)
                ->columnSpanFull()
                ->visible(fn(Get $get) => (bool) $get('fake_players_enabled')),
        ];
    }

    protected function beforeSave(array $data): bool
    {
        return $this->validateFakePlayerRules($data['fake_player_rules'] ?? []);
    }

    /**
     * Validate the fake player range rules before persisting.
     *
     * Ensures every rule has real min ≤ max, fake min ≤ max, non-negative
     * integers, and that no two real ranges overlap. On failure a danger
     * notification is shown and the save is aborted.
     *
     * @param  mixed  $rules
     */
    private function validateFakePlayerRules($rules): bool
    {
        if (! is_array($rules) || $rules === []) {
            return true;
        }

        $ranges = [];

        foreach (array_values($rules) as $position => $rule) {
            $row = $position + 1;

            foreach (['real_min', 'real_max', 'fake_min', 'fake_max'] as $field) {
                if (! isset($rule[$field]) || ! is_numeric($rule[$field]) || (int) $rule[$field] < 0) {
                    return $this->failFakePlayerValidation(
                        __('filament/settings.form.fake_players.validation.invalid_number', ['row' => $row])
                    );
                }
            }

            $realMin = (int) $rule['real_min'];
            $realMax = (int) $rule['real_max'];
            $fakeMin = (int) $rule['fake_min'];
            $fakeMax = (int) $rule['fake_max'];

            if ($realMin > $realMax) {
                return $this->failFakePlayerValidation(
                    __('filament/settings.form.fake_players.validation.real_order', ['row' => $row])
                );
            }

            if ($fakeMin > $fakeMax) {
                return $this->failFakePlayerValidation(
                    __('filament/settings.form.fake_players.validation.fake_order', ['row' => $row])
                );
            }

            foreach ($ranges as $existing) {
                if ($realMin <= $existing['max'] && $realMax >= $existing['min']) {
                    return $this->failFakePlayerValidation(
                        __('filament/settings.form.fake_players.validation.overlap', [
                            'row' => $row,
                            'other' => $existing['row'],
                        ])
                    );
                }
            }

            $ranges[] = ['min' => $realMin, 'max' => $realMax, 'row' => $row];
        }

        return true;
    }

    /**
     * Emit a danger notification for a fake player validation failure.
     */
    private function failFakePlayerValidation(string $message): bool
    {
        Notification::make()
            ->danger()
            ->title(__('filament/settings.form.fake_players.validation.title'))
            ->body($message)
            ->send();

        return false;
    }
}
