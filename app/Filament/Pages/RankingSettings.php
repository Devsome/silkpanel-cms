<?php

namespace App\Filament\Pages;

use App\Helpers\CrestHelper;
use App\Models\Setting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions as ActionsComponent;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;
use SilkPanel\SilkroadModels\Models\Shard\AbstractChar;
use SilkPanel\SilkroadModels\Models\Shard\BindingOptionWithItem;
use SilkPanel\SilkroadModels\Models\Shard\Guild;
use SilkPanel\SilkroadModels\Models\Shard\GuildCrest;
use SilkPanel\SilkroadModels\Models\Shard\GuildMember;
use SilkPanel\SilkroadModels\Models\Shard\Inventory;
use SilkPanel\SilkroadModels\Models\Shard\Items;
use SilkPanel\SilkroadModels\Models\Shard\RefObjCommon;
use Throwable;

/**
 * @property-read Schema $form
 */
class RankingSettings extends Page
{
    private const UNIQUE_JOIN_OUTPUT_COLUMN = '__join_output';
    private const UNIQUE_POINTS_OUTPUT_COLUMN = '__unique_points';

    protected const AVAILABLE_CHAR_COLUMNS = [
        'CharID'      => 'Character ID',
        'CharName16'  => 'Character Name',
        'CurLevel'    => 'Level',
        'RefObjID'    => 'Character Class ID',
        'GuildID'     => 'Guild ID',
        'GuildName'   => 'Guild Name',
        'ItemPoints'  => 'Item Points',
    ];

    protected const AVAILABLE_GUILD_COLUMNS = [
        'ID'             => 'Guild ID',
        'Name'           => 'Guild Name',
        'Lvl'            => 'Guild Level',
        'GatheredSP'     => 'Gathered SP',
        'FoundationDate' => 'Foundation Date',
        'LeaderID'       => 'Leader ID',
        'LeaderName'     => 'Leader Name',
        'TotalMember'    => 'Total Member',
        'ItemPoints'     => 'Item Points',
        'CrestIcon'      => 'Crest Icon',
    ];

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowTrendingUp;

    protected string $view = 'filament.pages.ranking-settings';

    protected static string|\UnitEnum|null $navigationGroup = 'Configuration';

    protected static ?int $navigationSort = 55;

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    /** @var array<int, array<string, mixed>> */
    public array $previewData = [];

    /** @var array<int, array<string, string>> */
    public array $previewColumns = [];

    /** @var array<string, string> */
    public array $uniqueTableColumns = [];

    /** @var array<string, string> */
    public array $uniqueJoinTableColumns = [];

    public ?string $previewError = null;

    public string $previewTab = '';

    public static function getNavigationLabel(): string
    {
        return __('filament/rankings.navigation');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament/settings.navigation_group');
    }

    public function mount(): void
    {
        $this->form->fill($this->getRankingSettingsArray());
        $this->refreshUniqueTableColumns(notify: false);
        if ((bool) ($this->data['ranking_unique_join_enabled'] ?? false)) {
            $this->refreshUniqueJoinTableColumns(notify: false);
        }
        $this->syncUniqueJoinOutputDisplayColumn();
        $this->syncUniquePointsDisplayColumn();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([
                    Tabs::make('ranking_tabs')
                        ->columnSpanFull()
                        ->tabs([
                            Tab::make(__('filament/rankings.tabs.chars'))
                                ->icon('heroicon-o-users')
                                ->schema([
                                    Section::make(__('filament/rankings.sections.config'))
                                        ->schema([
                                            TextInput::make('ranking_chars_title')
                                                ->label(__('filament/rankings.fields.title'))
                                                ->placeholder(__('filament/rankings.fields.title_placeholder'))
                                                ->maxLength(100)
                                                ->required(),

                                            TextInput::make('ranking_chars_cache_ttl')
                                                ->label(__('filament/rankings.fields.cache_ttl'))
                                                ->helperText(__('filament/rankings.fields.cache_ttl_description'))
                                                ->numeric()
                                                ->minValue(0)
                                                ->maxValue(10080)
                                                ->default(60)
                                                ->suffix('min'),

                                            TextInput::make('ranking_chars_limit')
                                                ->label(__('filament/rankings.fields.limit'))
                                                ->helperText(__('filament/rankings.fields.limit_description'))
                                                ->numeric()
                                                ->minValue(0)
                                                ->maxValue(10000)
                                                ->default(50)
                                                ->placeholder('0')
                                                ->suffix('#'),

                                            Select::make('ranking_chars_excluded')
                                                ->label(__('filament/rankings.fields.excluded'))
                                                ->helperText(__('filament/rankings.fields.excluded_description'))
                                                ->multiple()
                                                ->searchable()
                                                ->getSearchResultsUsing(function (string $search): array {
                                                    /** @var AbstractChar $charModel */
                                                    $charModel = app(AbstractChar::class);

                                                    return $charModel::query()
                                                        ->where('CharName16', 'like', "%{$search}%")
                                                        ->where('deleted', 0)
                                                        ->limit(20)
                                                        ->pluck('CharName16', 'CharID')
                                                        ->toArray();
                                                })
                                                ->getOptionLabelsUsing(function (array $values): array {
                                                    /** @var AbstractChar $charModel */
                                                    $charModel = app(AbstractChar::class);

                                                    return $charModel::query()
                                                        ->whereIn('CharID', $values)
                                                        ->pluck('CharName16', 'CharID')
                                                        ->toArray();
                                                })
                                                ->columnSpanFull(),
                                        ])
                                        ->columns(2)
                                        ->secondary(),

                                    Section::make(__('filament/rankings.sections.columns'))
                                        ->description(__('filament/rankings.sections.columns_description'))
                                        ->schema([
                                            Repeater::make('ranking_chars_columns')
                                                ->compact()
                                                ->table([
                                                    TableColumn::make(__('filament/rankings.fields.column_name')),
                                                    TableColumn::make(__('filament/rankings.fields.column_label')),
                                                ])
                                                ->schema([
                                                    Select::make('column')
                                                        ->label(__('filament/rankings.fields.column_name'))
                                                        ->options(self::AVAILABLE_CHAR_COLUMNS)
                                                        ->searchable()
                                                        ->preload()
                                                        ->required(),

                                                    TextInput::make('label')
                                                        ->label(__('filament/rankings.fields.column_label'))
                                                        ->placeholder('Character')
                                                        ->required(),
                                                ])
                                                ->columns(2)
                                                ->default([
                                                    ['column' => 'CharName16', 'label' => 'Character Name'],
                                                    ['column' => 'CurLevel',   'label' => 'Level'],
                                                    ['column' => 'GuildName',  'label' => 'Guild Name'],
                                                    ['column' => 'ItemPoints', 'label' => 'Item Points'],
                                                ])
                                                ->addActionLabel(__('filament/rankings.fields.add_column'))
                                                ->reorderable()
                                                ->collapsible(),
                                        ])
                                        ->secondary(),

                                    ActionsComponent::make([
                                        Action::make('testCharsRanking')
                                            ->label(__('filament/rankings.actions.test'))
                                            ->icon('heroicon-o-beaker')
                                            ->color('gray')
                                            ->action(fn() => $this->testCharsRankingQuery()),
                                    ]),
                                ]),

                            Tab::make(__('filament/rankings.tabs.guilds'))
                                ->icon('heroicon-o-user-group')
                                ->schema([
                                    Section::make(__('filament/rankings.sections.config'))
                                        ->schema([
                                            TextInput::make('ranking_guilds_title')
                                                ->label(__('filament/rankings.fields.title'))
                                                ->placeholder(__('filament/rankings.fields.title_guild_placeholder'))
                                                ->maxLength(100)
                                                ->required(),

                                            TextInput::make('ranking_guilds_cache_ttl')
                                                ->label(__('filament/rankings.fields.cache_ttl'))
                                                ->helperText(__('filament/rankings.fields.cache_ttl_description'))
                                                ->numeric()
                                                ->minValue(0)
                                                ->maxValue(10080)
                                                ->default(60)
                                                ->suffix('min'),

                                            TextInput::make('ranking_guilds_limit')
                                                ->label(__('filament/rankings.fields.limit'))
                                                ->helperText(__('filament/rankings.fields.limit_description'))
                                                ->numeric()
                                                ->minValue(0)
                                                ->maxValue(10000)
                                                ->default(50)
                                                ->placeholder('0')
                                                ->suffix('#'),

                                            Select::make('ranking_guilds_excluded')
                                                ->label(__('filament/rankings.fields.excluded_guilds'))
                                                ->helperText(__('filament/rankings.fields.excluded_guilds_description'))
                                                ->multiple()
                                                ->searchable()
                                                ->getSearchResultsUsing(function (string $search): array {
                                                    return Guild::query()
                                                        ->where('Name', 'like', "%{$search}%")
                                                        ->limit(20)
                                                        ->pluck('Name', 'ID')
                                                        ->toArray();
                                                })
                                                ->getOptionLabelsUsing(function (array $values): array {
                                                    return Guild::query()
                                                        ->whereIn('ID', $values)
                                                        ->pluck('Name', 'ID')
                                                        ->toArray();
                                                })
                                                ->columnSpanFull(),
                                        ])
                                        ->columns(2)
                                        ->secondary(),

                                    Section::make(__('filament/rankings.sections.columns'))
                                        ->description(__('filament/rankings.sections.columns_description'))
                                        ->schema([
                                            Repeater::make('ranking_guilds_columns')
                                                ->compact()
                                                ->table([
                                                    TableColumn::make(__('filament/rankings.fields.column_name')),
                                                    TableColumn::make(__('filament/rankings.fields.column_label')),
                                                ])
                                                ->schema([
                                                    Select::make('column')
                                                        ->label(__('filament/rankings.fields.column_name'))
                                                        ->options(self::AVAILABLE_GUILD_COLUMNS)
                                                        ->searchable()
                                                        ->preload()
                                                        ->required(),

                                                    TextInput::make('label')
                                                        ->label(__('filament/rankings.fields.column_label'))
                                                        ->placeholder('Guild Name')
                                                        ->required(),
                                                ])
                                                ->columns(2)
                                                ->default([
                                                    ['column' => 'Name',       'label' => 'Guild Name'],
                                                    ['column' => 'Lvl',        'label' => 'Guild Level'],
                                                    ['column' => 'LeaderName', 'label' => 'Leader Name'],
                                                    ['column' => 'TotalMember', 'label' => 'Members'],
                                                    ['column' => 'ItemPoints', 'label' => 'Item Points'],
                                                ])
                                                ->addActionLabel(__('filament/rankings.fields.add_column'))
                                                ->reorderable()
                                                ->collapsible(),
                                        ])
                                        ->secondary(),

                                    ActionsComponent::make([
                                        Action::make('testGuildsRanking')
                                            ->label(__('filament/rankings.actions.test_guilds'))
                                            ->icon('heroicon-o-beaker')
                                            ->color('gray')
                                            ->action(fn() => $this->testGuildsRankingQuery()),
                                    ]),
                                ]),

                            Tab::make(__('filament/rankings.tabs.unique'))
                                ->icon('heroicon-o-sparkles')
                                ->schema([
                                    Section::make(__('filament/rankings.sections.config'))
                                        ->schema([
                                            TextInput::make('ranking_unique_title')
                                                ->label(__('filament/rankings.fields.title'))
                                                ->placeholder(__('filament/rankings.fields.title_unique_placeholder'))
                                                ->maxLength(100)
                                                ->required(),

                                            TextInput::make('ranking_unique_cache_ttl')
                                                ->label(__('filament/rankings.fields.cache_ttl'))
                                                ->helperText(__('filament/rankings.fields.cache_ttl_description'))
                                                ->numeric()
                                                ->minValue(0)
                                                ->maxValue(10080)
                                                ->default(60)
                                                ->suffix('min'),

                                            TextInput::make('ranking_unique_limit')
                                                ->label(__('filament/rankings.fields.limit'))
                                                ->helperText(__('filament/rankings.fields.limit_description'))
                                                ->numeric()
                                                ->minValue(0)
                                                ->maxValue(10000)
                                                ->default(50)
                                                ->placeholder('0')
                                                ->suffix('#'),

                                            Select::make('ranking_unique_connection')
                                                ->label(__('filament/rankings.fields.database_connection'))
                                                ->options(fn(): array => $this->getUniqueConnectionOptions())
                                                ->searchable()
                                                ->preload()
                                                ->live()
                                                ->required(),

                                            Select::make('ranking_unique_table')
                                                ->label(__('filament/rankings.fields.table'))
                                                ->helperText(__('filament/rankings.fields.table_description'))
                                                ->options(fn(): array => $this->getUniqueTableOptions())
                                                ->searchable()
                                                ->preload()
                                                ->placeholder('sro_*')
                                                ->suffixAction(
                                                    Action::make('loadUniqueColumnsInline')
                                                        ->label(__('filament/rankings.actions.load_unique_columns'))
                                                        ->icon('heroicon-o-arrow-path')
                                                        ->action(fn() => $this->refreshUniqueTableColumns())
                                                )
                                                ->required(),

                                            Select::make('ranking_unique_order_by')
                                                ->label(__('filament/rankings.fields.order_by'))
                                                ->options(fn(): array => $this->uniqueTableColumns)
                                                ->searchable(),

                                            Select::make('ranking_unique_order_direction')
                                                ->label(__('filament/rankings.fields.order_direction'))
                                                ->options([
                                                    'desc' => __('filament/rankings.fields.order_desc'),
                                                    'asc' => __('filament/rankings.fields.order_asc'),
                                                ])
                                                ->default('desc')
                                                ->required(),
                                        ])
                                        ->columns(2)
                                        ->secondary(),

                                    Section::make(__('filament/rankings.sections.join'))
                                        ->description(__('filament/rankings.sections.join_description'))
                                        ->schema([
                                            Toggle::make('ranking_unique_join_enabled')
                                                ->label(__('filament/rankings.fields.join_enabled'))
                                                ->columnSpanFull()
                                                ->live(),

                                            Select::make('ranking_unique_join_connection')
                                                ->label(__('filament/rankings.fields.join_connection'))
                                                ->options(fn(): array => $this->getUniqueJoinConnectionOptions())
                                                ->searchable()
                                                ->preload()
                                                ->live()
                                                ->visible(fn(): bool => (bool) ($this->data['ranking_unique_join_enabled'] ?? false)),

                                            Select::make('ranking_unique_join_table')
                                                ->label(__('filament/rankings.fields.join_table'))
                                                ->helperText(__('filament/rankings.fields.join_table_description'))
                                                ->options(fn(): array => $this->getUniqueJoinTableOptions())
                                                ->searchable()
                                                ->preload()
                                                ->placeholder('sro_*')
                                                ->suffixAction(
                                                    Action::make('loadUniqueJoinColumnsInline')
                                                        ->label(__('filament/rankings.actions.load_join_columns'))
                                                        ->icon('heroicon-o-arrow-path')
                                                        ->action(fn() => $this->refreshUniqueJoinTableColumns())
                                                )
                                                ->visible(fn(): bool => (bool) ($this->data['ranking_unique_join_enabled'] ?? false)),

                                            Select::make('ranking_unique_join_local_key')
                                                ->label(__('filament/rankings.fields.join_local_key'))
                                                ->helperText(__('filament/rankings.fields.join_local_key_description'))
                                                ->options(fn(): array => $this->uniqueTableColumns)
                                                ->searchable()
                                                ->visible(fn(): bool => (bool) ($this->data['ranking_unique_join_enabled'] ?? false)),

                                            Select::make('ranking_unique_join_foreign_key')
                                                ->label(__('filament/rankings.fields.join_foreign_key'))
                                                ->helperText(__('filament/rankings.fields.join_foreign_key_description'))
                                                ->options(fn(): array => $this->uniqueJoinTableColumns)
                                                ->searchable()
                                                ->visible(fn(): bool => (bool) ($this->data['ranking_unique_join_enabled'] ?? false)),

                                            Select::make('ranking_unique_join_output_column')
                                                ->label(__('filament/rankings.fields.join_output_column'))
                                                ->helperText(__('filament/rankings.fields.join_output_column_description'))
                                                ->options(fn(): array => $this->uniqueJoinTableColumns)
                                                ->searchable()
                                                ->live()
                                                ->afterStateUpdated(fn() => $this->syncUniqueJoinOutputDisplayColumn())
                                                ->visible(fn(): bool => (bool) ($this->data['ranking_unique_join_enabled'] ?? false)),

                                        ])
                                        ->columns(2)
                                        ->secondary(),

                                    Section::make(__('filament/rankings.sections.unique_points'))
                                        ->description(__('filament/rankings.sections.unique_points_description'))
                                        ->schema([
                                            Toggle::make('ranking_unique_points_enabled')
                                                ->label(__('filament/rankings.fields.unique_points_enabled'))
                                                ->columnSpanFull()
                                                ->live()
                                                ->afterStateUpdated(fn() => $this->syncUniquePointsDisplayColumn()),

                                            Select::make('ranking_unique_points_source_column')
                                                ->label(__('filament/rankings.fields.unique_points_source_column'))
                                                ->helperText(__('filament/rankings.fields.unique_points_source_column_description'))
                                                ->options(fn(): array => $this->uniqueTableColumns)
                                                ->searchable()
                                                ->visible(fn(): bool => (bool) ($this->data['ranking_unique_points_enabled'] ?? false)),

                                            Select::make('ranking_unique_points_player_column')
                                                ->label(__('filament/rankings.fields.unique_points_player_column'))
                                                ->helperText(__('filament/rankings.fields.unique_points_player_column_description'))
                                                ->options(fn(): array => $this->uniqueTableColumns)
                                                ->searchable()
                                                ->visible(fn(): bool => (bool) ($this->data['ranking_unique_points_enabled'] ?? false)),

                                            ActionsComponent::make([
                                                Action::make('loadUniquePointsFromConfig')
                                                    ->label(__('filament/rankings.actions.load_unique_points'))
                                                    ->icon('heroicon-o-arrow-path')
                                                    ->color('gray')
                                                    ->action(fn() => $this->refreshUniquePointsMapFromConfig()),
                                            ])->visible(fn(): bool => (bool) ($this->data['ranking_unique_points_enabled'] ?? false)),

                                            Repeater::make('ranking_unique_points_map')
                                                ->compact()
                                                ->addable(false)
                                                ->table([
                                                    TableColumn::make(__('filament/rankings.fields.unique_points_key')),
                                                    TableColumn::make(__('filament/rankings.fields.unique_points_id')),
                                                    TableColumn::make(__('filament/rankings.fields.unique_points_name')),
                                                    TableColumn::make(__('filament/rankings.fields.unique_points_value')),
                                                ])
                                                ->schema([
                                                    TextInput::make('key')
                                                        ->label(__('filament/rankings.fields.unique_points_key'))
                                                        ->readOnly()
                                                        ->dehydrated(true),

                                                    TextInput::make('id')
                                                        ->label(__('filament/rankings.fields.unique_points_id'))
                                                        ->readOnly()
                                                        ->dehydrated(true),

                                                    TextInput::make('name')
                                                        ->label(__('filament/rankings.fields.unique_points_name'))
                                                        ->readOnly()
                                                        ->dehydrated(true),

                                                    TextInput::make('points')
                                                        ->label(__('filament/rankings.fields.unique_points_value'))
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->default(1)
                                                        ->required(),
                                                ])
                                                ->columns(4)
                                                ->deletable(false)
                                                ->reorderableWithDragAndDrop(false)
                                                ->reorderableWithButtons()
                                                ->columnSpanFull()
                                                ->visible(fn(): bool => (bool) ($this->data['ranking_unique_points_enabled'] ?? false)),
                                        ])
                                        ->columns(3)
                                        ->secondary(),

                                    Section::make(__('filament/rankings.sections.columns'))
                                        ->description(__('filament/rankings.sections.columns_description'))
                                        ->schema([
                                            Repeater::make('ranking_unique_columns')
                                                ->compact()
                                                ->table([
                                                    TableColumn::make(__('filament/rankings.fields.column_name')),
                                                    TableColumn::make(__('filament/rankings.fields.column_label')),
                                                ])
                                                ->schema([
                                                    Select::make('column')
                                                        ->label(__('filament/rankings.fields.column_name'))
                                                        ->options(fn(): array => $this->getUniqueDisplayColumnOptions())
                                                        ->searchable()
                                                        ->required(),

                                                    TextInput::make('label')
                                                        ->label(__('filament/rankings.fields.column_label'))
                                                        ->placeholder('Value')
                                                        ->required(),
                                                ])
                                                ->columns(2)
                                                ->addActionLabel(__('filament/rankings.fields.add_column'))
                                                ->reorderableWithDragAndDrop(false)
                                                ->reorderableWithButtons()
                                                ->collapsible(),
                                        ])
                                        ->secondary(),

                                    ActionsComponent::make([
                                        Action::make('testUniqueRanking')
                                            ->label(__('filament/rankings.actions.test_unique'))
                                            ->icon('heroicon-o-beaker')
                                            ->color('gray')
                                            ->action(fn() => $this->testUniqueRankingQuery()),
                                    ]),
                                ]),
                        ]),
                ])
                    ->livewireSubmitHandler('save')
                    ->footer([
                        ActionsComponent::make([
                            Action::make('save')
                                ->label(__('filament/rankings.actions.save'))
                                ->submit('save')
                                ->keyBindings(['mod+s']),
                        ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $keys = [
            'ranking_chars_title',
            'ranking_chars_cache_ttl',
            'ranking_chars_limit',
            'ranking_chars_excluded',
            'ranking_chars_columns',
            'ranking_guilds_title',
            'ranking_guilds_cache_ttl',
            'ranking_guilds_limit',
            'ranking_guilds_excluded',
            'ranking_guilds_columns',
            'ranking_unique_title',
            'ranking_unique_cache_ttl',
            'ranking_unique_limit',
            'ranking_unique_connection',
            'ranking_unique_table',
            'ranking_unique_order_by',
            'ranking_unique_order_direction',
            'ranking_unique_columns',
            'ranking_unique_join_enabled',
            'ranking_unique_join_connection',
            'ranking_unique_join_table',
            'ranking_unique_join_local_key',
            'ranking_unique_join_foreign_key',
            'ranking_unique_join_output_column',
            'ranking_unique_join_output_label',
            'ranking_unique_points_enabled',
            'ranking_unique_points_source_column',
            'ranking_unique_points_player_column',
            'ranking_unique_points_map',
        ];

        foreach ($keys as $key) {
            if (array_key_exists($key, $data)) {
                Setting::set($key, $data[$key], null, null, null);
            }
        }

        Notification::make()
            ->success()
            ->title(__('filament/rankings.notifications.saved_title'))
            ->body(__('filament/rankings.notifications.saved_message'))
            ->send();
    }

    public function testCharsRankingQuery(): void
    {
        $this->previewError   = null;
        $this->previewData    = [];
        $this->previewColumns = [];
        $this->previewTab     = 'chars';

        try {
            $rows = $this->buildCharsRankingQuery(previewLimit: 10)->get();

            $this->previewData    = $rows->map(fn($row) => (array) $row)->all();
            $this->previewColumns = $this->data['ranking_chars_columns'] ?? [];
        } catch (Throwable $e) {
            $this->previewError = $e->getMessage();
        }
    }

    public function testGuildsRankingQuery(): void
    {
        $this->previewError   = null;
        $this->previewData    = [];
        $this->previewColumns = [];
        $this->previewTab     = 'guilds';

        try {
            $rows = $this->buildGuildsRankingQuery(previewLimit: 10)->get();

            $this->previewData    = $rows->map(fn($row) => $this->mapGuildPreviewRow($row))->all();
            $this->previewColumns = $this->data['ranking_guilds_columns'] ?? [];
        } catch (Throwable $e) {
            $this->previewError = $e->getMessage();
        }
    }

    public function testUniqueRankingQuery(): void
    {
        $this->previewError   = null;
        $this->previewData    = [];
        $this->previewColumns = [];
        $this->previewTab     = 'unique';

        try {
            $rows = $this->buildUniqueRankingQuery(previewLimit: 10)->get();

            $this->previewData    = $rows->map(fn($row) => (array) $row)->all();
            $this->previewColumns = $this->data['ranking_unique_columns'] ?? [];
        } catch (Throwable $e) {
            $this->previewError = $e->getMessage();
        }
    }

    /**
     * @param object $row
     * @return array<string, mixed>
     */
    private function mapGuildPreviewRow(object $row): array
    {
        $data = (array) $row;
        $crestHex = $data['CrestIcon'] ?? null;

        if (is_string($crestHex) && $crestHex !== '') {
            try {
                $data['CrestIcon'] = CrestHelper::decodeHexToDataUri($crestHex);
            } catch (Throwable) {
                $data['CrestIcon'] = null;
            }
        } else {
            $data['CrestIcon'] = null;
        }

        return $data;
    }

    private function buildCharsRankingQuery(int $previewLimit = 0): Builder
    {
        /** @var AbstractChar $charModel */
        $charModel  = app(AbstractChar::class);
        $connection = $charModel->getConnectionName();

        $invTable  = (new Inventory)->getTable();
        $itemTable = (new Items)->getTable();
        $refTable  = (new RefObjCommon)->getTable();
        $bindTable = (new BindingOptionWithItem)->getTable();
        $charTable = $charModel->getTable();
        $guildTable = (new Guild)->getTable();

        $itemPointsSub = DB::connection($connection)
            ->table("$invTable as inv")
            ->selectRaw(
                'ISNULL(SUM(b.nOptValue), 0)'
                    . ' + ISNULL(SUM(i.OptLevel), 0)'
                    . ' + ISNULL(SUM(r.ReqLevel1), 0)'
                    . ' + ISNULL(SUM(CASE WHEN r.CodeName128 LIKE \'%_A_RARE%\' THEN 5 ELSE 0 END), 0)'
                    . ' + ISNULL(SUM(CASE WHEN r.CodeName128 LIKE \'%_B_RARE%\' THEN 10 ELSE 0 END), 0)'
                    . ' + ISNULL(SUM(CASE WHEN r.CodeName128 LIKE \'%_C_RARE%\' THEN 15 ELSE 0 END), 0)'
            )
            ->join("$itemTable as i", 'i.ID64', '=', 'inv.ItemID')
            ->join("$refTable as r", 'r.ID', '=', 'i.RefItemID')
            ->leftJoin("$bindTable as b", function ($join) {
                $join->on('b.nItemDBID', '=', 'i.ID64')
                    ->where('b.bOptType', '=', 2)
                    ->where('b.nOptValue', '>', 0);
            })
            ->whereColumn('inv.CharID', 'chars.CharID')
            ->where('inv.Slot', '<', 13)
            ->whereNotIn('inv.Slot', [7, 8])
            ->where('inv.ItemID', '>', 0);

        return DB::connection($connection)
            ->table("$charTable as chars")
            ->select([
                'chars.CharID',
                'chars.CharName16',
                'chars.CurLevel',
                'chars.RefObjID',
                'g.ID as GuildID',
                'g.Name as GuildName',
            ])
            ->selectSub($itemPointsSub, 'ItemPoints')
            ->leftJoin("$guildTable as g", 'g.ID', '=', 'chars.GuildID')
            ->where('chars.deleted', 0)
            ->when(
                !empty($this->data['ranking_chars_excluded']),
                fn($q) => $q->whereNotIn('chars.CharID', $this->data['ranking_chars_excluded'])
            )
            ->orderByDesc('ItemPoints')
            ->orderByDesc('chars.CurLevel')
            ->when($previewLimit > 0, fn($q) => $q->limit($previewLimit))
            ->when($previewLimit === 0, function ($q) {
                $limit = (int) ($this->data['ranking_chars_limit'] ?? 50);
                return $limit > 0 ? $q->limit($limit) : $q;
            });
    }

    private function buildGuildsRankingQuery(int $previewLimit = 0): Builder
    {
        /** @var AbstractChar $charModel */
        $charModel  = app(AbstractChar::class);
        $connection = $charModel->getConnectionName();
        $version    = (string) config('silkpanel.version', 'vsro');

        $guildTable = (new Guild)->getTable();
        $memberTable = (new GuildMember)->getTable();
        $invTable  = (new Inventory)->getTable();
        $itemTable = (new Items)->getTable();
        $refTable  = (new RefObjCommon)->getTable();
        $bindTable = (new BindingOptionWithItem)->getTable();
        $crestTable = (new GuildCrest)->getTable();

        $leaderIdSub = DB::connection($connection)
            ->table("$memberTable as gm")
            ->select('gm.CharID')
            ->whereColumn('gm.GuildID', 'guilds.ID')
            ->where('gm.MemberClass', 0)
            ->limit(1);

        $leaderNameSub = DB::connection($connection)
            ->table("$memberTable as gm")
            ->select('gm.CharName')
            ->whereColumn('gm.GuildID', 'guilds.ID')
            ->where('gm.MemberClass', 0)
            ->limit(1);

        $totalMemberSub = DB::connection($connection)
            ->table("$memberTable as gm")
            ->selectRaw('COUNT(*)')
            ->whereColumn('gm.GuildID', 'guilds.ID');

        $itemPointsSub = DB::connection($connection)
            ->table("$memberTable as gm")
            ->selectRaw(
                'ISNULL(SUM(b.nOptValue), 0)'
                    . ' + ISNULL(SUM(i.OptLevel), 0)'
                    . ' + ISNULL(SUM(r.ReqLevel1), 0)'
                    . ' + ISNULL(SUM(CASE WHEN r.CodeName128 LIKE \'%_A_RARE%\' THEN 5 ELSE 0 END), 0)'
                    . ' + ISNULL(SUM(CASE WHEN r.CodeName128 LIKE \'%_B_RARE%\' THEN 10 ELSE 0 END), 0)'
                    . ' + ISNULL(SUM(CASE WHEN r.CodeName128 LIKE \'%_C_RARE%\' THEN 15 ELSE 0 END), 0)'
            )
            ->join("$invTable as inv", 'inv.CharID', '=', 'gm.CharID')
            ->join("$itemTable as i", 'i.ID64', '=', 'inv.ItemID')
            ->join("$refTable as r", 'r.ID', '=', 'i.RefItemID')
            ->leftJoin("$bindTable as b", function ($join) {
                $join->on('b.nItemDBID', '=', 'i.ID64')
                    ->where('b.bOptType', '=', 2)
                    ->where('b.nOptValue', '>', 0);
            })
            ->whereColumn('gm.GuildID', 'guilds.ID')
            ->where('inv.Slot', '<', 13)
            ->whereNotIn('inv.Slot', [7, 8])
            ->where('inv.ItemID', '>', 0);

        $query = DB::connection($connection)
            ->table("$guildTable as guilds")
            ->select([
                'guilds.ID',
                'guilds.Name',
                'guilds.Lvl',
                'guilds.GatheredSP',
                'guilds.FoundationDate',
            ])
            ->selectSub($leaderIdSub, 'LeaderID')
            ->selectSub($leaderNameSub, 'LeaderName')
            ->selectSub($totalMemberSub, 'TotalMember')
            ->selectSub($itemPointsSub, 'ItemPoints')
            ->when(
                !empty($this->data['ranking_guilds_excluded']),
                fn($q) => $q->whereNotIn('guilds.ID', $this->data['ranking_guilds_excluded'])
            )
            ->orderByDesc('ItemPoints')
            ->orderByDesc('guilds.Lvl');

        if ($version === 'isro') {
            $query
                ->leftJoin("$crestTable as gc", 'gc.GuildID', '=', 'guilds.ID')
                ->addSelect(DB::raw('CONVERT(VARCHAR(MAX), gc.CrestBinary, 2) as CrestIcon'));
        }

        return $query
            ->when($previewLimit > 0, fn($q) => $q->limit($previewLimit))
            ->when($previewLimit === 0, function ($q) {
                $limit = (int) ($this->data['ranking_guilds_limit'] ?? 50);
                return $limit > 0 ? $q->limit($limit) : $q;
            });
    }

    private function buildUniqueRankingQuery(int $previewLimit = 0): Builder
    {
        $connection = (string) ($this->data['ranking_unique_connection'] ?? config('database.default'));
        $table = trim((string) ($this->data['ranking_unique_table'] ?? ''));
        $baseAlias = 'ranking_base';

        if ($table === '') {
            throw new RuntimeException('Please define a table for unique ranking.');
        }

        $columns = collect($this->data['ranking_unique_columns'] ?? [])
            ->pluck('column')
            ->filter(fn(mixed $column): bool => is_string($column) && $column !== '')
            ->values()
            ->all();

        $baseColumns = array_values(array_filter(
            $columns,
            fn(string $column): bool => ! in_array($column, [self::UNIQUE_JOIN_OUTPUT_COLUMN, self::UNIQUE_POINTS_OUTPUT_COLUMN], true)
        ));

        $hasJoinOutputSelected = in_array(self::UNIQUE_JOIN_OUTPUT_COLUMN, $columns, true);
        $hasPointsColumnSelected = in_array(self::UNIQUE_POINTS_OUTPUT_COLUMN, $columns, true);
        $pointsEnabled = (bool) ($this->data['ranking_unique_points_enabled'] ?? false);

        if ($baseColumns === [] && ! $hasJoinOutputSelected && ! $hasPointsColumnSelected) {
            throw new RuntimeException('Please load table columns and pick at least one display column.');
        }

        $selectedColumns = array_map(
            fn(string $column) => DB::raw("{$baseAlias}.{$column} as {$column}"),
            $baseColumns
        );

        $query = DB::connection($connection)
            ->table("{$table} as {$baseAlias}")
            ->select($selectedColumns);

        $pointsSourceColumn = trim((string) ($this->data['ranking_unique_points_source_column'] ?? ''));
        $pointsPlayerColumn = trim((string) ($this->data['ranking_unique_points_player_column'] ?? ''));

        $pointsRows = collect($this->data['ranking_unique_points_map'] ?? [])
            ->filter(fn(mixed $row): bool => is_array($row))
            ->values();

        if ($pointsEnabled) {
            if ($pointsSourceColumn === '' || $pointsPlayerColumn === '') {
                throw new RuntimeException('Please select source and player columns for unique points.');
            }

            if ($pointsRows->isEmpty()) {
                throw new RuntimeException('Please load unique points mapping from config first.');
            }

            $query->select([]);

            $effectiveBaseColumns = $baseColumns;
            if (! in_array($pointsPlayerColumn, $effectiveBaseColumns, true)) {
                array_unshift($effectiveBaseColumns, $pointsPlayerColumn);
            }

            foreach ($effectiveBaseColumns as $column) {
                $quotedColumn = $this->quoteSqlIdentifier($column);

                if ($column === $pointsPlayerColumn) {
                    $query->addSelect(DB::raw("{$baseAlias}.{$quotedColumn} as {$quotedColumn}"));
                    $query->groupBy(DB::raw("{$baseAlias}.{$quotedColumn}"));
                } else {
                    $query->addSelect(DB::raw("MAX({$baseAlias}.{$quotedColumn}) as {$quotedColumn}"));
                }
            }

            [$pointsCaseSql, $pointsCaseBindings] = $this->buildUniquePointsCaseExpression(
                sourceColumn: $pointsSourceColumn,
                pointsRows: $pointsRows,
                baseAlias: $baseAlias,
            );

            if ($pointsCaseSql !== '') {
                $query->selectRaw("SUM({$pointsCaseSql}) as " . self::UNIQUE_POINTS_OUTPUT_COLUMN, $pointsCaseBindings);
            } else {
                $query->selectRaw('0 as ' . self::UNIQUE_POINTS_OUTPUT_COLUMN);
            }
        }

        $orderBy = (string) ($this->data['ranking_unique_order_by'] ?? '');
        if (! $pointsEnabled && $orderBy !== '') {
            $direction = strtolower((string) ($this->data['ranking_unique_order_direction'] ?? 'desc')) === 'asc' ? 'asc' : 'desc';
            // Qualify with main table name to avoid ambiguity when a LEFT JOIN adds same column names
            $query->orderBy("{$baseAlias}.{$orderBy}", $direction);
        }

        // Optional left join
        $joinEnabled = (bool) ($this->data['ranking_unique_join_enabled'] ?? false);
        if ($joinEnabled) {
            $joinConnection = trim((string) ($this->data['ranking_unique_join_connection'] ?? ''));
            $joinTable      = trim((string) ($this->data['ranking_unique_join_table'] ?? ''));
            $localKey       = trim((string) ($this->data['ranking_unique_join_local_key'] ?? ''));
            $foreignKey     = trim((string) ($this->data['ranking_unique_join_foreign_key'] ?? ''));
            $outputColumn   = trim((string) ($this->data['ranking_unique_join_output_column'] ?? ''));

            if ($joinTable !== '' && $localKey !== '' && $foreignKey !== '' && $outputColumn !== '') {
                // Build fully-qualified table name for cross-database joins on the same SQL Server instance
                if ($joinConnection !== '' && $joinConnection !== $connection) {
                    $joinDbConfig = config("database.connections.{$joinConnection}", []);
                    $joinDatabase = is_array($joinDbConfig) ? trim((string) ($joinDbConfig['database'] ?? '')) : '';
                    $qualifiedJoinTable = $joinDatabase !== '' ? "{$joinDatabase}.dbo.{$joinTable}" : $joinTable;
                } else {
                    $qualifiedJoinTable = $joinTable;
                }

                $query->leftJoin("{$qualifiedJoinTable} as _ranking_join", "_ranking_join.{$foreignKey}", '=', "{$baseAlias}.{$localKey}");

                if ($hasJoinOutputSelected) {
                    if ($pointsEnabled) {
                        $quotedOutput = $this->quoteSqlIdentifier($outputColumn);
                        $query->addSelect(DB::raw('MAX(_ranking_join.' . $quotedOutput . ') as ' . self::UNIQUE_JOIN_OUTPUT_COLUMN));
                    } else {
                        $query->addSelect(DB::raw('_ranking_join.' . $outputColumn . ' as ' . self::UNIQUE_JOIN_OUTPUT_COLUMN));
                    }
                }
            }
        }

        if ($pointsEnabled) {
            $query->orderByDesc(self::UNIQUE_POINTS_OUTPUT_COLUMN);
        }

        return $query
            ->when($previewLimit > 0, fn($q) => $q->limit($previewLimit))
            ->when($previewLimit === 0, function ($q) {
                $limit = (int) ($this->data['ranking_unique_limit'] ?? 50);
                return $limit > 0 ? $q->limit($limit) : $q;
            });
    }

    private function refreshUniqueTableColumns(bool $notify = true): void
    {
        $connection = (string) ($this->data['ranking_unique_connection'] ?? config('database.default'));
        $table = trim((string) ($this->data['ranking_unique_table'] ?? ''));

        if ($table === '') {
            if ($notify) {
                Notification::make()
                    ->warning()
                    ->title(__('filament/rankings.notifications.unique_missing_table_title'))
                    ->body(__('filament/rankings.notifications.unique_missing_table_message'))
                    ->send();
            }

            return;
        }

        try {
            $columns = DB::connection($connection)
                ->getSchemaBuilder()
                ->getColumnListing($table);

            if ($columns === []) {
                throw new RuntimeException('No columns found for the selected table.');
            }

            $options = [];
            foreach ($columns as $column) {
                if (is_string($column) && $column !== '') {
                    $options[$column] = $column;
                }
            }

            $this->uniqueTableColumns = $options;

            $existingColumns = collect($this->data['ranking_unique_columns'] ?? [])
                ->filter(fn(mixed $entry): bool => is_array($entry) && isset($entry['column']) && is_string($entry['column']));

            $mappedColumns = [];
            $usedColumns = [];

            // Keep existing order for currently selected columns.
            foreach ($existingColumns as $existing) {
                $column = (string) ($existing['column'] ?? '');

                if ($column === '') {
                    continue;
                }

                if (! isset($options[$column]) && ! in_array($column, [self::UNIQUE_JOIN_OUTPUT_COLUMN, self::UNIQUE_POINTS_OUTPUT_COLUMN], true)) {
                    continue;
                }

                $mappedColumns[] = [
                    'column' => $column,
                    'label' => is_string($existing['label'] ?? null) && $existing['label'] !== ''
                        ? $existing['label']
                        : $column,
                ];

                $usedColumns[$column] = true;
            }

            // Only prefill all discovered columns when no saved mapping exists yet.
            if ($mappedColumns === []) {
                foreach (array_keys($options) as $column) {
                    if (isset($usedColumns[$column])) {
                        continue;
                    }

                    $mappedColumns[] = [
                        'column' => $column,
                        'label' => $column,
                    ];
                }
            }

            $this->data['ranking_unique_columns'] = $mappedColumns;
            $this->syncUniqueJoinOutputDisplayColumn();
            $this->syncUniquePointsDisplayColumn();

            $orderBy = (string) ($this->data['ranking_unique_order_by'] ?? '');
            if (! isset($options[$orderBy])) {
                $this->data['ranking_unique_order_by'] = array_key_first($options);
            }

            if ($notify) {
                Notification::make()
                    ->success()
                    ->title(__('filament/rankings.notifications.unique_columns_loaded_title'))
                    ->body(__('filament/rankings.notifications.unique_columns_loaded_message', ['count' => count($options)]))
                    ->send();
            }
        } catch (Throwable $e) {
            if ($notify) {
                Notification::make()
                    ->danger()
                    ->title(__('filament/rankings.notifications.unique_columns_error_title'))
                    ->body($e->getMessage())
                    ->send();
            }

            $this->uniqueTableColumns = [];
        }
    }

    /**
     * @return array<string, string>
     */
    private function getUniqueConnectionOptions(): array
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

            $isSroConnection = Str::startsWith(strtolower($connectionName), 'sro_');
            $isSroDatabase = Str::startsWith(strtolower($databaseName), 'sro_');

            if ($isSroConnection || $isSroDatabase) {
                $options[$connectionName] = $connectionName;
            }
        }

        asort($options);

        return $options;
    }

    /**
     * @return array<string, string>
     */
    private function getUniqueTableOptions(): array
    {
        $connection = (string) ($this->data['ranking_unique_connection'] ?? config('database.default'));

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
            if (! is_string($table) || $table === '') {
                continue;
            }

            $rawName = trim($table);
            $baseName = Str::contains($rawName, '.')
                ? Str::afterLast($rawName, '.')
                : $rawName;

            $baseName = trim($baseName, '[]`"');

            $options[$rawName] = $rawName;
        }

        asort($options);

        return $options;
    }

    /**
     * @return array<string, string>
     */
    private function getUniqueJoinConnectionOptions(): array
    {
        return $this->getUniqueConnectionOptions();
    }

    /**
     * @return array<string, string>
     */
    private function getUniqueJoinTableOptions(): array
    {
        $connection = (string) ($this->data['ranking_unique_join_connection'] ?? config('database.default'));

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
            if (! is_string($table) || $table === '') {
                continue;
            }

            $rawName  = trim($table);
            $baseName = Str::contains($rawName, '.') ? Str::afterLast($rawName, '.') : $rawName;
            $baseName = trim($baseName, '[]`"');

            $options[$rawName] = $rawName;
        }

        asort($options);

        return $options;
    }

    private function refreshUniqueJoinTableColumns(bool $notify = true): void
    {
        $connection = (string) ($this->data['ranking_unique_join_connection'] ?? config('database.default'));
        $table      = trim((string) ($this->data['ranking_unique_join_table'] ?? ''));

        if ($table === '') {
            if ($notify) {
                Notification::make()
                    ->warning()
                    ->title(__('filament/rankings.notifications.join_missing_table_title'))
                    ->body(__('filament/rankings.notifications.join_missing_table_message'))
                    ->send();
            }

            return;
        }

        try {
            $columns = DB::connection($connection)
                ->getSchemaBuilder()
                ->getColumnListing($table);

            if ($columns === []) {
                throw new RuntimeException('No columns found for the selected join table.');
            }

            $options = [];
            foreach ($columns as $column) {
                if (is_string($column) && $column !== '') {
                    $options[$column] = $column;
                }
            }

            $this->uniqueJoinTableColumns = $options;
            $this->syncUniqueJoinOutputDisplayColumn();

            if ($notify) {
                Notification::make()
                    ->success()
                    ->title(__('filament/rankings.notifications.join_columns_loaded_title'))
                    ->body(__('filament/rankings.notifications.join_columns_loaded_message', ['count' => count($options)]))
                    ->send();
            }
        } catch (Throwable $e) {
            if ($notify) {
                Notification::make()
                    ->danger()
                    ->title(__('filament/rankings.notifications.join_columns_error_title'))
                    ->body($e->getMessage())
                    ->send();
            }

            $this->uniqueJoinTableColumns = [];
        }
    }

    private function getRankingSettingsArray(): array
    {
        return [
            'ranking_chars_title'     => Setting::get('ranking_chars_title', 'Character Ranking'),
            'ranking_chars_cache_ttl' => Setting::get('ranking_chars_cache_ttl', 60),
            'ranking_chars_limit'     => Setting::get('ranking_chars_limit', 50),
            'ranking_chars_excluded'  => Setting::get('ranking_chars_excluded', []),
            'ranking_chars_columns'   => Setting::get('ranking_chars_columns', [
                ['column' => 'CharName16', 'label' => 'Character Name'],
                ['column' => 'CurLevel',   'label' => 'Level'],
                ['column' => 'GuildName',  'label' => 'Guild Name'],
                ['column' => 'ItemPoints', 'label' => 'Item Points'],
            ]),
            'ranking_guilds_title'     => Setting::get('ranking_guilds_title', 'Guild Ranking'),
            'ranking_guilds_cache_ttl' => Setting::get('ranking_guilds_cache_ttl', 60),
            'ranking_guilds_limit'     => Setting::get('ranking_guilds_limit', 50),
            'ranking_guilds_excluded'  => Setting::get('ranking_guilds_excluded', []),
            'ranking_guilds_columns'   => Setting::get('ranking_guilds_columns', [
                ['column' => 'Name',       'label' => 'Guild Name'],
                ['column' => 'Lvl',        'label' => 'Guild Level'],
                ['column' => 'LeaderName', 'label' => 'Leader Name'],
                ['column' => 'TotalMember', 'label' => 'Members'],
                ['column' => 'ItemPoints', 'label' => 'Item Points'],
            ]),
            'ranking_unique_title'           => Setting::get('ranking_unique_title', 'Unique Ranking'),
            'ranking_unique_cache_ttl'       => Setting::get('ranking_unique_cache_ttl', 60),
            'ranking_unique_limit'           => Setting::get('ranking_unique_limit', 50),
            'ranking_unique_connection'      => Setting::get('ranking_unique_connection', (string) config('database.default')),
            'ranking_unique_table'           => Setting::get('ranking_unique_table', ''),
            'ranking_unique_order_by'        => Setting::get('ranking_unique_order_by', ''),
            'ranking_unique_order_direction' => Setting::get('ranking_unique_order_direction', 'desc'),
            'ranking_unique_columns'         => Setting::get('ranking_unique_columns', []),
            'ranking_unique_join_enabled'        => Setting::get('ranking_unique_join_enabled', false),
            'ranking_unique_join_connection'     => Setting::get('ranking_unique_join_connection', ''),
            'ranking_unique_join_table'          => Setting::get('ranking_unique_join_table', ''),
            'ranking_unique_join_local_key'      => Setting::get('ranking_unique_join_local_key', ''),
            'ranking_unique_join_foreign_key'    => Setting::get('ranking_unique_join_foreign_key', ''),
            'ranking_unique_join_output_column'  => Setting::get('ranking_unique_join_output_column', ''),
            'ranking_unique_join_output_label'   => Setting::get('ranking_unique_join_output_label', ''),
            'ranking_unique_points_enabled'       => Setting::get('ranking_unique_points_enabled', false),
            'ranking_unique_points_source_column' => Setting::get('ranking_unique_points_source_column', 'MobID'),
            'ranking_unique_points_player_column' => Setting::get('ranking_unique_points_player_column', 'CharID'),
            'ranking_unique_points_map'           => $this->getHydratedUniquePointsMap(),
        ];
    }

    /**
     * @return array<int, array{key: string, id: string, name: string, points: int}>
     */
    private function getHydratedUniquePointsMap(): array
    {
        $defaults = $this->getDefaultUniquePointsRows();
        $savedRaw = Setting::get('ranking_unique_points_map', []);

        if (! is_array($savedRaw) || $savedRaw === []) {
            return $defaults;
        }

        $savedRows = collect($savedRaw)
            ->filter(fn(mixed $row): bool => is_array($row))
            ->values();

        if ($savedRows->isEmpty()) {
            return $defaults;
        }

        $savedByKey = $savedRows
            ->filter(fn(array $row): bool => trim((string) ($row['key'] ?? '')) !== '')
            ->keyBy(fn(array $row): string => trim((string) $row['key']));

        $savedById = $savedRows
            ->filter(fn(array $row): bool => trim((string) ($row['id'] ?? '')) !== '')
            ->keyBy(fn(array $row): string => trim((string) $row['id']));

        $savedByName = $savedRows
            ->filter(fn(array $row): bool => trim((string) ($row['name'] ?? '')) !== '')
            ->keyBy(fn(array $row): string => trim((string) $row['name']));

        $fallbackPointsByIndex = $savedRows
            ->map(fn(array $row): int => max(0, (int) ($row['points'] ?? 0)))
            ->values();

        $result = [];

        foreach ($defaults as $index => $defaultRow) {
            $key = trim((string) ($defaultRow['key'] ?? ''));
            $id = trim((string) ($defaultRow['id'] ?? ''));
            $name = trim((string) ($defaultRow['name'] ?? ''));

            $saved = null;

            if ($key !== '' && $savedByKey->has($key)) {
                $saved = $savedByKey->get($key);
            } elseif ($id !== '' && $savedById->has($id)) {
                $saved = $savedById->get($id);
            } elseif ($name !== '' && $savedByName->has($name)) {
                $saved = $savedByName->get($name);
            }

            $points = is_array($saved)
                ? max(0, (int) ($saved['points'] ?? $defaultRow['points']))
                : max(0, (int) ($fallbackPointsByIndex->get($index, (int) $defaultRow['points'])));

            $result[] = [
                'key' => $key,
                'id' => $id,
                'name' => $name,
                'points' => $points,
            ];
        }

        return $result;
    }

    /**
     * @return array<string, string>
     */
    private function getUniqueDisplayColumnOptions(): array
    {
        $options = $this->uniqueTableColumns;

        $joinEnabled = (bool) ($this->data['ranking_unique_join_enabled'] ?? false);
        $outputColumn = trim((string) ($this->data['ranking_unique_join_output_column'] ?? ''));
        $hasJoinOutputRow = collect($this->data['ranking_unique_columns'] ?? [])
            ->contains(fn(mixed $entry): bool => is_array($entry) && (($entry['column'] ?? null) === self::UNIQUE_JOIN_OUTPUT_COLUMN));

        // Keep the virtual option available while the repeater contains it to avoid transient resets on reorder.
        if (($joinEnabled && $outputColumn !== '') || $hasJoinOutputRow) {
            $options[self::UNIQUE_JOIN_OUTPUT_COLUMN] = $outputColumn !== ''
                ? 'Join Output: ' . $outputColumn
                : 'Join Output';
        }

        $pointsEnabled = (bool) ($this->data['ranking_unique_points_enabled'] ?? false);
        $hasPointsRow = collect($this->data['ranking_unique_columns'] ?? [])
            ->contains(fn(mixed $entry): bool => is_array($entry) && (($entry['column'] ?? null) === self::UNIQUE_POINTS_OUTPUT_COLUMN));

        if ($pointsEnabled || $hasPointsRow) {
            $options[self::UNIQUE_POINTS_OUTPUT_COLUMN] = __('filament/rankings.fields.unique_points_output_column');
        }

        return $options;
    }

    private function syncUniqueJoinOutputDisplayColumn(): void
    {
        $joinEnabled = (bool) ($this->data['ranking_unique_join_enabled'] ?? false);
        $outputColumn = trim((string) ($this->data['ranking_unique_join_output_column'] ?? ''));

        $existing = collect($this->data['ranking_unique_columns'] ?? [])
            ->filter(fn(mixed $entry): bool => is_array($entry))
            ->filter(function (array $entry): bool {
                $column = trim((string) ($entry['column'] ?? ''));
                $label = trim((string) ($entry['label'] ?? ''));

                // Cleanup for accidental blank rows that can appear during drag/reorder edge cases.
                return $column !== '' || $label !== '';
            })
            ->values();

        $this->data['ranking_unique_columns'] = $existing->all();

        if (! $joinEnabled || $outputColumn === '') {
            return;
        }

        if ($existing->contains(fn(mixed $entry): bool => is_array($entry) && ($entry['column'] ?? null) === self::UNIQUE_JOIN_OUTPUT_COLUMN)) {
            return;
        }

        $legacyLabel = trim((string) ($this->data['ranking_unique_join_output_label'] ?? ''));

        $this->data['ranking_unique_columns'] = [
            ...$existing->all(),
            [
                'column' => self::UNIQUE_JOIN_OUTPUT_COLUMN,
                'label' => $legacyLabel !== '' ? $legacyLabel : $outputColumn,
            ],
        ];
    }

    private function syncUniquePointsDisplayColumn(): void
    {
        $pointsEnabled = (bool) ($this->data['ranking_unique_points_enabled'] ?? false);

        $existing = collect($this->data['ranking_unique_columns'] ?? [])
            ->filter(fn(mixed $entry): bool => is_array($entry))
            ->values();

        $withoutPoints = $existing
            ->reject(fn(array $entry): bool => (($entry['column'] ?? null) === self::UNIQUE_POINTS_OUTPUT_COLUMN))
            ->values();

        if (! $pointsEnabled) {
            $this->data['ranking_unique_columns'] = $withoutPoints->all();
            return;
        }

        if ($existing->contains(fn(array $entry): bool => (($entry['column'] ?? null) === self::UNIQUE_POINTS_OUTPUT_COLUMN))) {
            return;
        }

        $this->data['ranking_unique_columns'] = [
            ...$withoutPoints->all(),
            [
                'column' => self::UNIQUE_POINTS_OUTPUT_COLUMN,
                'label' => __('filament/rankings.fields.unique_points_output_label_default'),
            ],
        ];
    }

    private function refreshUniquePointsMapFromConfig(bool $notify = true): void
    {
        $rows = $this->getDefaultUniquePointsRows();
        $current = collect($this->data['ranking_unique_points_map'] ?? [])
            ->filter(fn(mixed $row): bool => is_array($row))
            ->keyBy('key');

        $mapped = [];
        foreach ($rows as $row) {
            $key = (string) ($row['key'] ?? '');
            $currentPoints = (int) (($current->get($key)['points'] ?? null) ?? $row['points']);

            $mapped[] = [
                'key' => $row['key'],
                'id' => $row['id'],
                'name' => $row['name'],
                'points' => max(0, $currentPoints),
            ];
        }

        $this->data['ranking_unique_points_map'] = $mapped;

        if ($notify) {
            Notification::make()
                ->success()
                ->title(__('filament/rankings.notifications.unique_points_loaded_title'))
                ->body(__('filament/rankings.notifications.unique_points_loaded_message', ['count' => count($mapped)]))
                ->send();
        }
    }

    /**
     * @return array<int, array{key: string, id: string, name: string, points: int}>
     */
    private function getDefaultUniquePointsRows(): array
    {
        $uniques = config('silkpanel.uniques', []);
        if (! is_array($uniques)) {
            return [];
        }

        $rows = [];
        foreach ($uniques as $uniqueKey => $config) {
            if (! is_string($uniqueKey) || $uniqueKey === '' || ! is_array($config)) {
                continue;
            }

            $rows[] = [
                'key' => $uniqueKey,
                'id' => (string) ($config['id'] ?? ''),
                'name' => (string) ($config['name'] ?? ''),
                'points' => 1,
            ];
        }

        return $rows;
    }

    /**
     * @param \Illuminate\Support\Collection<int, array<string, mixed>> $pointsRows
     * @return array{0: string, 1: array<int, mixed>}
     */
    private function buildUniquePointsCaseExpression(string $sourceColumn, \Illuminate\Support\Collection $pointsRows, string $baseAlias): array
    {
        $cases = [];
        $bindings = [];
        $quotedSource = $this->quoteSqlIdentifier($sourceColumn);

        foreach ($pointsRows as $row) {
            $key = trim((string) ($row['key'] ?? ''));
            $id = trim((string) ($row['id'] ?? ''));
            $name = trim((string) ($row['name'] ?? ''));
            $points = max(0, (int) ($row['points'] ?? 0));

            $matchValues = array_values(array_unique(array_filter([$key, $id, $name], fn(string $value): bool => $value !== '')));
            if ($matchValues === []) {
                continue;
            }

            $placeholders = implode(', ', array_fill(0, count($matchValues), '?'));
            $cases[] = "WHEN TRY_CAST({$baseAlias}.{$quotedSource} AS NVARCHAR(255)) IN ({$placeholders}) THEN ?";

            foreach ($matchValues as $value) {
                $bindings[] = $value;
            }

            $bindings[] = $points;
        }

        if ($cases === []) {
            return ['', []];
        }

        return ['CASE ' . implode(' ', $cases) . ' ELSE 0 END', $bindings];
    }

    private function quoteSqlIdentifier(string $identifier): string
    {
        return '[' . str_replace(']', ']]', $identifier) . ']';
    }
}
