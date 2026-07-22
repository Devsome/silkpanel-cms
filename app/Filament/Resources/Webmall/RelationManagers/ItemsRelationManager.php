<?php

namespace App\Filament\Resources\Webmall\RelationManagers;

use App\Enums\WebmallItemTypeEnum;
use App\Enums\SilkTypeEnum;
use App\Enums\SilkTypeIsroEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Placeholder;
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

    /**
     * Load the read-only reference info for an item into the form's hidden fields.
     */
    private function loadItemInfo(int $refItemId, callable $set, bool $updateName): void
    {
        if ($refItemId <= 0) {
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
        ])->with('getRefObjItem:ID,MaxStack')->find($refItemId);

        if (! $item) {
            return;
        }

        if ($updateName) {
            $names = resolve(AbstractItemNameDesc::class)->getItemNames([$item->NameStrID128]);
            $readableName = $names[$item->NameStrID128] ?? null;
            $set('item_name_snapshot', $readableName ?? $item->CodeName128);
        }

        $set('_info_req_level', $item->ReqLevel1);
        $set('_info_price', $item->Price);
        $set('_info_sell_price', $item->SellPrice);
        $set('_info_cost_repair', $item->CostRepair);
        $set('_info_can_trade', $item->CanTrade);
        $set('_info_can_sell', $item->CanSell);
        $set('_info_can_buy', $item->CanBuy);
        $set('_info_max_stack', $item->getRefObjItem?->MaxStack ?? 1);
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
                TextColumn::make('item_type')
                    ->label(__('filament/webmall.col_item_type'))
                    ->badge()
                    ->formatStateUsing(function ($state): string {
                        $type = $state instanceof WebmallItemTypeEnum
                            ? $state
                            : (is_string($state) ? WebmallItemTypeEnum::tryFrom($state) : null);

                        if ($type instanceof WebmallItemTypeEnum) {
                            return $type->getLabel();
                        }

                        return is_scalar($state) ? (string) $state : '-';
                    })
                    ->color(function ($state): string {
                        $type = $state instanceof WebmallItemTypeEnum
                            ? $state
                            : (is_string($state) ? WebmallItemTypeEnum::tryFrom($state) : null);

                        return $type?->getColor() ?? 'gray';
                    }),
                TextColumn::make('custom_database_connection')
                    ->label(__('filament/webmall.col_custom_database'))
                    ->placeholder('-')
                    ->toggleable(),
                TextColumn::make('custom_procedure_name')
                    ->label(__('filament/webmall.col_custom_procedure_name'))
                    ->placeholder('-')
                    ->toggleable(),
                TextColumn::make('price_type')
                    ->label(__('filament/webmall.col_price_type'))
                    ->badge()
                    ->formatStateUsing(function ($state): string {
                        return match (true) {
                            $state === 'gold' => 'Gold',
                            SilkTypeIsroEnum::tryFrom((int) $state) !== null => SilkTypeIsroEnum::from((int) $state)->getLabel(),
                            SilkTypeEnum::tryFrom((string) $state) !== null => SilkTypeEnum::from((string) $state)->getLabel(),
                            default => (string) $state,
                        };
                    })
                    ->color(function ($state): string {
                        return match (true) {
                            $state === 'gold' => 'danger',
                            SilkTypeIsroEnum::tryFrom((int) $state) === SilkTypeIsroEnum::SILK_TYPE_NORMAL => 'success',
                            SilkTypeIsroEnum::tryFrom((int) $state) === SilkTypeIsroEnum::SILK_TYPE_PREMIUM => 'warning',
                            SilkTypeEnum::tryFrom((string) $state) === SilkTypeEnum::SILK_OWN => 'success',
                            SilkTypeEnum::tryFrom((string) $state) === SilkTypeEnum::SILK_GIFT => 'info',
                            SilkTypeEnum::tryFrom((string) $state) === SilkTypeEnum::SILK_POINT => 'warning',
                            default => 'gray',
                        };
                    }),
                TextColumn::make('price_value')
                    ->label(__('filament/webmall.col_price'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('amount')
                    ->label(__('filament/webmall.col_amount'))
                    ->formatStateUsing(fn($state): string => '×' . (int) ($state ?? 1))
                    ->toggleable(),
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
                CreateAction::make()->schema($this->getItemForm()),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()->schema($this->getItemForm()),
                    DeleteAction::make(),
                ]),
            ])
            ->emptyStateHeading(__('filament/webmall.empty_items_heading'))
            ->emptyStateDescription(__('filament/webmall.empty_items_desc'));
    }

    private function getItemForm(): array
    {
        return [
            Select::make('item_type')
                ->label(__('filament/webmall.field_item_type'))
                ->options(collect(WebmallItemTypeEnum::cases())->mapWithKeys(fn(WebmallItemTypeEnum $type) => [$type->value => $type->getLabel()])->all())
                ->default(WebmallItemTypeEnum::REGULAR_ITEM->value)
                ->required()
                ->live()
                ->afterStateUpdated(function ($state, $set): void {
                    if ($state === WebmallItemTypeEnum::CUSTOM_ITEM->value) {
                        $set('ref_item_id', 0);
                        $set('_info_req_level', null);
                        $set('_info_price', null);
                        $set('_info_sell_price', null);
                        $set('_info_cost_repair', null);
                        $set('_info_can_trade', null);
                        $set('_info_can_sell', null);
                        $set('_info_can_buy', null);
                        $set('_info_max_stack', null);
                    } else {
                        $set('custom_image_path', null);
                        $set('custom_database_connection', null);
                        $set('custom_procedure_name', null);
                        $set('custom_parameters', null);
                    }
                }),

            Select::make('ref_item_id')
                ->label(__('filament/webmall.field_item'))
                ->searchable()
                ->required(fn(Get $get): bool => $get('item_type') !== WebmallItemTypeEnum::CUSTOM_ITEM->value)
                ->visible(fn(Get $get): bool => $get('item_type') !== WebmallItemTypeEnum::CUSTOM_ITEM->value)
                ->dehydrated(true)
                ->dehydratedWhenHidden(true)
                ->dehydrateStateUsing(fn($state, Get $get) => $get('item_type') === WebmallItemTypeEnum::CUSTOM_ITEM->value ? 0 : $state)
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
                        $label = $readableName ? "{$readableName} ({$item->CodeName128})" : $item->CodeName128;

                        return [$item->ID => $label];
                    })->toArray();
                })
                ->getOptionLabelUsing(function ($value): ?string {
                    $item = RefObjCommon::select(['ID', 'CodeName128', 'NameStrID128'])->find((int) $value);
                    if (! $item) {
                        return $value;
                    }

                    $names = resolve(AbstractItemNameDesc::class)->getItemNames([$item->NameStrID128]);
                    $readableName = $names[$item->NameStrID128] ?? null;

                    return $readableName ? "{$readableName} ({$item->CodeName128})" : $item->CodeName128;
                })
                ->afterStateHydrated(function ($state, $set): void {
                    // Repopulate the read-only info fields (incl. max stack, which drives
                    // the Amount field's visibility) when editing an existing record.
                    if ($state) {
                        $this->loadItemInfo((int) $state, $set, updateName: false);
                    }
                })
                ->afterStateUpdated(function ($state, $set): void {
                    if (! $state) {
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

                    $this->loadItemInfo((int) $state, $set, updateName: true);
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
                        ->state(fn(Get $get): string => (string) ($get('_info_req_level') ?? '-')),
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
                        ->state(fn(Get $get): string => (string) ($get('_info_max_stack') ?? '-')),
                ])
                ->columns(4)
                ->visible(fn(Get $get): bool => (bool) $get('ref_item_id') && $get('item_type') !== WebmallItemTypeEnum::CUSTOM_ITEM->value),

            TextInput::make('item_name_snapshot')
                ->label(__('filament/webmall.field_display_name'))
                ->required(fn(Get $get): bool => $get('item_type') === WebmallItemTypeEnum::CUSTOM_ITEM->value)
                ->maxLength(200)
                ->helperText(__('filament/webmall.field_display_name_helper')),

            FileUpload::make('custom_image_path')
                ->label(__('filament/webmall.field_custom_image'))
                ->image()
                ->disk('public')
                ->directory('webmall/custom-items')
                ->visibility('public')
                ->required(fn(Get $get): bool => $get('item_type') === WebmallItemTypeEnum::CUSTOM_ITEM->value)
                ->visible(fn(Get $get): bool => $get('item_type') === WebmallItemTypeEnum::CUSTOM_ITEM->value)
                ->helperText(__('filament/webmall.field_custom_image_helper')),

            Select::make('custom_database_connection')
                ->label(__('filament/webmall.field_custom_database'))
                ->options(function (): array {
                    return collect(config('database.connections', []))
                        ->keys()
                        ->mapWithKeys(fn(string $connection) => [$connection => $connection])
                        ->all();
                })
                ->required(fn(Get $get): bool => $get('item_type') === WebmallItemTypeEnum::CUSTOM_ITEM->value)
                ->visible(fn(Get $get): bool => $get('item_type') === WebmallItemTypeEnum::CUSTOM_ITEM->value)
                ->live(),

            Select::make('custom_procedure_name')
                ->label(__('filament/webmall.field_custom_procedure_name'))
                ->options(function (Get $get): array {
                    $connection = (string) ($get('custom_database_connection') ?? '');
                    if ($connection === '') {
                        return [];
                    }

                    return app(\App\Services\ProcedureManager::class)->listProcedureNames($connection);
                })
                ->searchable()
                ->preload()
                ->required(fn(Get $get): bool => $get('item_type') === WebmallItemTypeEnum::CUSTOM_ITEM->value)
                ->visible(fn(Get $get): bool => $get('item_type') === WebmallItemTypeEnum::CUSTOM_ITEM->value)
                ->live(),

            Placeholder::make('custom_procedure_parameters')
                ->label(__('filament/webmall.field_custom_procedure_parameters'))
                ->content(function (Get $get): string {
                    $connection = (string) ($get('custom_database_connection') ?? '');
                    $procedure = (string) ($get('custom_procedure_name') ?? '');

                    if ($connection === '' || $procedure === '') {
                        return __('filament/webmall.field_custom_procedure_parameters_empty');
                    }

                    $parameters = app(\App\Services\ProcedureManager::class)->listProcedureParameters($connection, $procedure);

                    if ($parameters === []) {
                        return __('filament/webmall.field_custom_procedure_parameters_unknown');
                    }

                    return implode(', ', $parameters);
                })
                ->visible(fn(Get $get): bool => $get('item_type') === WebmallItemTypeEnum::CUSTOM_ITEM->value),

            KeyValue::make('custom_parameters')
                ->label(__('filament/webmall.field_custom_parameters'))
                ->keyLabel(__('filament/webmall.field_custom_parameters_key'))
                ->valueLabel(__('filament/webmall.field_custom_parameters_value'))
                ->helperText(__('filament/webmall.field_custom_parameters_helper'))
                ->visible(fn(Get $get): bool => $get('item_type') === WebmallItemTypeEnum::CUSTOM_ITEM->value),

            Placeholder::make('custom_parameter_tokens')
                ->label(__('filament/webmall.field_custom_parameter_tokens'))
                ->content(__('filament/webmall.field_custom_parameter_tokens_helper'))
                ->visible(fn(Get $get): bool => $get('item_type') === WebmallItemTypeEnum::CUSTOM_ITEM->value),

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

                    TextInput::make('amount')
                        ->label(__('filament/webmall.field_amount'))
                        ->numeric()
                        ->default(1)
                        ->minValue(1)
                        ->maxValue(fn(Get $get): ?int => $get('item_type') === WebmallItemTypeEnum::CUSTOM_ITEM->value
                            ? null
                            : ((int) ($get('_info_max_stack') ?? 1)))
                        ->required(fn(Get $get): bool => $get('item_type') === WebmallItemTypeEnum::CUSTOM_ITEM->value
                            || (int) ($get('_info_max_stack') ?? 1) > 1)
                        ->helperText(__('filament/webmall.field_amount_helper'))
                        ->visible(fn(Get $get): bool => $get('item_type') === WebmallItemTypeEnum::CUSTOM_ITEM->value
                            || (int) ($get('_info_max_stack') ?? 1) > 1),

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
