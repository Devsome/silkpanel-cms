<?php

namespace App\Filament\Pages;

use App\Enums\DatabaseNameEnums;
use App\Enums\UsergroupRoleEnums;
use App\Filament\Concerns\InteractsWithLockedState;
use App\Helpers\LicenseHelper;
use App\Models\ProcedureLog;
use App\Models\ProcedureMapping;
use App\Services\ProcedureManager;
use BackedEnum;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\CodeEditor;
use Filament\Forms\Components\CodeEditor\Enums\Language;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;
use Throwable;

/**
 * @property-read Schema $form
 */
class CustomProcedures extends Page implements HasTable
{
    use InteractsWithLockedState;
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCommandLine;

    protected static string|\UnitEnum|null $navigationGroup = 'Configuration';

    protected static ?int $navigationSort = 58;

    protected string $view = 'filament.pages.custom-procedures';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    /** @var array<string, mixed>|null */
    public ?array $testResult = null;

    private ?ProcedureManager $procedureManager = null;

    public static function getNavigationLabel(): string
    {
        return self::t('filament/pages.custom_procedures.navigation');
    }

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user?->hasRole(UsergroupRoleEnums::ADMIN->value) ?? false;
    }

    public function isLocked(): bool
    {
        return ! LicenseHelper::isValid();
    }

    public function getLockedDescription(): string
    {
        return __('filament/settings.locked.license_required_custom_procedures');
    }

    public function boot(ProcedureManager $procedureManager): void
    {
        $this->procedureManager = $procedureManager;
    }

    public function mount(): void
    {
        $this->procedureManager()->ensureActionMappingsExist();

        $actionKey = array_key_first($this->getActionOptions());
        if ($actionKey === null) {
            $this->data = [
                'selected_action' => null,
                'test_input_values' => [],
            ];
            $this->form->fill($this->data);

            return;
        }

        $this->loadActionMapping($actionKey);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([
                    Section::make(self::t('filament/pages.custom_procedures.sections.action_mapping'))
                        ->description(self::t('filament/pages.custom_procedures.sections.action_mapping_description'))
                        ->schema([
                            Select::make('selected_action')
                                ->label(self::t('filament/pages.custom_procedures.fields.action'))
                                ->helperText('If you are missing some actions, please contact us in the discord support channel.')
                                ->options($this->getActionOptions())
                                ->required()
                                ->searchable()
                                ->live()
                                ->afterStateUpdated(fn(?string $state) => $this->loadActionMapping($state)),

                            TextInput::make('action_label')
                                ->label(self::t('filament/pages.custom_procedures.fields.action_label'))
                                ->required(),

                            Toggle::make('is_active')
                                ->label(self::t('filament/pages.custom_procedures.fields.is_active')),

                            Toggle::make('use_fallback')
                                ->label(self::t('filament/pages.custom_procedures.fields.use_fallback'))
                                ->default(true),

                            Select::make('database_connection')
                                ->label(self::t('filament/pages.custom_procedures.fields.database_connection'))
                                ->options($this->connectionOptions())
                                ->default(DatabaseNameEnums::SRO_SHARD->value)
                                ->live()
                                ->afterStateUpdated(function (?string $state, callable $set): void {
                                    $set('procedure_picker', null);
                                    $this->syncProcedurePreview($set, (string) $state, null);
                                })
                                ->required(),

                            Select::make('procedure_picker')
                                ->label(self::t('filament/pages.custom_procedures.fields.procedure_picker'))
                                ->helperText(self::t('filament/pages.custom_procedures.fields.procedure_picker_help'))
                                ->options(fn(callable $get): array => $this->procedureNameOptions((string) $get('database_connection')))
                                ->live()
                                ->searchable()
                                ->preload()
                                ->dehydrated(false)
                                ->afterStateHydrated(function (?string $state, callable $set): void {
                                    if (filled($state)) {
                                        $procedureName = $this->extractProcedureName($state);
                                        $set('procedure_name', $procedureName);
                                        $this->syncProcedurePreview($set, (string) ($this->data['database_connection'] ?? DatabaseNameEnums::SRO_SHARD->value), $procedureName);
                                    }
                                })
                                ->afterStateUpdated(function (?string $state, callable $set): void {
                                    $procedureName = $this->extractProcedureName($state);
                                    $set('procedure_name', $procedureName);
                                    $this->syncProcedurePreview($set, (string) ($this->data['database_connection'] ?? DatabaseNameEnums::SRO_SHARD->value), $procedureName);
                                }),

                            TextInput::make('procedure_name')
                                ->label(self::t('filament/pages.custom_procedures.fields.procedure_name'))
                                ->placeholder(self::t('filament/pages.custom_procedures.fields.procedure_name_placeholder'))
                                ->readOnly(fn(callable $get): bool => filled((string) ($get('procedure_picker') ?? '')))
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (?string $state, callable $set, callable $get): void {
                                    if (filled((string) ($get('procedure_picker') ?? ''))) {
                                        return;
                                    }

                                    $this->syncProcedurePreview(
                                        $set,
                                        (string) ($get('database_connection') ?? DatabaseNameEnums::SRO_SHARD->value),
                                        (string) $state,
                                    );
                                })
                                ->required(fn(callable $get): bool => (bool) $get('is_active')),
                        ])
                        ->columns(2),

                    Section::make(self::t('filament/pages.custom_procedures.sections.parameter_mapping'))
                        ->description(self::t('filament/pages.custom_procedures.sections.parameter_mapping_description'))
                        ->schema([
                            Repeater::make('parameter_map')
                                ->label(self::t('filament/pages.custom_procedures.fields.mapped_parameters'))
                                ->schema([
                                    Select::make('laravel_key')
                                        ->label(self::t('filament/pages.custom_procedures.fields.laravel_key'))
                                        ->options(fn(): array => $this->fixedParameterKeyOptions((string) ($this->data['selected_action'] ?? '')))
                                        ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                        ->live()
                                        ->afterStateUpdated(function (?string $state, callable $set): void {
                                            $actionKey = (string) ($this->data['selected_action'] ?? '');
                                            $defaults = $this->defaultParameterConfig($actionKey, (string) $state);

                                            if ($defaults === null) {
                                                return;
                                            }

                                            $set('procedure_param', (string) ($defaults['procedure_param'] ?? ''));
                                            $set('position', (int) ($defaults['position'] ?? 1));
                                        })
                                        ->required(),
                                    TextInput::make('procedure_param')
                                        ->label(self::t('filament/pages.custom_procedures.fields.procedure_param'))
                                        ->datalist(fn(callable $get): array => array_values($this->procedureParameterOptions(
                                            connection: (string) ($get('../../database_connection') ?? $this->data['database_connection'] ?? ''),
                                            procedureName: (string) ($get('../../procedure_name') ?? $this->data['procedure_name'] ?? ''),
                                            current: (string) ($get('procedure_param') ?? ''),
                                        )))
                                        ->placeholder(self::t('filament/pages.custom_procedures.fields.procedure_param_placeholder'))
                                        ->required(),
                                    TextInput::make('position')
                                        ->label(self::t('filament/pages.custom_procedures.fields.position'))
                                        ->numeric()
                                        ->default(1)
                                        ->required(),
                                ])
                                ->columns(3)
                                ->default([])
                                ->addable(true)
                                ->deletable(true)
                                ->reorderable(false),
                        ]),

                    Section::make(self::t('filament/pages.custom_procedures.sections.additional_parameters'))
                        ->description(self::t('filament/pages.custom_procedures.sections.additional_parameters_description'))
                        ->schema([
                            Repeater::make('extra_parameter_map')
                                ->label(self::t('filament/pages.custom_procedures.fields.extra_mapped_parameters'))
                                ->schema([
                                    TextInput::make('payload_key')
                                        ->label(self::t('filament/pages.custom_procedures.fields.payload_key'))
                                        ->placeholder(self::t('filament/pages.custom_procedures.fields.payload_key_placeholder'))
                                        ->required(),
                                    TextInput::make('procedure_param')
                                        ->label(self::t('filament/pages.custom_procedures.fields.procedure_param'))
                                        ->datalist(fn(callable $get): array => array_values($this->procedureParameterOptions(
                                            connection: (string) ($get('../../database_connection') ?? $this->data['database_connection'] ?? ''),
                                            procedureName: (string) ($get('../../procedure_name') ?? $this->data['procedure_name'] ?? ''),
                                            current: (string) ($get('procedure_param') ?? ''),
                                        )))
                                        ->placeholder(self::t('filament/pages.custom_procedures.fields.extra_procedure_param_placeholder'))
                                        ->required(),
                                    TextInput::make('position')
                                        ->label(self::t('filament/pages.custom_procedures.fields.position'))
                                        ->numeric()
                                        ->default(100)
                                        ->required(),
                                    TextInput::make('default_value')
                                        ->label(self::t('filament/pages.custom_procedures.fields.default_value'))
                                        ->placeholder(self::t('filament/pages.custom_procedures.fields.default_value_placeholder')),
                                ])
                                ->columns(4)
                                ->default([])
                                ->addActionLabel(self::t('filament/pages.custom_procedures.actions.add_extra_parameter')),
                        ]),

                    Section::make(self::t('filament/pages.custom_procedures.sections.procedure_preview'))
                        ->description(self::t('filament/pages.custom_procedures.sections.procedure_preview_description'))
                        ->schema([
                            CodeEditor::make('procedure_signature')
                                ->label(self::t('filament/pages.custom_procedures.fields.procedure_signature'))
                                ->language(Language::Sql)
                                ->columnSpanFull()
                                ->dehydrated(false),
                        ]),

                    Section::make(self::t('filament/pages.custom_procedures.sections.testing'))
                        ->description(self::t('filament/pages.custom_procedures.sections.testing_description'))
                        ->collapsible()
                        ->collapsed()
                        ->schema([
                            Repeater::make('test_input_values')
                                ->label(self::t('filament/pages.custom_procedures.fields.test_values'))
                                ->schema([
                                    TextInput::make('payload_key')
                                        ->label(self::t('filament/pages.custom_procedures.fields.payload_key'))
                                        ->readOnly()
                                        ->required(),
                                    TextInput::make('value')
                                        ->label(self::t('filament/pages.custom_procedures.fields.test_value'))
                                        ->placeholder(self::t('filament/pages.custom_procedures.fields.test_value_placeholder')),
                                ])
                                ->columns(2)
                                ->default([])
                                ->addable(false)
                                ->deletable(false)
                                ->reorderable(false),
                        ]),
                ])
                    ->disabled($this->isLocked()),
            ])
            ->statePath('data');
    }

    public function saveMapping(): void
    {
        if ($this->isLocked()) {
            return;
        }

        $data = $this->form->getState();

        $selectedAction = (string) ($data['selected_action'] ?? '');
        $filteredFixedMap = $this->filterFixedParameterMap(
            actionKey: $selectedAction,
            incomingMap: $data['parameter_map'] ?? [],
        );

        $data['parameter_map'] = $this->composeParameterMap(
            actionKey: $selectedAction,
            incomingFixedMap: $filteredFixedMap,
            incomingExtraMap: $data['extra_parameter_map'] ?? [],
        );

        $validator = Validator::make($data, [
            'selected_action' => ['required', 'string'],
            'action_label' => ['required', 'string', 'max:255'],
            'procedure_name' => ['nullable', 'string', 'max:255'],
            'database_connection' => ['required', 'string', 'max:255'],
            'is_active' => ['required', 'boolean'],
            'use_fallback' => ['required', 'boolean'],
            'parameter_map' => ['array'],
            'parameter_map.*.laravel_key' => ['required', 'string', 'max:255'],
            'parameter_map.*.procedure_param' => ['required', 'string', 'max:255'],
            'parameter_map.*.position' => ['required', 'integer', 'min:1'],
            'extra_parameter_map' => ['array'],
            'extra_parameter_map.*.payload_key' => ['required', 'string', 'max:255'],
            'extra_parameter_map.*.procedure_param' => ['required', 'string', 'max:255'],
            'extra_parameter_map.*.position' => ['required', 'integer', 'min:1'],
            'extra_parameter_map.*.default_value' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            Notification::make()
                ->danger()
                ->title(self::t('filament/pages.custom_procedures.notifications.validation_failed_title'))
                ->body($validator->errors()->first())
                ->send();

            return;
        }

        ProcedureMapping::query()->updateOrCreate(
            ['action' => $data['selected_action']],
            [
                'action_label' => $data['action_label'],
                'procedure_name' => $data['procedure_name'] ?: null,
                'database_connection' => $data['database_connection'],
                'is_active' => (bool) $data['is_active'],
                'use_fallback' => (bool) $data['use_fallback'],
                'parameter_map' => $data['parameter_map'] ?? [],
            ]
        );

        Notification::make()
            ->success()
            ->title(self::t('filament/pages.custom_procedures.notifications.mapping_saved_title'))
            ->body(self::t('filament/pages.custom_procedures.notifications.mapping_saved_body'))
            ->send();
    }

    public function testProcedure(): void
    {
        if ($this->isLocked()) {
            return;
        }

        $data = $this->form->getState();

        if (empty($data['selected_action'])) {
            Notification::make()
                ->danger()
                ->title(self::t('filament/pages.custom_procedures.notifications.no_action_title'))
                ->send();

            return;
        }

        $payload = [];
        $testInputValues = $data['test_input_values'] ?? [];

        foreach ($testInputValues as $row) {
            $key = trim((string) ($row['payload_key'] ?? ''));
            if ($key === '') {
                continue;
            }

            $payload[$key] = $this->castTestValue((string) ($row['value'] ?? ''));
        }

        $selectedAction = (string) ($data['selected_action'] ?? '');
        $filteredFixedMap = $this->filterFixedParameterMap(
            actionKey: $selectedAction,
            incomingMap: $data['parameter_map'] ?? [],
        );

        $normalizedMap = $this->composeParameterMap(
            actionKey: $selectedAction,
            incomingFixedMap: $filteredFixedMap,
            incomingExtraMap: $data['extra_parameter_map'] ?? [],
        );

        // Sync current form state into the mapping so "Run Test" works without manual save first.
        ProcedureMapping::query()->updateOrCreate(
            ['action' => $selectedAction],
            [
                'action_label' => (string) ($data['action_label'] ?? $selectedAction),
                'procedure_name' => (string) ($data['procedure_name'] ?? ''),
                'database_connection' => (string) ($data['database_connection'] ?? DatabaseNameEnums::SRO_SHARD->value),
                'is_active' => (bool) ($data['is_active'] ?? false),
                'use_fallback' => (bool) ($data['use_fallback'] ?? true),
                'parameter_map' => $normalizedMap,
            ]
        );

        $result = $this->procedureManager()->test($selectedAction, $payload);

        $this->testResult = $result;

        $message = $this->humanizeProcedureMessage($result['message'] ?? null);

        if ($result['success']) {
            Notification::make()
                ->success()
                ->title(self::t('filament/pages.custom_procedures.notifications.test_success_title'))
                ->body($message)
                ->send();

            return;
        }

        Notification::make()
            ->danger()
            ->title(self::t('filament/pages.custom_procedures.notifications.test_failed_title'))
            ->body($message)
            ->send();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(ProcedureLog::query()->latest())
            ->columns([
                TextColumn::make('action')
                    ->label(self::t('filament/pages.custom_procedures.table.action'))
                    ->searchable(),
                TextColumn::make('procedure_name')
                    ->label(self::t('filament/pages.custom_procedures.table.procedure'))
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('database_connection')
                    ->label(self::t('filament/pages.custom_procedures.table.connection'))
                    ->badge(),
                TextColumn::make('success')
                    ->label(self::t('filament/pages.custom_procedures.table.status'))
                    ->badge()
                    ->formatStateUsing(fn(bool $state): string => $state ? self::t('filament/pages.custom_procedures.table.success') : self::t('filament/pages.custom_procedures.table.failed'))
                    ->color(fn(bool $state): string => $state ? 'success' : 'danger'),
                TextColumn::make('fallback_used')
                    ->label(self::t('filament/pages.custom_procedures.table.fallback'))
                    ->badge()
                    ->formatStateUsing(fn(bool $state): string => $state ? self::t('filament/pages.custom_procedures.table.fallback_used') : self::t('filament/pages.custom_procedures.table.fallback_no')),
                TextColumn::make('error_message')
                    ->label(self::t('filament/pages.custom_procedures.table.error'))
                    ->limit(90)
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label(self::t('filament/pages.custom_procedures.table.executed'))
                    ->dateTime(),
            ])
            ->filters([
                SelectFilter::make('action')
                    ->options($this->getActionOptions()),
                SelectFilter::make('success')
                    ->options([
                        '1' => 'Success',
                        '0' => 'Failed',
                    ])
                    ->query(
                        fn($query, array $data) =>
                        isset($data['value']) && $data['value'] !== ''
                            ? $query->where('success', (bool) $data['value'])
                            : $query
                    ),
            ]);
    }

    private function loadActionMapping(?string $actionKey): void
    {
        if ($actionKey === null || $actionKey === '') {
            return;
        }

        $mapping = $this->procedureManager()->getOrCreateMapping($actionKey);
        $actions = $this->procedureManager()->getActions();
        $actionMeta = $actions[$actionKey] ?? null;

        $storedParameterMap = $mapping->parameter_map ?? ($actionMeta['default_parameter_map'] ?? []);

        $normalizedMap = $this->normalizeParameterMap(
            actionKey: $actionKey,
            incomingMap: $storedParameterMap,
        );

        $extraMap = $this->extractExtraParameterMap(
            actionKey: $actionKey,
            incomingMap: $storedParameterMap,
        );

        [$signature, $definitionPreview] = $this->resolveProcedurePreview(
            (string) ($mapping->database_connection ?: DatabaseNameEnums::SRO_SHARD->value),
            (string) ($mapping->procedure_name ?? ''),
        );

        $databaseConnection = (string) ($mapping->database_connection ?: DatabaseNameEnums::SRO_SHARD->value);
        $procedureName = (string) ($mapping->procedure_name ?? '');

        $this->data = [
            ...($this->data ?? []),
            'selected_action' => $actionKey,
            'action_label' => $mapping->action_label ?: ($actionMeta['label'] ?? $actionKey),
            'procedure_name' => $procedureName,
            'procedure_picker' => $this->resolveProcedurePickerValue($databaseConnection, $procedureName),
            'database_connection' => $databaseConnection,
            'parameter_map' => $normalizedMap,
            'extra_parameter_map' => $extraMap,
            'is_active' => (bool) $mapping->is_active,
            'use_fallback' => (bool) $mapping->use_fallback,
            'test_input_values' => $this->buildTestInputValues($storedParameterMap),
            'procedure_signature' => $signature,
        ];

        $this->form->fill($this->data);
    }

    /**
     * @return array<string, string>
     */
    private function getActionOptions(): array
    {
        $options = [];

        foreach ($this->procedureManager()->getActions() as $action) {
            $options[$action['key']] = $action['label'];
        }

        return $options;
    }

    /**
     * @return array<string, string>
     */
    private function connectionOptions(): array
    {
        return [
            DatabaseNameEnums::SRO_SHARD->value => self::t('filament/pages.custom_procedures.connections.sro_shard'),
            DatabaseNameEnums::SRO_ACCOUNT->value => self::t('filament/pages.custom_procedures.connections.sro_account'),
            DatabaseNameEnums::SRO_LOG->value => self::t('filament/pages.custom_procedures.connections.sro_log'),
            DatabaseNameEnums::SRO_CUSTOM->value => self::t('filament/pages.custom_procedures.connections.sro_custom'),
            DatabaseNameEnums::SRO_PORTAL->value => self::t('filament/pages.custom_procedures.connections.sro_portal'),
            DatabaseNameEnums::MYSQL->value => self::t('filament/pages.custom_procedures.connections.mysql'),
        ];
    }

    /**
     * @return array<string, string>
     */
    private function procedureNameOptions(string $connection): array
    {
        if ($connection === '') {
            return [];
        }

        try {
            $driver = DB::connection($connection)->getDriverName();

            if ($driver === 'sqlsrv') {
                $rows = DB::connection($connection)->select(
                    'SELECT s.name AS schema_name, p.name AS procedure_name
                     FROM sys.procedures p
                     INNER JOIN sys.schemas s ON s.schema_id = p.schema_id
                     ORDER BY s.name, p.name'
                );

                $options = [];
                foreach ($rows as $row) {
                    $schema = (string) ($row->schema_name ?? 'dbo');
                    $name = (string) ($row->procedure_name ?? '');
                    if ($name === '') {
                        continue;
                    }

                    $qualified = sprintf('[%s].[%s]', $schema, $name);
                    $options[$qualified] = $qualified;
                }

                return $options;
            }

            if ($driver === 'mysql') {
                $rows = DB::connection($connection)->select(
                    'SELECT ROUTINE_NAME AS procedure_name
                     FROM information_schema.routines
                     WHERE ROUTINE_TYPE = ? AND ROUTINE_SCHEMA = DATABASE()
                     ORDER BY ROUTINE_NAME',
                    ['PROCEDURE']
                );

                $options = [];
                foreach ($rows as $row) {
                    $name = (string) ($row->procedure_name ?? '');
                    if ($name !== '') {
                        $options[$name] = $name;
                    }
                }

                return $options;
            }
        } catch (Throwable) {
            return [];
        }

        return [];
    }

    private function procedureManager(): ProcedureManager
    {
        return $this->procedureManager ??= app(ProcedureManager::class);
    }

    private function extractProcedureName(?string $qualifiedName): string
    {
        $value = trim((string) $qualifiedName);
        if ($value === '') {
            return '';
        }

        $unwrapped = str_replace(['[', ']'], '', $value);
        $parts = explode('.', $unwrapped);

        return trim((string) end($parts));
    }

    /**
     * @return array<string, string>
     */
    private function fixedParameterKeyOptions(string $actionKey): array
    {
        $actions = $this->procedureManager()->getActions();
        $defaultMap = $actions[$actionKey]['default_parameter_map'] ?? [];

        $options = [];
        foreach ($defaultMap as $row) {
            $key = (string) ($row['laravel_key'] ?? '');
            if ($key !== '') {
                $options[$key] = $key;
            }
        }

        return $options;
    }

    /**
     * @return array{procedure_param?: string, position?: int}|null
     */
    private function defaultParameterConfig(string $actionKey, string $key): ?array
    {
        if ($key === '') {
            return null;
        }

        $actions = $this->procedureManager()->getActions();
        $defaultMap = $actions[$actionKey]['default_parameter_map'] ?? [];

        foreach ($defaultMap as $row) {
            if ((string) ($row['laravel_key'] ?? '') === $key) {
                return $row;
            }
        }

        return null;
    }

    /**
     * @param array<int, array<string, mixed>> $incomingMap
     * @return array<int, array{laravel_key: string, procedure_param: string, position: int, default_value?: string|null}>
     */
    private function filterFixedParameterMap(string $actionKey, array $incomingMap): array
    {
        $allowedKeys = $this->fixedParameterKeyOptions($actionKey);
        if ($allowedKeys === []) {
            return [];
        }

        $filtered = [];
        $seen = [];

        foreach ($incomingMap as $row) {
            $key = (string) ($row['laravel_key'] ?? '');
            if ($key === '' || ! isset($allowedKeys[$key]) || isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;

            $filtered[] = [
                'laravel_key' => $key,
                'procedure_param' => (string) ($row['procedure_param'] ?? ''),
                'position' => max(1, (int) ($row['position'] ?? 1)),
            ];
        }

        return $filtered;
    }

    private function resolveProcedurePickerValue(string $connection, string $procedureName): ?string
    {
        if ($procedureName === '') {
            return null;
        }

        $options = $this->procedureNameOptions($connection);
        if (isset($options[$procedureName])) {
            return $procedureName;
        }

        $normalized = strtolower(str_replace(['[', ']'], '', $procedureName));

        foreach (array_keys($options) as $optionKey) {
            $optionNormalized = strtolower(str_replace(['[', ']'], '', (string) $optionKey));

            if ($optionNormalized === $normalized) {
                return (string) $optionKey;
            }

            $parts = explode('.', $optionNormalized);
            $optionShortName = (string) end($parts);

            if ($optionShortName === $normalized) {
                return (string) $optionKey;
            }
        }

        return null;
    }

    /**
     * @return array<string, string>
     */
    private function procedureParameterOptions(string $connection, string $procedureName, ?string $current = null): array
    {
        $options = [];

        if ($connection !== '' && $procedureName !== '') {
            try {
                $driver = DB::connection($connection)->getDriverName();

                if ($driver === 'sqlsrv') {
                    $normalizedInput = str_replace(['[', ']'], '', $procedureName);

                    $rows = DB::connection($connection)->select(
                        'SELECT TOP 1 p.object_id
                         FROM sys.procedures p
                         INNER JOIN sys.schemas s ON s.schema_id = p.schema_id
                         WHERE p.name = ? OR (s.name + \'\.\' + p.name) = ?
                         ORDER BY CASE WHEN (s.name + \'\.\' + p.name) = ? THEN 0 ELSE 1 END, s.name, p.name',
                        [$normalizedInput, $normalizedInput, $normalizedInput]
                    );

                    if ($rows !== []) {
                        $objectId = (int) ($rows[0]->object_id ?? 0);

                        if ($objectId > 0) {
                            $params = DB::connection($connection)->select(
                                'SELECT prm.name AS parameter_name
                                 FROM sys.parameters prm
                                 WHERE prm.object_id = ?
                                 ORDER BY prm.parameter_id',
                                [$objectId]
                            );

                            foreach ($params as $param) {
                                $name = trim((string) ($param->parameter_name ?? ''));
                                if ($name === '') {
                                    continue;
                                }

                                $options[$name] = $name;
                            }
                        }
                    }
                }
            } catch (Throwable) {
                // Keep options empty if metadata cannot be loaded.
            }
        }

        $currentValue = trim((string) $current);
        if ($currentValue !== '' && ! isset($options[$currentValue])) {
            $options[$currentValue] = $currentValue;
        }

        return $options;
    }

    /**
     * @param array<int, array<string, mixed>> $incomingFixedMap
     * @param array<int, array<string, mixed>> $incomingExtraMap
     * @return array<int, array{laravel_key: string, procedure_param: string, position: int, default_value?: string|null}>
     */
    private function composeParameterMap(string $actionKey, array $incomingFixedMap, array $incomingExtraMap): array
    {
        $fixed = $this->normalizeParameterMap($actionKey, $incomingFixedMap);

        $extras = [];
        foreach ($incomingExtraMap as $row) {
            $payloadKey = trim((string) ($row['payload_key'] ?? ''));
            $procedureParam = trim((string) ($row['procedure_param'] ?? ''));

            if ($payloadKey === '' || $procedureParam === '') {
                continue;
            }

            $extras[] = [
                'laravel_key' => $payloadKey,
                'procedure_param' => $procedureParam,
                'position' => max(1, (int) ($row['position'] ?? 100)),
                'default_value' => isset($row['default_value']) && $row['default_value'] !== ''
                    ? (string) $row['default_value']
                    : null,
            ];
        }

        return [...$fixed, ...$extras];
    }

    /**
     * @param array<int, array<string, mixed>> $incomingMap
     * @return array<int, array{payload_key: string, procedure_param: string, position: int, default_value?: string|null}>
     */
    private function extractExtraParameterMap(string $actionKey, array $incomingMap): array
    {
        $actions = $this->procedureManager()->getActions();
        $defaultMap = $actions[$actionKey]['default_parameter_map'] ?? [];
        $fixedKeys = [];

        foreach ($defaultMap as $row) {
            $key = (string) ($row['laravel_key'] ?? '');
            if ($key !== '') {
                $fixedKeys[$key] = true;
            }
        }

        $extras = [];
        foreach ($incomingMap as $row) {
            $key = (string) ($row['laravel_key'] ?? '');
            if ($key === '' || isset($fixedKeys[$key])) {
                continue;
            }

            $extras[] = [
                'payload_key' => $key,
                'procedure_param' => (string) ($row['procedure_param'] ?? ''),
                'position' => max(1, (int) ($row['position'] ?? 100)),
                'default_value' => isset($row['default_value']) && $row['default_value'] !== ''
                    ? (string) $row['default_value']
                    : null,
            ];
        }

        return $extras;
    }

    private function syncProcedurePreview(callable $set, string $connection, ?string $procedureName): void
    {
        [$signature, $definitionPreview] = $this->resolveProcedurePreview($connection, (string) $procedureName);
        $set('procedure_signature', $signature);
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function resolveProcedurePreview(string $connection, string $procedureName): array
    {
        if ($connection === '' || $procedureName === '') {
            return ['', ''];
        }

        try {
            $driver = DB::connection($connection)->getDriverName();

            if ($driver === 'sqlsrv') {
                $normalizedInput = str_replace(['[', ']'], '', $procedureName);

                $rows = DB::connection($connection)->select(
                    'SELECT TOP 1 p.object_id, s.name AS schema_name, p.name AS procedure_name
                     FROM sys.procedures p
                     INNER JOIN sys.schemas s ON s.schema_id = p.schema_id
                     WHERE p.name = ? OR (s.name + \'\.\' + p.name) = ?
                     ORDER BY CASE WHEN (s.name + \'\.\' + p.name) = ? THEN 0 ELSE 1 END, s.name, p.name',
                    [$normalizedInput, $normalizedInput, $normalizedInput]
                );

                if ($rows === []) {
                    return [self::t('filament/pages.custom_procedures.messages.procedure_not_found_on_connection'), ''];
                }

                $row = $rows[0];
                $objectId = (int) ($row->object_id ?? 0);
                $schema = (string) ($row->schema_name ?? 'dbo');
                $name = (string) ($row->procedure_name ?? $procedureName);

                $params = DB::connection($connection)->select(
                    'SELECT prm.name AS parameter_name,
                            TYPE_NAME(prm.user_type_id) AS type_name,
                            prm.max_length,
                            prm.precision,
                            prm.scale,
                            prm.is_output
                     FROM sys.parameters prm
                     WHERE prm.object_id = ?
                     ORDER BY prm.parameter_id',
                    [$objectId]
                );

                $paramLines = [];
                foreach ($params as $param) {
                    $type = strtoupper((string) ($param->type_name ?? 'SQL_VARIANT'));
                    $maxLength = (int) ($param->max_length ?? 0);
                    $precision = (int) ($param->precision ?? 0);
                    $scale = (int) ($param->scale ?? 0);

                    $typeWithLength = match ($type) {
                        'VARCHAR', 'NVARCHAR', 'CHAR', 'NCHAR', 'VARBINARY', 'BINARY' => $maxLength > 0 ? "{$type}({$maxLength})" : $type,
                        'DECIMAL', 'NUMERIC' => ($precision > 0 || $scale > 0) ? "{$type}({$precision},{$scale})" : $type,
                        default => $type,
                    };

                    $suffix = ((int) ($param->is_output ?? 0) === 1) ? ' OUTPUT' : '';
                    $paramLines[] = sprintf('    %s %s%s', (string) ($param->parameter_name ?? '@param'), $typeWithLength, $suffix);
                }

                $signature = sprintf(
                    "CREATE PROCEDURE [%s].[%s]\n%s",
                    $schema,
                    $name,
                    $paramLines !== [] ? implode(",\n", $paramLines) : '    -- no parameters'
                );

                $definitionRows = DB::connection($connection)->select(
                    'SELECT sm.definition
                     FROM sys.sql_modules sm
                     WHERE sm.object_id = ?',
                    [$objectId]
                );

                $definition = (string) ($definitionRows[0]->definition ?? '');

                return [$signature, mb_substr($definition, 0, 4000)];
            }
        } catch (Throwable $exception) {
            return [self::t('filament/pages.custom_procedures.messages.preview_load_failed', ['error' => $exception->getMessage()]), ''];
        }

        return [self::t('filament/pages.custom_procedures.messages.preview_not_supported'), ''];
    }

    /**
     * @param array<int, array<string, mixed>> $incomingMap
     * @return array<int, array{laravel_key: string, procedure_param: string, position: int}>
     */
    private function normalizeParameterMap(string $actionKey, array $incomingMap): array
    {
        if ($actionKey === '') {
            return [];
        }

        $actions = $this->procedureManager()->getActions();
        $defaultMap = $actions[$actionKey]['default_parameter_map'] ?? null;

        if (! is_array($defaultMap)) {
            throw new InvalidArgumentException("Unknown procedurable action: {$actionKey}");
        }

        $allowedKeys = [];
        foreach ($defaultMap as $row) {
            $key = (string) ($row['laravel_key'] ?? '');
            if ($key !== '') {
                $allowedKeys[$key] = true;
            }
        }

        $incomingByKey = [];
        foreach ($incomingMap as $row) {
            $key = (string) ($row['laravel_key'] ?? '');
            if ($key !== '' && isset($allowedKeys[$key])) {
                $incomingByKey[$key] = $row;
            }
        }

        $normalized = [];
        foreach ($defaultMap as $defaultRow) {
            $key = (string) ($defaultRow['laravel_key'] ?? '');
            if ($key === '' || ! isset($incomingByKey[$key])) {
                continue;
            }

            $customRow = $incomingByKey[$key];

            $normalized[] = [
                'laravel_key' => $key,
                'procedure_param' => (string) ($customRow['procedure_param'] ?? $defaultRow['procedure_param'] ?? ''),
                'position' => max(1, (int) ($customRow['position'] ?? $defaultRow['position'] ?? 1)),
            ];
        }

        return $normalized;
    }

    private function humanizeProcedureMessage(?string $message): string
    {
        return match ($message) {
            null, '' => self::t('filament/pages.custom_procedures.messages.completed_without_output'),
            'procedure_name_missing' => self::t('filament/pages.custom_procedures.messages.procedure_name_missing'),
            'procedure_mapping_inactive' => self::t('filament/pages.custom_procedures.messages.procedure_mapping_inactive'),
            'custom_procedures_disabled' => self::t('filament/pages.custom_procedures.messages.custom_procedures_disabled'),
            default => $message,
        };
    }

    /**
     * @param array<string, string|int|float|bool|null> $replace
     */
    private static function t(string $key, array $replace = []): string
    {
        return __($key, $replace);
    }

    /**
     * @param array<int, array<string, mixed>> $parameterMap
     * @return array<int, array{payload_key: string, value: string}>
     */
    private function buildTestInputValues(array $parameterMap): array
    {
        $rows = [];

        foreach ($parameterMap as $row) {
            $key = trim((string) ($row['laravel_key'] ?? ''));
            if ($key === '') {
                continue;
            }

            $default = $row['default_value'] ?? '';

            $rows[] = [
                'payload_key' => $key,
                'value' => is_scalar($default) ? (string) $default : '',
            ];
        }

        return $rows;
    }

    private function castTestValue(string $value): mixed
    {
        $trimmed = trim($value);

        if ($trimmed === '') {
            return null;
        }

        $lower = strtolower($trimmed);
        if ($lower === 'null') {
            return null;
        }

        if ($lower === 'true') {
            return true;
        }

        if ($lower === 'false') {
            return false;
        }

        if (is_numeric($trimmed)) {
            return str_contains($trimmed, '.') ? (float) $trimmed : (int) $trimmed;
        }

        return $value;
    }
}
