<?php

namespace App\Filament\Resources\Webmall\RelationManagers;

use App\Enums\SilkTypeEnum;
use App\Enums\SilkTypeIsroEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Table;
use SilkPanel\SilkroadModels\Models\Account\AbstractItemNameDesc;
use SilkPanel\SilkroadModels\Models\Shard\RefObjCommon;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    private ?\Illuminate\Support\Collection $cachedRefObjs = null;

    private function getRefObjs(): \Illuminate\Support\Collection
    {
        if ($this->cachedRefObjs === null) {
            $ids = $this->getOwnerRecord()
                ->items()
                ->pluck('ref_item_id')
                ->unique()
                ->filter()
                ->values()
                ->all();

            $this->cachedRefObjs = RefObjCommon::select(['ID', 'CodeName128', 'TypeID2', 'AssocFileIcon128'])
                ->whereIn('ID', $ids)
                ->get()
                ->keyBy('ID');
        }

        return $this->cachedRefObjs;
    }

    protected static ?string $title = null;

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('filament/webmall.items_title');
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                ViewColumn::make('item_icon')
                    ->label('')
                    ->view('filament.webmall.item-icon-column')
                    ->viewData(['refObjs' => $this->getRefObjs()]),
                TextColumn::make('ref_item_id')
                    ->label(__('filament/webmall.col_item_id'))
                    ->sortable(),
                TextColumn::make('item_name_snapshot')
                    ->label(__('filament/webmall.col_item_name'))
                    ->searchable(),
                TextColumn::make('price_type')
                    ->label(__('filament/webmall.col_price_type'))
                    ->badge()
                    ->formatStateUsing(function ($state): string {
                        return match (true) {
                            $state === 'gold'                                                     => 'Gold',
                            SilkTypeIsroEnum::tryFrom((int) $state) !== null                     => SilkTypeIsroEnum::from((int) $state)->getLabel(),
                            SilkTypeEnum::tryFrom((string) $state) !== null                      => SilkTypeEnum::from((string) $state)->getLabel(),
                            default                                                               => (string) $state,
                        };
                    })
                    ->color(function ($state): string {
                        return match (true) {
                            $state === 'gold'                                                             => 'danger',
                            SilkTypeIsroEnum::tryFrom((int) $state) === SilkTypeIsroEnum::SILK_TYPE_NORMAL  => 'success',
                            SilkTypeIsroEnum::tryFrom((int) $state) === SilkTypeIsroEnum::SILK_TYPE_PREMIUM => 'warning',
                            SilkTypeEnum::tryFrom((string) $state) === SilkTypeEnum::SILK_OWN               => 'success',
                            SilkTypeEnum::tryFrom((string) $state) === SilkTypeEnum::SILK_GIFT              => 'info',
                            SilkTypeEnum::tryFrom((string) $state) === SilkTypeEnum::SILK_POINT             => 'warning',
                            default                                                                         => 'gray',
                        };
                    }),
                TextColumn::make('price_value')
                    ->label(__('filament/webmall.col_price'))
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_hot')
                    ->label(__('filament/webmall.col_hot'))
                    ->boolean(),
                TextColumn::make('available_from')
                    ->label(__('filament/webmall.col_available_from'))
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('available_until')
                    ->label(__('filament/webmall.col_available_until'))
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('stock')
                    ->label(__('filament/webmall.col_stock'))
                    ->formatStateUsing(fn($record) => $record->stock === null ? '∞' : ($record->stock - $record->sold) . ' / ' . $record->stock),
                IconColumn::make('enabled')
                    ->label(__('filament/webmall.field_enabled'))
                    ->boolean(),
                TextColumn::make('order')
                    ->label(__('filament/webmall.col_order'))
                    ->sortable(),
            ])
            ->reorderable('order')
            ->defaultSort('order', 'asc')
            ->headerActions([
                CreateAction::make()
                    ->schema($this->getItemForm()),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                        ->schema($this->getItemForm()),
                    DeleteAction::make(),
                ]),
            ])
            ->emptyStateHeading(__('filament/webmall.empty_items_heading'))
            ->emptyStateDescription(__('filament/webmall.empty_items_desc'));
    }

    private function getItemForm(): array
    {
        return [
            Select::make('ref_item_id')
                ->label(__('filament/webmall.field_item'))
                ->searchable()
                ->required()
                ->live()
                ->getSearchResultsUsing(function (string $search): array {
                    $items = RefObjCommon::where('TypeID1', 3)
                        ->where('CodeName128', 'like', "%{$search}%")
                        ->select(['ID', 'CodeName128', 'NameStrID128'])
                        ->limit(50)
                        ->get();

                    if ($items->isEmpty()) {
                        return [];
                    }

                    $nameStrIds = $items->pluck('NameStrID128')->filter()->unique()->values()->all();
                    $names = resolve(AbstractItemNameDesc::class)->getItemNames($nameStrIds);

                    return $items->mapWithKeys(function ($item) use ($names) {
                        $readableName = $names[$item->NameStrID128] ?? null;
                        $label = $readableName
                            ? "{$readableName} ({$item->CodeName128})"
                            : $item->CodeName128;
                        return [$item->ID => $label];
                    })->toArray();
                })
                ->getOptionLabelUsing(function ($value): ?string {
                    $item = RefObjCommon::select(['ID', 'CodeName128', 'NameStrID128'])->find((int) $value);
                    if (!$item) {
                        return $value;
                    }
                    $names = resolve(AbstractItemNameDesc::class)->getItemNames([$item->NameStrID128]);
                    $readableName = $names[$item->NameStrID128] ?? null;
                    return $readableName
                        ? "{$readableName} ({$item->CodeName128})"
                        : $item->CodeName128;
                })
                ->afterStateUpdated(function ($state, $set): void {
                    if (!$state) {
                        $set('item_name_snapshot', null);
                        $set('_info_req_level', null);
                        $set('_info_price', null);
                        $set('_info_sell_price', null);
                        $set('_info_cost_repair', null);
                        $set('_info_can_trade', null);
                        $set('_info_can_sell', null);
                        $set('_info_can_buy', null);
                        $set('_info_max_stack', null);
                        return;
                    }
                    $item = RefObjCommon::select([
                        'ID',
                        'CodeName128',
                        'NameStrID128',
                        'CanTrade',
                        'CanSell',
                        'CanBuy',
                        'Price',
                        'CostRepair',
                        'SellPrice',
                        'ReqLevel1',
                        'Link',
                    ])->with('getRefObjItem:ID,MaxStack')->find((int) $state);
                    if (!$item) {
                        return;
                    }
                    $names = resolve(AbstractItemNameDesc::class)->getItemNames([$item->NameStrID128]);
                    $readableName = $names[$item->NameStrID128] ?? null;
                    $set('item_name_snapshot', $readableName ?? $item->CodeName128);
                    $set('_info_req_level', $item->ReqLevel1);
                    $set('_info_price', $item->Price);
                    $set('_info_sell_price', $item->SellPrice);
                    $set('_info_cost_repair', $item->CostRepair);
                    $set('_info_can_trade', $item->CanTrade);
                    $set('_info_can_sell', $item->CanSell);
                    $set('_info_can_buy', $item->CanBuy);
                    $set('_info_max_stack', $item->getRefObjItem?->MaxStack ?? 1);
                }),

            Hidden::make('_info_req_level'),
            Hidden::make('_info_price'),
            Hidden::make('_info_sell_price'),
            Hidden::make('_info_cost_repair'),
            Hidden::make('_info_can_trade'),
            Hidden::make('_info_can_sell'),
            Hidden::make('_info_can_buy'),
            Hidden::make('_info_max_stack'),

            Section::make(__('filament/webmall.section_item_info'))
                ->schema([
                    TextEntry::make('_req_level')
                        ->label(__('filament/webmall.info_req_level'))
                        ->state(fn(Get $get): string => (string) ($get('_info_req_level') ?? '—')),
                    TextEntry::make('_price')
                        ->label(__('filament/webmall.info_npc_price'))
                        ->state(fn(Get $get): string => number_format((int) ($get('_info_price') ?? 0))),
                    TextEntry::make('_sell_price')
                        ->label(__('filament/webmall.info_sell_price'))
                        ->state(fn(Get $get): string => number_format((int) ($get('_info_sell_price') ?? 0))),
                    TextEntry::make('_cost_repair')
                        ->label(__('filament/webmall.info_repair_cost'))
                        ->state(fn(Get $get): string => number_format((int) ($get('_info_cost_repair') ?? 0))),
                    TextEntry::make('_can_trade')
                        ->label(__('filament/webmall.info_tradeable'))
                        ->state(fn(Get $get): string => $get('_info_can_trade') ? '✓' : '✗'),
                    TextEntry::make('_can_sell')
                        ->label(__('filament/webmall.info_can_sell'))
                        ->state(fn(Get $get): string => $get('_info_can_sell') ? '✓' : '✗'),
                    TextEntry::make('_can_buy')
                        ->label(__('filament/webmall.info_can_buy'))
                        ->state(fn(Get $get): string => $get('_info_can_buy') ? '✓' : '✗'),
                    TextEntry::make('_max_stack')
                        ->label(__('filament/webmall.info_max_stack'))
                        ->state(fn(Get $get): string => (string) ($get('_info_max_stack') ?? '—')),
                ])
                ->columns(4)
                ->visible(fn(Get $get): bool => (bool) $get('ref_item_id')),

            TextInput::make('item_name_snapshot')
                ->label(__('filament/webmall.field_display_name'))
                ->maxLength(200)
                ->helperText(__('filament/webmall.field_display_name_helper')),

            Section::make()
                ->schema([
                    Select::make('price_type')
                        ->label(__('filament/webmall.field_price_type'))
                        ->options(function (): array {
                            if (config('silkpanel.version') === 'isro') {
                                return collect(SilkTypeIsroEnum::cases())
                                    ->mapWithKeys(fn($c) => [(string) $c->value => $c->getLabel()])
                                    ->toArray() + ['gold' => 'Gold'];
                            }
                            return collect(SilkTypeEnum::cases())
                                ->mapWithKeys(fn($c) => [$c->value => $c->getLabel()])
                                ->toArray() + ['gold' => 'Gold'];
                        })
                        ->required(),

                    TextInput::make('price_value')
                        ->label(__('filament/webmall.field_price'))
                        ->numeric()
                        ->required()
                        ->minValue(1)
                        ->maxValue(4294967295),
                ])
                ->columns(2),

            Section::make()
                ->schema([
                    TextInput::make('order')
                        ->label(__('filament/webmall.field_sort_order'))
                        ->numeric()
                        ->default(0)
                        ->minValue(0),

                    TextInput::make('stock')
                        ->label(__('filament/webmall.field_stock_limit'))
                        ->numeric()
                        ->minValue(1)
                        ->nullable()
                        ->helperText(__('filament/webmall.field_stock_limit_helper')),

                    DateTimePicker::make('available_from')
                        ->label(__('filament/webmall.field_available_from'))
                        ->nullable(),

                    DateTimePicker::make('available_until')
                        ->label(__('filament/webmall.field_available_until'))
                        ->nullable(),

                    Toggle::make('is_hot')
                        ->label(__('filament/webmall.field_is_hot'))
                        ->default(false),

                    Toggle::make('enabled')
                        ->label(__('filament/webmall.field_enabled'))
                        ->default(true),
                ])
                ->columns(2),
        ];
    }
}
