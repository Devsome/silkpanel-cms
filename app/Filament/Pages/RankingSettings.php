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
                                ]),
                        ]),
                ])
                    ->livewireSubmitHandler('save')
                    ->footer([
                        ActionsComponent::make([
                            Action::make('testCharsRanking')
                                ->label(__('filament/rankings.actions.test'))
                                ->icon('heroicon-o-beaker')
                                ->color('gray')
                                ->action(fn() => $this->testCharsRankingQuery()),

                            Action::make('testGuildsRanking')
                                ->label(__('filament/rankings.actions.test_guilds'))
                                ->icon('heroicon-o-beaker')
                                ->color('gray')
                                ->action(fn() => $this->testGuildsRankingQuery()),

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
        ];
    }
}
