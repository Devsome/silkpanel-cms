<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Filament\Clusters\Settings;
use App\Filament\Concerns\InteractsWithLockedState;
use App\Helpers\LicenseHelper;
use App\Models\Setting;
use App\Services\UniqueHistoryService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions as ActionsComponent;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

/**
 * Configure the vSRO / custom Unique History source.
 *
 * iSRO ships a standard `_LogInstanceWorldInfo` log table, so the public
 * `/history/uniques` page works out of the box there. vSRO has no standard
 * unique log — every server stores it differently — so this page lets an admin
 * point at any table, optionally LEFT JOIN another table (e.g. `_Char` for the
 * killer name / avatar or `_RefRegion` for the area) and map the source columns
 * onto the fields the frontend already expects. The shared query builder lives
 * in {@see UniqueHistoryService::customQuery()} so the frontend needs no changes.
 *
 * @property-read Schema $form
 */
class UniqueHistorySource extends Page
{
    use InteractsWithLockedState;

    protected static ?string $cluster = Settings::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTrophy;

    protected static ?int $navigationSort = 52;

    protected string $view = 'filament.pages.unique-history-source';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    /** @var array<int, array<string, mixed>> */
    public array $previewData = [];

    /** @var array<int, array<string, string>> */
    public array $previewColumns = [];

    public ?string $previewError = null;

    /** @var array<string, string> */
    public array $sourceTableColumns = [];

    /** @var array<string, string> */
    public array $joinTableColumns = [];

    public static function getNavigationLabel(): string
    {
        return __('filament/unique-history.navigation');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return config('silkpanel.version') === 'vsro';
    }

    public static function canAccess(): bool
    {
        return config('silkpanel.version') === 'vsro';
    }

    public function getTitle(): string
    {
        return __('filament/unique-history.title');
    }

    public function isLocked(): bool
    {
        return ! LicenseHelper::isValid();
    }

    public function getLockedDescription(): string
    {
        return __('filament/settings.locked.license_required_unique_history');
    }

    public function mount(): void
    {
        abort_unless(config('silkpanel.version') === 'vsro', 404);

        $this->form->fill($this->getSettingsArray());
        $this->refreshSourceColumns(notify: false);

        if ((bool) ($this->data['unique_history_source_join_enabled'] ?? false)) {
            $this->refreshJoinColumns(notify: false);
        }
    }

    public function form(Schema $schema): Schema
    {
        $locked = $this->isLocked();

        return $schema
            ->components([
                Form::make([
                    Section::make(__('filament/unique-history.sections.status'))
                        ->description(__('filament/unique-history.sections.status_description'))
                        ->schema([
                            Toggle::make('unique_history_vsro_enabled')
                                ->label(__('filament/unique-history.fields.enabled'))
                                ->helperText(__('filament/unique-history.fields.enabled_description'))
                                ->columnSpanFull(),
                        ]),

                    Section::make(__('filament/unique-history.sections.source'))
                        ->description(__('filament/unique-history.sections.source_description'))
                        ->schema([
                            Select::make('unique_history_source_connection')
                                ->label(__('filament/unique-history.fields.connection'))
                                ->options(fn(): array => $this->getConnectionOptions())
                                ->searchable()
                                ->preload()
                                ->live()
                                ->required(),

                            Select::make('unique_history_source_table')
                                ->label(__('filament/unique-history.fields.table'))
                                ->helperText(__('filament/unique-history.fields.table_description'))
                                ->options(fn(): array => $this->getTableOptions('unique_history_source_connection'))
                                ->searchable()
                                ->preload()
                                ->placeholder('sro_*')
                                ->suffixAction(
                                    Action::make('loadSourceColumns')
                                        ->label(__('filament/unique-history.actions.load_columns'))
                                        ->icon('heroicon-o-arrow-path')
                                        ->action(fn() => $this->refreshSourceColumns())
                                )
                                ->required(),

                            Select::make('unique_history_source_filter_column')
                                ->label(__('filament/unique-history.fields.filter_column'))
                                ->helperText(__('filament/unique-history.fields.filter_column_description'))
                                ->options(fn(): array => $this->sourceTableColumns)
                                ->searchable()
                                ->placeholder('—'),

                            TextInput::make('unique_history_source_filter_value')
                                ->label(__('filament/unique-history.fields.filter_value'))
                                ->helperText(__('filament/unique-history.fields.filter_value_description')),

                            TextInput::make('unique_history_source_kill_value')
                                ->label(__('filament/unique-history.fields.kill_value'))
                                ->helperText(__('filament/unique-history.fields.kill_value_description'))
                                ->columnSpanFull(),
                        ])
                        ->columns(2),

                    Section::make(__('filament/unique-history.sections.join'))
                        ->description(__('filament/unique-history.sections.join_description'))
                        ->schema([
                            Toggle::make('unique_history_source_join_enabled')
                                ->label(__('filament/unique-history.fields.join_enabled'))
                                ->helperText(__('filament/unique-history.fields.join_enabled_description'))
                                ->columnSpanFull()
                                ->live(),

                            Select::make('unique_history_source_join_connection')
                                ->label(__('filament/unique-history.fields.join_connection'))
                                ->options(fn(): array => $this->getConnectionOptions())
                                ->searchable()
                                ->preload()
                                ->live()
                                ->visible(fn(): bool => (bool) ($this->data['unique_history_source_join_enabled'] ?? false)),

                            Select::make('unique_history_source_join_table')
                                ->label(__('filament/unique-history.fields.join_table'))
                                ->helperText(__('filament/unique-history.fields.join_table_description'))
                                ->options(fn(): array => $this->getTableOptions('unique_history_source_join_connection'))
                                ->searchable()
                                ->preload()
                                ->placeholder('_Char')
                                ->suffixAction(
                                    Action::make('loadJoinColumns')
                                        ->label(__('filament/unique-history.actions.load_columns'))
                                        ->icon('heroicon-o-arrow-path')
                                        ->action(fn() => $this->refreshJoinColumns())
                                )
                                ->visible(fn(): bool => (bool) ($this->data['unique_history_source_join_enabled'] ?? false)),

                            Select::make('unique_history_source_join_local_key')
                                ->label(__('filament/unique-history.fields.join_local_key'))
                                ->helperText(__('filament/unique-history.fields.join_local_key_description'))
                                ->options(fn(): array => $this->sourceTableColumns)
                                ->searchable()
                                ->visible(fn(): bool => (bool) ($this->data['unique_history_source_join_enabled'] ?? false)),

                            Select::make('unique_history_source_join_foreign_key')
                                ->label(__('filament/unique-history.fields.join_foreign_key'))
                                ->helperText(__('filament/unique-history.fields.join_foreign_key_description'))
                                ->options(fn(): array => $this->joinTableColumns)
                                ->searchable()
                                ->visible(fn(): bool => (bool) ($this->data['unique_history_source_join_enabled'] ?? false)),
                        ])
                        ->columns(2),

                    Section::make(__('filament/unique-history.sections.mapping'))
                        ->description(__('filament/unique-history.sections.mapping_description'))
                        ->schema([
                            Select::make('unique_history_source_map_value')
                                ->label(__('filament/unique-history.fields.map_value'))
                                ->helperText(__('filament/unique-history.fields.map_value_description'))
                                ->options(fn(): array => $this->getMappingOptions())
                                ->searchable()
                                ->required(),

                            Select::make('unique_history_source_map_eventtime')
                                ->label(__('filament/unique-history.fields.map_eventtime'))
                                ->helperText(__('filament/unique-history.fields.map_eventtime_description'))
                                ->options(fn(): array => $this->getMappingOptions())
                                ->searchable()
                                ->required(),

                            Select::make('unique_history_source_map_eventtype')
                                ->label(__('filament/unique-history.fields.map_eventtype'))
                                ->helperText(__('filament/unique-history.fields.map_eventtype_description'))
                                ->options(fn(): array => $this->getMappingOptions())
                                ->searchable()
                                ->placeholder('—'),

                            Select::make('unique_history_source_map_charname')
                                ->label(__('filament/unique-history.fields.map_charname'))
                                ->helperText(__('filament/unique-history.fields.map_charname_description'))
                                ->options(fn(): array => $this->getMappingOptions())
                                ->searchable()
                                ->placeholder('—'),

                            Select::make('unique_history_source_map_charid')
                                ->label(__('filament/unique-history.fields.map_charid'))
                                ->helperText(__('filament/unique-history.fields.map_charid_description'))
                                ->options(fn(): array => $this->getMappingOptions())
                                ->searchable()
                                ->placeholder('—')
                                ->required(),

                            Select::make('unique_history_source_map_refobjid')
                                ->label(__('filament/unique-history.fields.map_refobjid'))
                                ->helperText(__('filament/unique-history.fields.map_refobjid_description'))
                                ->options(fn(): array => $this->getMappingOptions())
                                ->searchable()
                                ->placeholder('—'),

                            Select::make('unique_history_source_map_area')
                                ->label(__('filament/unique-history.fields.map_area'))
                                ->helperText(__('filament/unique-history.fields.map_area_description'))
                                ->options(fn(): array => $this->getMappingOptions())
                                ->searchable()
                                ->placeholder('—'),
                        ])
                        ->columns(2),

                    ActionsComponent::make([
                        Action::make('testUniqueHistorySource')
                            ->label(__('filament/unique-history.actions.test'))
                            ->icon('heroicon-o-beaker')
                            ->color('gray')
                            ->action(fn() => $this->testUniqueHistorySource()),
                    ])->visible(! $locked),
                ])
                    ->disabled($locked)
                    ->livewireSubmitHandler($locked ? null : 'save')
                    ->footer($locked ? [] : [
                        ActionsComponent::make([
                            Action::make('save')
                                ->label(__('filament/unique-history.actions.save'))
                                ->submit('save')
                                ->keyBindings(['mod+s']),
                        ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        if ($this->isLocked()) {
            return;
        }

        $data = $this->form->getState();

        foreach ($this->settingKeys() as $key) {
            if (array_key_exists($key, $data)) {
                Setting::set($key, $data[$key], null, null, null);
            }
        }

        Notification::make()
            ->success()
            ->title(__('filament/unique-history.notifications.saved_title'))
            ->body(__('filament/unique-history.notifications.saved_message'))
            ->send();
    }

    public function testUniqueHistorySource(): void
    {
        if ($this->isLocked()) {
            return;
        }

        $this->previewError = null;
        $this->previewData = [];
        $this->previewColumns = [];

        try {
            $data = $this->data ?? [];

            if (
                trim((string) ($data['unique_history_source_connection'] ?? '')) === ''
                || trim((string) ($data['unique_history_source_table'] ?? '')) === ''
            ) {
                throw new \RuntimeException(__('filament/unique-history.preview.missing_source'));
            }

            if (
                trim((string) ($data['unique_history_source_map_value'] ?? '')) === ''
                || trim((string) ($data['unique_history_source_map_eventtime'] ?? '')) === ''
            ) {
                throw new \RuntimeException(__('filament/unique-history.preview.missing_required_map'));
            }

            // Preview the current (possibly unsaved) values, independently of the
            // public enable toggle, so the mapping can be validated before going live.
            $override = array_merge($data, ['unique_history_vsro_enabled' => true]);
            $query = app(UniqueHistoryService::class)->customQuery(showSpawns: true, configOverride: $override);

            if ($query === null) {
                throw new \RuntimeException(__('filament/unique-history.preview.not_configured'));
            }

            $rows = $query->limit(10)->get();

            $this->previewData = $rows->map(fn($row) => (array) $row)->all();
            $this->previewColumns = [
                ['column' => 'Value', 'label' => 'Value'],
                ['column' => 'ValueCodeName128', 'label' => 'ValueCodeName128'],
                ['column' => 'EventTime', 'label' => 'EventTime'],
                ['column' => 'CharName16', 'label' => 'CharName16'],
                ['column' => 'CharID', 'label' => 'CharID'],
                ['column' => 'RefObjID', 'label' => 'RefObjID'],
                ['column' => 'AreaName', 'label' => 'AreaName'],
            ];
        } catch (Throwable $e) {
            $this->previewError = $e->getMessage();
        }
    }

    public function refreshSourceColumns(bool $notify = true): void
    {
        $this->sourceTableColumns = $this->loadColumns(
            (string) ($this->data['unique_history_source_connection'] ?? config('database.default')),
            trim((string) ($this->data['unique_history_source_table'] ?? '')),
            $notify,
        );
    }

    public function refreshJoinColumns(bool $notify = true): void
    {
        $this->joinTableColumns = $this->loadColumns(
            (string) ($this->data['unique_history_source_join_connection'] ?? config('database.default')),
            trim((string) ($this->data['unique_history_source_join_table'] ?? '')),
            $notify,
        );
    }

    /**
     * @return array<string, string>
     */
    private function loadColumns(string $connection, string $table, bool $notify): array
    {
        if ($table === '') {
            if ($notify) {
                Notification::make()
                    ->warning()
                    ->title(__('filament/unique-history.notifications.missing_table_title'))
                    ->body(__('filament/unique-history.notifications.missing_table_message'))
                    ->send();
            }

            return [];
        }

        try {
            $columns = DB::connection($connection)
                ->getSchemaBuilder()
                ->getColumnListing($table);

            $options = [];
            foreach ($columns as $column) {
                if (is_string($column) && $column !== '') {
                    $options[$column] = $column;
                }
            }

            if ($options === [] && $notify) {
                Notification::make()
                    ->warning()
                    ->title(__('filament/unique-history.notifications.columns_error_title'))
                    ->body(__('filament/unique-history.notifications.no_columns_message'))
                    ->send();
            } elseif ($notify) {
                Notification::make()
                    ->success()
                    ->title(__('filament/unique-history.notifications.columns_loaded_title'))
                    ->body(__('filament/unique-history.notifications.columns_loaded_message', ['count' => count($options)]))
                    ->send();
            }

            return $options;
        } catch (Throwable $e) {
            if ($notify) {
                Notification::make()
                    ->danger()
                    ->title(__('filament/unique-history.notifications.columns_error_title'))
                    ->body($e->getMessage())
                    ->send();
            }

            return [];
        }
    }

    /**
     * SRO connections, sourced from config, filtered to `sro_*`.
     *
     * @return array<string, string>
     */
    private function getConnectionOptions(): array
    {
        $connections = config('database.connections', []);
        $options = [];

        if (! is_array($connections)) {
            return $options;
        }

        foreach ($connections as $connectionName => $connectionConfig) {
            if (! is_string($connectionName) || $connectionName === '') {
                continue;
            }

            $databaseName = is_array($connectionConfig)
                ? (string) ($connectionConfig['database'] ?? '')
                : '';

            if (Str::startsWith(strtolower($connectionName), 'sro_') || Str::startsWith(strtolower($databaseName), 'sro_')) {
                $options[$connectionName] = $connectionName;
            }
        }

        asort($options);

        return $options;
    }

    /**
     * @return array<string, string>
     */
    private function getTableOptions(string $connectionKey): array
    {
        $connection = (string) ($this->data[$connectionKey] ?? config('database.default'));

        try {
            $tables = DB::connection($connection)
                ->getSchemaBuilder()
                ->getTableListing();
        } catch (Throwable) {
            return [];
        }

        if (! is_array($tables)) {
            return [];
        }

        $options = [];
        foreach ($tables as $table) {
            if (is_string($table) && $table !== '') {
                $rawName = trim($table);
                $options[$rawName] = $rawName;
            }
        }

        asort($options);

        return $options;
    }

    /**
     * Combined mapping options: base-table columns (prefix `base:`) plus, when
     * the join is enabled, join-table columns (prefix `join:`), grouped.
     *
     * @return array<string, array<string, string>>
     */
    private function getMappingOptions(): array
    {
        $options = [];

        $base = [];
        foreach (array_keys($this->sourceTableColumns) as $column) {
            $base['base:' . $column] = $column;
        }

        if ($base !== []) {
            $options[__('filament/unique-history.mapping.base_group')] = $base;
        }

        if ((bool) ($this->data['unique_history_source_join_enabled'] ?? false)) {
            $join = [];
            foreach (array_keys($this->joinTableColumns) as $column) {
                $join['join:' . $column] = $column;
            }

            if ($join !== []) {
                $options[__('filament/unique-history.mapping.join_group')] = $join;
            }
        }

        return $options;
    }

    /**
     * @return array<int, string>
     */
    private function settingKeys(): array
    {
        return [
            'unique_history_vsro_enabled',
            'unique_history_source_connection',
            'unique_history_source_table',
            'unique_history_source_filter_column',
            'unique_history_source_filter_value',
            'unique_history_source_kill_value',
            'unique_history_source_join_enabled',
            'unique_history_source_join_connection',
            'unique_history_source_join_table',
            'unique_history_source_join_local_key',
            'unique_history_source_join_foreign_key',
            'unique_history_source_map_value',
            'unique_history_source_map_eventtime',
            'unique_history_source_map_eventtype',
            'unique_history_source_map_charname',
            'unique_history_source_map_charid',
            'unique_history_source_map_refobjid',
            'unique_history_source_map_area',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function getSettingsArray(): array
    {
        $defaults = [
            'unique_history_vsro_enabled' => false,
            'unique_history_source_join_enabled' => false,
        ];

        $out = [];
        foreach ($this->settingKeys() as $key) {
            $out[$key] = Setting::get($key, $defaults[$key] ?? '');
        }

        return $out;
    }
}
