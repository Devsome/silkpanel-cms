<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Helpers\LicenseHelper;
use App\Services\OnlinePlayerCountService;
use App\View\Components\OnlineCounter;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions as ActionsComponent;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

/**
 * Point the public online player count at a custom database source.
 *
 * The standard `_ShardCurrentUser` log table is used out of the box and stays
 * the fallback. Servers storing their online count elsewhere can enable the
 * override here and select connection → table → column plus the aggregate
 * (single value, count, distinct count, sum, max) to apply.
 *
 * @see OnlinePlayerCountService for the query that consumes these settings.
 */
class OnlinePlayersSource extends AbstractSettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSignal;

    protected static ?int $navigationSort = 25;

    public static function getNavigationLabel(): string
    {
        return __('filament/online-players.navigation');
    }

    public function getTitle(): string
    {
        return __('filament/online-players.title');
    }

    public function isLocked(): bool
    {
        return ! LicenseHelper::isValid();
    }

    public function getLockedDescription(): string
    {
        return __('filament/settings.locked.license_required_online_players');
    }

    protected function getFormColumns(): int|array
    {
        return 1;
    }

    protected function getSettingKeys(): array
    {
        return [
            OnlinePlayerCountService::ENABLED_KEY,
            OnlinePlayerCountService::CONNECTION_KEY,
            OnlinePlayerCountService::TABLE_KEY,
            OnlinePlayerCountService::COLUMN_KEY,
            OnlinePlayerCountService::AGGREGATE_KEY,
            OnlinePlayerCountService::ORDER_COLUMN_KEY,
            OnlinePlayerCountService::ORDER_DIRECTION_KEY,
            OnlinePlayerCountService::FILTER_COLUMN_KEY,
            OnlinePlayerCountService::FILTER_VALUE_KEY,
        ];
    }

    protected function getFormSchema(): array
    {
        $enabled = fn(Get $get): bool => (bool) $get(OnlinePlayerCountService::ENABLED_KEY);

        return [
            Section::make(__('filament/online-players.sections.status'))
                ->description(__('filament/online-players.sections.status_description'))
                ->schema([
                    Toggle::make(OnlinePlayerCountService::ENABLED_KEY)
                        ->label(__('filament/online-players.fields.enabled'))
                        ->helperText(__('filament/online-players.fields.enabled_description'))
                        ->default(false)
                        ->live()
                        ->columnSpanFull(),
                ])->columns(1),

            Section::make(__('filament/online-players.sections.source'))
                ->description(__('filament/online-players.sections.source_description'))
                ->schema([
                    Select::make(OnlinePlayerCountService::CONNECTION_KEY)
                        ->label(__('filament/online-players.fields.connection'))
                        ->helperText(__('filament/online-players.fields.connection_description'))
                        ->options(fn(): array => $this->getConnectionOptions())
                        ->searchable()
                        ->preload()
                        ->live()
                        ->afterStateUpdated(fn(callable $set) => $this->resetColumnSelections($set, resetTable: true))
                        ->required($enabled),

                    Select::make(OnlinePlayerCountService::TABLE_KEY)
                        ->label(__('filament/online-players.fields.table'))
                        ->helperText(__('filament/online-players.fields.table_description'))
                        ->options(fn(Get $get): array => $this->getTableOptions($get))
                        ->searchable()
                        ->preload()
                        ->live()
                        ->afterStateUpdated(fn(callable $set) => $this->resetColumnSelections($set))
                        ->required($enabled),

                    Select::make(OnlinePlayerCountService::AGGREGATE_KEY)
                        ->label(__('filament/online-players.fields.aggregate'))
                        ->helperText(__('filament/online-players.fields.aggregate_description'))
                        ->options(fn(): array => $this->getAggregateOptions())
                        ->default(OnlinePlayerCountService::AGGREGATE_VALUE)
                        ->selectablePlaceholder(false)
                        ->live()
                        ->required($enabled),

                    Select::make(OnlinePlayerCountService::COLUMN_KEY)
                        ->label(__('filament/online-players.fields.column'))
                        ->helperText(__('filament/online-players.fields.column_description'))
                        ->options(fn(Get $get): array => $this->getColumnOptions($get))
                        ->searchable()
                        ->preload()
                        ->visible(fn(Get $get): bool => $this->aggregateNeedsColumn($get))
                        ->required(fn(Get $get): bool => $enabled($get) && $this->aggregateNeedsColumn($get)),
                ])
                ->columns(2)
                ->visible($enabled),

            Section::make(__('filament/online-players.sections.ordering'))
                ->description(__('filament/online-players.sections.ordering_description'))
                ->schema([
                    Select::make(OnlinePlayerCountService::ORDER_COLUMN_KEY)
                        ->label(__('filament/online-players.fields.order_column'))
                        ->helperText(__('filament/online-players.fields.order_column_description'))
                        ->options(fn(Get $get): array => $this->getColumnOptions($get))
                        ->searchable()
                        ->preload()
                        ->placeholder('—'),

                    Select::make(OnlinePlayerCountService::ORDER_DIRECTION_KEY)
                        ->label(__('filament/online-players.fields.order_direction'))
                        ->options([
                            'desc' => __('filament/online-players.order_directions.desc'),
                            'asc' => __('filament/online-players.order_directions.asc'),
                        ])
                        ->default('desc')
                        ->selectablePlaceholder(false),
                ])
                ->columns(2)
                ->visible(fn(Get $get): bool => $enabled($get)
                    && ($get(OnlinePlayerCountService::AGGREGATE_KEY) ?? OnlinePlayerCountService::AGGREGATE_VALUE) === OnlinePlayerCountService::AGGREGATE_VALUE),

            Section::make(__('filament/online-players.sections.filter'))
                ->description(__('filament/online-players.sections.filter_description'))
                ->schema([
                    Select::make(OnlinePlayerCountService::FILTER_COLUMN_KEY)
                        ->label(__('filament/online-players.fields.filter_column'))
                        ->helperText(__('filament/online-players.fields.filter_column_description'))
                        ->options(fn(Get $get): array => $this->getColumnOptions($get))
                        ->searchable()
                        ->preload()
                        ->placeholder('—'),

                    TextInput::make(OnlinePlayerCountService::FILTER_VALUE_KEY)
                        ->label(__('filament/online-players.fields.filter_value'))
                        ->helperText(__('filament/online-players.fields.filter_value_description')),
                ])
                ->columns(2)
                ->visible($enabled),

            ActionsComponent::make([
                Action::make('testOnlinePlayerSource')
                    ->label(__('filament/online-players.actions.test'))
                    ->icon('heroicon-o-beaker')
                    ->color('gray')
                    ->action(fn() => $this->testOnlinePlayerSource()),
            ])->visible(fn(Get $get): bool => ! $this->isLocked() && $enabled($get)),
        ];
    }

    /**
     * Flush the cached counter so a changed source takes effect immediately.
     *
     * @param  array<string, mixed>  $data
     */
    protected function afterSave(array $data): void
    {
        Cache::forget(OnlineCounter::CACHE_KEY);
    }

    /**
     * Run the currently configured (possibly unsaved) source and report the result.
     */
    public function testOnlinePlayerSource(): void
    {
        if ($this->isLocked()) {
            return;
        }

        $service = app(OnlinePlayerCountService::class);
        $fallback = $service->fallbackCount();

        try {
            $count = $service->customCount($this->data ?? []);
        } catch (Throwable $e) {
            Notification::make()
                ->danger()
                ->title(__('filament/online-players.notifications.test_failed_title'))
                ->body($e->getMessage())
                ->send();

            return;
        }

        if ($count === null) {
            Notification::make()
                ->warning()
                ->title(__('filament/online-players.notifications.test_incomplete_title'))
                ->body(__('filament/online-players.notifications.test_incomplete_message'))
                ->send();

            return;
        }

        Notification::make()
            ->success()
            ->title(__('filament/online-players.notifications.test_success_title'))
            ->body(__('filament/online-players.notifications.test_success_message', [
                'count' => $count,
                'fallback' => $fallback,
            ]))
            ->send();
    }

    /**
     * Clear the column pickers when the source they refer to changes.
     */
    private function resetColumnSelections(callable $set, bool $resetTable = false): void
    {
        if ($resetTable) {
            $set(OnlinePlayerCountService::TABLE_KEY, '');
        }

        $set(OnlinePlayerCountService::COLUMN_KEY, '');
        $set(OnlinePlayerCountService::ORDER_COLUMN_KEY, '');
        $set(OnlinePlayerCountService::FILTER_COLUMN_KEY, '');
    }

    private function aggregateNeedsColumn(Get $get): bool
    {
        $aggregate = (string) ($get(OnlinePlayerCountService::AGGREGATE_KEY) ?: OnlinePlayerCountService::AGGREGATE_VALUE);

        return in_array($aggregate, OnlinePlayerCountService::AGGREGATES_REQUIRING_COLUMN, true);
    }

    /**
     * @return array<string, string>
     */
    private function getAggregateOptions(): array
    {
        return [
            OnlinePlayerCountService::AGGREGATE_VALUE => __('filament/online-players.aggregates.value'),
            OnlinePlayerCountService::AGGREGATE_COUNT_ROWS => __('filament/online-players.aggregates.count_rows'),
            OnlinePlayerCountService::AGGREGATE_COUNT => __('filament/online-players.aggregates.count'),
            OnlinePlayerCountService::AGGREGATE_COUNT_DISTINCT => __('filament/online-players.aggregates.count_distinct'),
            OnlinePlayerCountService::AGGREGATE_SUM => __('filament/online-players.aggregates.sum'),
            OnlinePlayerCountService::AGGREGATE_MAX => __('filament/online-players.aggregates.max'),
        ];
    }

    /**
     * Configured database connections, limited to the Silkroad ones.
     *
     * @return array<string, string>
     */
    private function getConnectionOptions(): array
    {
        $connections = config('database.connections', []);

        if (! is_array($connections)) {
            return [];
        }

        $options = [];

        foreach ($connections as $name => $config) {
            if (! is_string($name) || $name === '') {
                continue;
            }

            $database = is_array($config) ? (string) ($config['database'] ?? '') : '';

            if (Str::startsWith(strtolower($name), 'sro_') || Str::startsWith(strtolower($database), 'sro_')) {
                $options[$name] = $database !== '' ? "{$name} ({$database})" : $name;
            }
        }

        asort($options);

        return $options;
    }

    /**
     * @return array<string, string>
     */
    private function getTableOptions(Get $get): array
    {
        $connection = trim((string) $get(OnlinePlayerCountService::CONNECTION_KEY));

        if ($connection === '') {
            return [];
        }

        try {
            $tables = DB::connection($connection)->getSchemaBuilder()->getTableListing();
        } catch (Throwable) {
            return [];
        }

        $options = [];

        foreach ((array) $tables as $table) {
            if (is_string($table) && trim($table) !== '') {
                $options[trim($table)] = trim($table);
            }
        }

        asort($options);

        return $options;
    }

    /**
     * @return array<string, string>
     */
    private function getColumnOptions(Get $get): array
    {
        $connection = trim((string) $get(OnlinePlayerCountService::CONNECTION_KEY));
        $table = trim((string) $get(OnlinePlayerCountService::TABLE_KEY));

        if ($connection === '' || $table === '') {
            return [];
        }

        try {
            $columns = DB::connection($connection)->getSchemaBuilder()->getColumnListing($table);
        } catch (Throwable) {
            return [];
        }

        $options = [];

        foreach ((array) $columns as $column) {
            if (is_string($column) && $column !== '') {
                $options[$column] = $column;
            }
        }

        return $options;
    }
}
