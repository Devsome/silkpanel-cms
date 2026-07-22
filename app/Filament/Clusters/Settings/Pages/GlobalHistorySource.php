<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Filament\Clusters\Settings;
use App\Filament\Concerns\InteractsWithLockedState;
use App\Helpers\LicenseHelper;
use App\Models\Setting;
use App\Services\GlobalsService;
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
 * Configure the vSRO / custom Global History source.
 *
 * iSRO ships a standard `_LogChatMessage` log table, so the public
 * `/history/globals` page works out of the box there. vSRO has no standard
 * global (yell) log — every server stores it differently — so this page lets
 * an admin point at any table, optionally LEFT JOIN another table (e.g. `_Char`
 * for the character name / avatar), and map the source columns onto the five
 * fields the frontend already expects. The shared query builder lives in
 * {@see GlobalsService::customQuery()} so the frontend needs no changes.
 *
 * @property-read Schema $form
 */
class GlobalHistorySource extends Page
{
    use InteractsWithLockedState;

    protected static ?string $cluster = Settings::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static ?int $navigationSort = 51;

    protected string $view = 'filament.pages.global-history-source';

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
        return __('filament/global-history.navigation');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return config('silkpanel.version') === 'vsro';
    }

    public static function canAccess(): bool
    {
        return config('silkpanel.version') === 'vsro';
    }

    public function isLocked(): bool
    {
        return ! LicenseHelper::isValid();
    }

    public function getLockedDescription(): string
    {
        return __('filament/settings.locked.license_required_global_history');
    }

    public function getTitle(): string
    {
        return __('filament/global-history.title');
    }

    public function mount(): void
    {
        abort_unless(config('silkpanel.version') === 'vsro', 404);

        $this->form->fill($this->getSettingsArray());
        $this->refreshSourceColumns(notify: false);

        if ((bool) ($this->data['global_history_source_join_enabled'] ?? false)) {
            $this->refreshJoinColumns(notify: false);
        }
    }

    public function form(Schema $schema): Schema
    {
        $locked = $this->isLocked();

        return $schema
            ->components([
                Form::make([
                    Section::make(__('filament/global-history.sections.status'))
                        ->description(__('filament/global-history.sections.status_description'))
                        ->schema([
                            Toggle::make('global_history_vsro_enabled')
                                ->label(__('filament/global-history.fields.enabled'))
                                ->helperText(__('filament/global-history.fields.enabled_description'))
                                ->columnSpanFull(),
                        ]),

                    Section::make(__('filament/global-history.sections.source'))
                        ->description(__('filament/global-history.sections.source_description'))
                        ->schema([
                            Select::make('global_history_source_connection')
                                ->label(__('filament/global-history.fields.connection'))
                                ->options(fn(): array => $this->getConnectionOptions())
                                ->searchable()
                                ->preload()
                                ->live()
                                ->required(),

                            Select::make('global_history_source_table')
                                ->label(__('filament/global-history.fields.table'))
                                ->helperText(__('filament/global-history.fields.table_description'))
                                ->options(fn(): array => $this->getTableOptions('global_history_source_connection'))
                                ->searchable()
                                ->preload()
                                ->placeholder('sro_*')
                                ->suffixAction(
                                    Action::make('loadSourceColumns')
                                        ->label(__('filament/global-history.actions.load_columns'))
                                        ->icon('heroicon-o-arrow-path')
                                        ->action(fn() => $this->refreshSourceColumns())
                                )
                                ->required(),

                            Select::make('global_history_source_filter_column')
                                ->label(__('filament/global-history.fields.filter_column'))
                                ->helperText(__('filament/global-history.fields.filter_column_description'))
                                ->options(fn(): array => $this->sourceTableColumns)
                                ->searchable()
                                ->placeholder('—'),

                            TextInput::make('global_history_source_filter_value')
                                ->label(__('filament/global-history.fields.filter_value'))
                                ->helperText(__('filament/global-history.fields.filter_value_description'))
                                ->placeholder('[YELL]'),

                            TextInput::make('global_history_source_cache_ttl')
                                ->label(__('filament/global-history.fields.cache_ttl'))
                                ->helperText(__('filament/global-history.fields.cache_ttl_description'))
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(86400)
                                ->default(60)
                                ->suffix('s'),
                        ])
                        ->columns(2),

                    Section::make(__('filament/global-history.sections.join'))
                        ->description(__('filament/global-history.sections.join_description'))
                        ->schema([
                            Toggle::make('global_history_source_join_enabled')
                                ->label(__('filament/global-history.fields.join_enabled'))
                                ->helperText(__('filament/global-history.fields.join_enabled_description'))
                                ->columnSpanFull()
                                ->live(),

                            Select::make('global_history_source_join_connection')
                                ->label(__('filament/global-history.fields.join_connection'))
                                ->options(fn(): array => $this->getConnectionOptions())
                                ->searchable()
                                ->preload()
                                ->live()
                                ->visible(fn(): bool => (bool) ($this->data['global_history_source_join_enabled'] ?? false)),

                            Select::make('global_history_source_join_table')
                                ->label(__('filament/global-history.fields.join_table'))
                                ->helperText(__('filament/global-history.fields.join_table_description'))
                                ->options(fn(): array => $this->getTableOptions('global_history_source_join_connection'))
                                ->searchable()
                                ->preload()
                                ->placeholder('_Char')
                                ->suffixAction(
                                    Action::make('loadJoinColumns')
                                        ->label(__('filament/global-history.actions.load_columns'))
                                        ->icon('heroicon-o-arrow-path')
                                        ->action(fn() => $this->refreshJoinColumns())
                                )
                                ->visible(fn(): bool => (bool) ($this->data['global_history_source_join_enabled'] ?? false)),

                            Select::make('global_history_source_join_local_key')
                                ->label(__('filament/global-history.fields.join_local_key'))
                                ->helperText(__('filament/global-history.fields.join_local_key_description'))
                                ->options(fn(): array => $this->sourceTableColumns)
                                ->searchable()
                                ->visible(fn(): bool => (bool) ($this->data['global_history_source_join_enabled'] ?? false)),

                            Select::make('global_history_source_join_foreign_key')
                                ->label(__('filament/global-history.fields.join_foreign_key'))
                                ->helperText(__('filament/global-history.fields.join_foreign_key_description'))
                                ->options(fn(): array => $this->joinTableColumns)
                                ->searchable()
                                ->visible(fn(): bool => (bool) ($this->data['global_history_source_join_enabled'] ?? false)),
                        ])
                        ->columns(2),

                    Section::make(__('filament/global-history.sections.mapping'))
                        ->description(__('filament/global-history.sections.mapping_description'))
                        ->schema([
                            Select::make('global_history_source_map_comment')
                                ->label(__('filament/global-history.fields.map_comment'))
                                ->helperText(__('filament/global-history.fields.map_comment_description'))
                                ->options(fn(): array => $this->getMappingOptions())
                                ->searchable()
                                ->required(),

                            Select::make('global_history_source_map_eventtime')
                                ->label(__('filament/global-history.fields.map_eventtime'))
                                ->helperText(__('filament/global-history.fields.map_eventtime_description'))
                                ->options(fn(): array => $this->getMappingOptions())
                                ->searchable()
                                ->required(),

                            Select::make('global_history_source_map_charname')
                                ->label(__('filament/global-history.fields.map_charname'))
                                ->helperText(__('filament/global-history.fields.map_charname_description'))
                                ->options(fn(): array => $this->getMappingOptions())
                                ->searchable()
                                ->placeholder('—'),

                            Select::make('global_history_source_map_charid')
                                ->label(__('filament/global-history.fields.map_charid'))
                                ->helperText(__('filament/global-history.fields.map_charid_description'))
                                ->options(fn(): array => $this->getMappingOptions())
                                ->searchable()
                                ->placeholder('—'),

                            Select::make('global_history_source_map_refobjid')
                                ->label(__('filament/global-history.fields.map_refobjid'))
                                ->helperText(__('filament/global-history.fields.map_refobjid_description'))
                                ->options(fn(): array => $this->getMappingOptions())
                                ->searchable()
                                ->placeholder('—'),
                        ])
                        ->columns(2),

                    ActionsComponent::make([
                        Action::make('testGlobalHistorySource')
                            ->label(__('filament/global-history.actions.test'))
                            ->icon('heroicon-o-beaker')
                            ->color('gray')
                            ->action(fn() => $this->testGlobalHistorySource()),
                    ])->visible(! $locked),
                ])
                    ->disabled($locked)
                    ->livewireSubmitHandler($locked ? null : 'save')
                    ->footer($locked ? [] : [
                        ActionsComponent::make([
                            Action::make('save')
                                ->label(__('filament/global-history.actions.save'))
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

        $keys = [
            'global_history_vsro_enabled',
            'global_history_source_connection',
            'global_history_source_table',
            'global_history_source_filter_column',
            'global_history_source_filter_value',
            'global_history_source_cache_ttl',
            'global_history_source_join_enabled',
            'global_history_source_join_connection',
            'global_history_source_join_table',
            'global_history_source_join_local_key',
            'global_history_source_join_foreign_key',
            'global_history_source_map_comment',
            'global_history_source_map_eventtime',
            'global_history_source_map_charname',
            'global_history_source_map_charid',
            'global_history_source_map_refobjid',
        ];

        foreach ($keys as $key) {
            if (array_key_exists($key, $data)) {
                Setting::set($key, $data[$key], null, null, null);
            }
        }

        Notification::make()
            ->success()
            ->title(__('filament/global-history.notifications.saved_title'))
            ->body(__('filament/global-history.notifications.saved_message'))
            ->send();
    }

    public function testGlobalHistorySource(): void
    {
        if ($this->isLocked()) {
            return;
        }

        $this->previewError = null;
        $this->previewData = [];
        $this->previewColumns = [];

        try {
            $data = $this->data ?? [];

            // Specific, actionable guidance before delegating to the builder.
            if (trim((string) ($data['global_history_source_connection'] ?? '')) === ''
                || trim((string) ($data['global_history_source_table'] ?? '')) === '') {
                throw new \RuntimeException(__('filament/global-history.preview.missing_source'));
            }

            if (trim((string) ($data['global_history_source_map_comment'] ?? '')) === ''
                || trim((string) ($data['global_history_source_map_eventtime'] ?? '')) === '') {
                throw new \RuntimeException(__('filament/global-history.preview.missing_required_map'));
            }

            // Preview the *current* (possibly unsaved) form state via the shared
            // builder. The test works regardless of the public enable toggle so
            // you can validate the mapping before going live.
            $override = array_merge($data, ['global_history_vsro_enabled' => true]);
            $query = app(GlobalsService::class)->customQuery(configOverride: $override);

            if ($query === null) {
                throw new \RuntimeException(__('filament/global-history.preview.not_configured'));
            }

            $rows = $query->limit(10)->get();

            $this->previewData = $rows->map(fn($row) => (array) $row)->all();
            $this->previewColumns = [
                ['column' => 'Comment', 'label' => 'Comment'],
                ['column' => 'CharName', 'label' => 'CharName'],
                ['column' => 'EventTime', 'label' => 'EventTime'],
                ['column' => 'CharID', 'label' => 'CharID'],
                ['column' => 'RefObjID', 'label' => 'RefObjID'],
            ];
        } catch (Throwable $e) {
            $this->previewError = $e->getMessage();
        }
    }

    public function refreshSourceColumns(bool $notify = true): void
    {
        $this->sourceTableColumns = $this->loadColumns(
            (string) ($this->data['global_history_source_connection'] ?? config('database.default')),
            trim((string) ($this->data['global_history_source_table'] ?? '')),
            $notify,
        );
    }

    public function refreshJoinColumns(bool $notify = true): void
    {
        $this->joinTableColumns = $this->loadColumns(
            (string) ($this->data['global_history_source_join_connection'] ?? config('database.default')),
            trim((string) ($this->data['global_history_source_join_table'] ?? '')),
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
                    ->title(__('filament/global-history.notifications.missing_table_title'))
                    ->body(__('filament/global-history.notifications.missing_table_message'))
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
                    ->title(__('filament/global-history.notifications.columns_error_title'))
                    ->body(__('filament/global-history.notifications.no_columns_message'))
                    ->send();
            } elseif ($notify) {
                Notification::make()
                    ->success()
                    ->title(__('filament/global-history.notifications.columns_loaded_title'))
                    ->body(__('filament/global-history.notifications.columns_loaded_message', ['count' => count($options)]))
                    ->send();
            }

            return $options;
        } catch (Throwable $e) {
            if ($notify) {
                Notification::make()
                    ->danger()
                    ->title(__('filament/global-history.notifications.columns_error_title'))
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
            $options[__('filament/global-history.mapping.base_group')] = $base;
        }

        if ((bool) ($this->data['global_history_source_join_enabled'] ?? false)) {
            $join = [];
            foreach (array_keys($this->joinTableColumns) as $column) {
                $join['join:' . $column] = $column;
            }

            if ($join !== []) {
                $options[__('filament/global-history.mapping.join_group')] = $join;
            }
        }

        return $options;
    }

    /**
     * @return array<string, mixed>
     */
    private function getSettingsArray(): array
    {
        return [
            'global_history_vsro_enabled'          => Setting::get('global_history_vsro_enabled', false),
            'global_history_source_connection'     => Setting::get('global_history_source_connection', ''),
            'global_history_source_table'          => Setting::get('global_history_source_table', ''),
            'global_history_source_filter_column'  => Setting::get('global_history_source_filter_column', ''),
            'global_history_source_filter_value'   => Setting::get('global_history_source_filter_value', ''),
            'global_history_source_cache_ttl'      => Setting::get('global_history_source_cache_ttl', 60),
            'global_history_source_join_enabled'   => Setting::get('global_history_source_join_enabled', false),
            'global_history_source_join_connection' => Setting::get('global_history_source_join_connection', ''),
            'global_history_source_join_table'     => Setting::get('global_history_source_join_table', ''),
            'global_history_source_join_local_key' => Setting::get('global_history_source_join_local_key', ''),
            'global_history_source_join_foreign_key' => Setting::get('global_history_source_join_foreign_key', ''),
            'global_history_source_map_comment'    => Setting::get('global_history_source_map_comment', ''),
            'global_history_source_map_eventtime'  => Setting::get('global_history_source_map_eventtime', ''),
            'global_history_source_map_charname'   => Setting::get('global_history_source_map_charname', ''),
            'global_history_source_map_charid'     => Setting::get('global_history_source_map_charid', ''),
            'global_history_source_map_refobjid'   => Setting::get('global_history_source_map_refobjid', ''),
        ];
    }
}
