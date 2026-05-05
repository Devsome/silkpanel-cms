<?php

namespace App\Filament\Resources\Characters\Pages;

use App\Enums\CharacterAvatarMapEnum;
use App\Filament\Resources\Characters\CharacterResource;
use App\Services\SilkroadItemService;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\IconSize;
use Filament\Support\Enums\Size;
use Illuminate\Support\Carbon;
use Livewire\Attributes\On;
use SilkPanel\SilkroadModels\Models\Account\AbstractItemNameDesc;
use SilkPanel\SilkroadModels\Models\Shard\Inventory;
use SilkPanel\SilkroadModels\Models\Shard\RefObjCommon;

class ViewCharacter extends ViewRecord
{
    protected static string $resource = CharacterResource::class;

    private const INVENTORY_MAX_SLOTS = 108;
    private const INVENTORY_MIN_SLOTS = 13;
    private const INVENTORY_NOT_SLOTS = [8];

    private const EQUIPMENT_MAX_SLOTS = 12;
    private const EQUIPMENT_MIN_SLOTS = 0;
    private const EQUIPMENT_NOT_SLOTS = [8];

    private function getInventoryViewData(): array
    {
        return [
            'inventory' => $this->record->getCharInventorySet(
                self::INVENTORY_MAX_SLOTS,
                self::INVENTORY_MIN_SLOTS,
                self::INVENTORY_NOT_SLOTS,
            ),
        ];
    }

    private function getEquipmentViewData(): array
    {
        return [
            'equipment' => $this->record->getCharInventorySet(
                self::EQUIPMENT_MAX_SLOTS,
                self::EQUIPMENT_MIN_SLOTS,
                self::EQUIPMENT_NOT_SLOTS,
            ),
            'characterImage2d' => $this->getCharacter2dImageUrl(),
        ];
    }

    private function getAvatarViewData(): array
    {
        return [
            'avatar' => $this->record->getCharAvatarSet(),
            'characterImage2d' => $this->getCharacter2dImageUrl(),
        ];
    }

    #[On('deleteInventoryItem')]
    public function deleteInventoryItem(int $slot): void
    {
        $isIsro = config('silkpanel.version') === 'isro';

        /** @var SilkroadItemService $service */
        $service = resolve(SilkroadItemService::class);

        $result = $isIsro
            ? $service->deleteItemIsro(ownerName: $this->record->CharName16, targetStorage: 0, slot: $slot)
            : $service->deleteItemVsro(ownerName: $this->record->CharName16, targetStorage: 0, slot: $slot);

        Inventory::forgetInventoryCache(
            $this->record->CharID,
            self::INVENTORY_MAX_SLOTS,
            self::INVENTORY_MIN_SLOTS,
            self::INVENTORY_NOT_SLOTS,
        );

        $this->record = $this->record->fresh();

        if ($result['success']) {
            Notification::make()
                ->title(__('filament/characters.view.delete_item_success_title'))
                ->body(__('filament/characters.view.delete_item_success_message', ['slot' => $slot]))
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title(__('filament/characters.view.delete_item_error_title'))
                ->body(__('filament/characters.view.delete_item_error_message', ['code' => $result['return_code']]))
                ->danger()
                ->send();
        }
    }

    protected function getHeaderActions(): array
    {
        $isIsro = config('silkpanel.version') === 'isro';

        return [
            Action::make('addItem')
                ->label(__('filament/characters.view.add_item'))
                ->icon('heroicon-o-squares-plus')
                ->color('gray')
                ->modalHeading(__('filament/characters.view.add_item_modal_heading'))
                ->modalDescription(__('filament/characters.view.add_item_modal_description'))
                ->schema([
                    Select::make('ref_item_id')
                        ->label(__('filament/characters.view.add_item_search'))
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
                            $item = RefObjCommon::select(['ID', 'CodeName128', 'NameStrID128'])
                                ->find((int) $value);

                            if (!$item) {
                                return $value;
                            }

                            $names = resolve(AbstractItemNameDesc::class)
                                ->getItemNames([$item->NameStrID128]);

                            $readableName = $names[$item->NameStrID128] ?? null;

                            return $readableName
                                ? "{$readableName} ({$item->CodeName128})"
                                : $item->CodeName128;
                        })
                        ->afterStateUpdated(function (?string $state, Set $set): void {
                            if (!$state) {
                                $set('item_type1', null);
                                $set('item_type2', null);
                                $set('max_stack', null);
                                $set('can_trade', null);
                                $set('can_sell', null);
                                $set('can_buy', null);
                                $set('item_price', null);
                                $set('cost_repair', null);
                                $set('sell_price', null);
                                $set('req_level1', null);
                                return;
                            }

                            $item = RefObjCommon::select([
                                'ID',
                                'TypeID1',
                                'TypeID2',
                                'CanTrade',
                                'CanSell',
                                'CanBuy',
                                'Price',
                                'CostRepair',
                                'SellPrice',
                                'ReqLevel1',
                                'Link',
                            ])->with('getRefObjItem:ID,MaxStack')->find((int) $state);

                            $set('item_type1', $item?->TypeID1);
                            $set('item_type2', $item?->TypeID2);
                            $set('max_stack', $item?->getRefObjItem?->MaxStack ?? 1);
                            $set('can_trade', $item?->CanTrade);
                            $set('can_sell', $item?->CanSell);
                            $set('can_buy', $item?->CanBuy);
                            $set('item_price', $item?->Price);
                            $set('cost_repair', $item?->CostRepair);
                            $set('sell_price', $item?->SellPrice);
                            $set('req_level1', $item?->ReqLevel1);
                        }),

                    Hidden::make('item_type1'),
                    Hidden::make('item_type2'),
                    Hidden::make('max_stack'),

                    Section::make(__('filament/characters.view.add_item_info_title'))
                        ->schema([
                            TextEntry::make('req_level1_display')
                                ->label(__('filament/characters.view.add_item_info_req_level'))
                                ->state(fn(Get $get): string => (string) ($get('req_level1') ?? '—')),
                            TextEntry::make('item_price_display')
                                ->label(__('filament/characters.view.add_item_info_price'))
                                ->state(fn(Get $get): string => number_format((int) ($get('item_price') ?? 0))),
                            TextEntry::make('sell_price_display')
                                ->label(__('filament/characters.view.add_item_info_sell_price'))
                                ->state(fn(Get $get): string => number_format((int) ($get('sell_price') ?? 0))),
                            TextEntry::make('cost_repair_display')
                                ->label(__('filament/characters.view.add_item_info_cost_repair'))
                                ->state(fn(Get $get): string => number_format((int) ($get('cost_repair') ?? 0))),
                            TextEntry::make('can_trade_display')
                                ->label(__('filament/characters.view.add_item_info_can_trade'))
                                ->state(fn(Get $get): string => $get('can_trade') ? '✓' : '✗'),
                            TextEntry::make('can_sell_display')
                                ->label(__('filament/characters.view.add_item_info_can_sell'))
                                ->state(fn(Get $get): string => $get('can_sell') ? '✓' : '✗'),
                            TextEntry::make('can_buy_display')
                                ->label(__('filament/characters.view.add_item_info_can_buy'))
                                ->state(fn(Get $get): string => $get('can_buy') ? '✓' : '✗'),
                            TextEntry::make('max_stack_display')
                                ->label(__('filament/characters.view.add_item_info_max_stack'))
                                ->state(fn(Get $get): string => (string) ($get('max_stack') ?? '—')),
                        ])
                        ->columns(4)
                        ->visible(fn(Get $get): bool => (bool) $get('ref_item_id')),

                    TextInput::make('opt_level')
                        ->label(__('filament/characters.view.add_item_opt_level'))
                        ->helperText(__('filament/characters.view.add_item_opt_level_helper'))
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(15)
                        ->default(0)
                        ->visible(fn(Get $get): bool => (int) $get('item_type1') === 3 && (int) $get('item_type2') === 1),

                    TextInput::make('variance')
                        ->label(__('filament/characters.view.add_item_variance'))
                        ->helperText(__('filament/characters.view.add_item_variance_helper'))
                        ->numeric()
                        ->minValue(0)
                        ->default(null)
                        ->visible(fn(Get $get): bool => $isIsro && (int) $get('item_type1') === 3 && (int) $get('item_type2') === 1),

                    TextInput::make('data')
                        ->label(__('filament/characters.view.add_item_quantity'))
                        ->helperText(fn(Get $get): string => __('filament/characters.view.add_item_quantity_helper') . ' (Max: ' . ($get('max_stack') ?? 1) . ')')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(fn(Get $get): int => max(1, (int) ($get('max_stack') ?? 1)))
                        ->default(1)
                        ->visible(fn(Get $get): bool => (int) $get('item_type1') !== 3 || (int) $get('item_type2') !== 1),
                ])
                ->action(function ($record, array $data) use ($isIsro): void {
                    /** @var SilkroadItemService $service */
                    $service = resolve(SilkroadItemService::class);

                    $refItemId  = (int) $data['ref_item_id'];
                    $optLevel   = isset($data['opt_level']) ? (int) $data['opt_level'] : 0;
                    $quantity   = isset($data['data']) ? (int) $data['data'] : 1;
                    $variance   = isset($data['variance']) && $data['variance'] !== '' ? (int) $data['variance'] : null;

                    if ($isIsro) {
                        $result = $service->addItemIsro(
                            charName: null,
                            charId: $record->CharID,
                            codeName: null,
                            refItemId: $refItemId,
                            data: $quantity,
                            optLevel: $optLevel,
                            variance: $variance,
                        );
                    } else {
                        $result = $service->addItemVsro(
                            charName: null,
                            charId: $record->CharID,
                            codeName: null,
                            refItemId: $refItemId,
                            data: $quantity,
                            optLevel: $optLevel,
                        );
                    }

                    if ($result['success']) {
                        Notification::make()
                            ->title(__('filament/characters.view.add_item_success_title'))
                            ->body(__('filament/characters.view.add_item_success_message', [
                                'destination' => $result['destination'],
                                'slot'        => $result['slot'],
                            ]))
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title(__('filament/characters.view.add_item_error_title'))
                            ->body(__('filament/characters.view.add_item_error_message', [
                                'code' => $result['return_code'],
                            ]))
                            ->danger()
                            ->send();
                    }
                }),
            Action::make('users')
                ->label(__('filament/characters.view.users'))
                ->url(fn($record) => route('filament.admin.resources.users.edit', $record->getAccountUser->getWebAccountUser->id))
                ->color('gray'),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('filament/characters.section.character_title'))
                            ->description(__('filament/characters.section.character_information_description'))
                            ->schema([
                                TextEntry::make('CharID')
                                    ->label(__('filament/characters.view.charid')),
                                TextEntry::make('CharName16')
                                    ->label(__('filament/characters.view.charname')),
                                TextEntry::make('CurLevel')
                                    ->label(__('filament/characters.view.curlevel')),
                                TextEntry::make('Strength')
                                    ->label(__('filament/characters.view.strength')),
                                TextEntry::make('Intellect')
                                    ->label(__('filament/characters.view.intellect')),
                                TextEntry::make('current_percentage')
                                    ->state(function ($record) {
                                        return $record->getCharRefLevelExperience() . '%';
                                    })
                                    ->label(__('filament/characters.view.experience')),
                                TextEntry::make('RemainGold')
                                    ->label(__('filament/characters.view.remaingold')),
                                TextEntry::make('RemainSkillPoint')
                                    ->label(__('filament/characters.view.remainskillpoint')),
                                TextEntry::make('guild.Name')
                                    ->label(__('filament/characters.view.guild'))
                                    ->icon('heroicon-o-arrow-top-right-on-square')
                                    ->iconPosition(IconPosition::After)
                                    ->iconColor('primary')
                                    ->url(fn($record) => $record->guild
                                        ? route('filament.admin.resources.guilds.view', $record->guild->ID)
                                        : null)
                                    ->openUrlInNewTab(true)
                                    ->visible(fn($record) => $record->guild && $record->guild->ID !== 0),
                                TextEntry::make('HP')
                                    ->label(__('filament/characters.view.hp'))
                                    ->columnStart(1),
                                TextEntry::make('MP')
                                    ->label(__('filament/characters.view.mp')),
                                IconEntry::make('Deleted')
                                    ->label(__('filament/characters.view.deleted'))
                                    ->true('heroicon-o-trash', 'danger')
                                    ->false('heroicon-o-trash', 'gray')
                                    ->size(IconSize::Medium)
                                    ->hidden(fn($record) => !$record->Deleted),
                            ])->columns(3),
                        Tabs::make('Tabs')
                            ->tabs([
                                Tab::make(__('filament/characters.inventory.title'))
                                    ->schema([
                                        Action::make('clearInventoryCache')
                                            ->label(__('filament/characters.view.action_clear_cache'))
                                            ->icon('heroicon-m-arrow-path')
                                            ->color('gray')
                                            ->size(Size::Small)
                                            ->requiresConfirmation()
                                            ->extraAttributes([
                                                'class' => 'float-right',
                                            ])
                                            ->action(function ($record): void {
                                                Inventory::forgetInventoryCache(
                                                    $record->CharID,
                                                    self::INVENTORY_MAX_SLOTS,
                                                    self::INVENTORY_MIN_SLOTS,
                                                    self::INVENTORY_NOT_SLOTS,
                                                );

                                                // Ensure a fresh model instance is used on the next Livewire render.
                                                $this->record = $record->fresh();

                                                Notification::make()
                                                    ->title(__('filament/characters.notifications.cache_title'))
                                                    ->body(__('filament/characters.notifications.cache_message'))
                                                    ->success()
                                                    ->send();
                                            }),
                                        ViewEntry::make('inventory')
                                            ->view('filament.characters.partials.inventory')
                                            ->label(__('filament/characters.inventory.title'))
                                            ->viewData(fn() => $this->getInventoryViewData())
                                            ->columnSpanFull(),
                                    ]),
                                Tab::make(__('filament/characters.equipment.title'))
                                    ->schema([
                                        ViewEntry::make('equipment')
                                            ->view('filament.characters.partials.equipment')
                                            ->label(__('filament/characters.equipment.title'))
                                            ->viewData(fn() => $this->getEquipmentViewData())
                                            ->columnSpanFull(),
                                    ]),
                                Tab::make(__('filament/characters.avatar.title'))
                                    ->schema([
                                        ViewEntry::make('avatar')
                                            ->view('filament.characters.partials.avatar')
                                            ->label(__('filament/characters.avatar.title'))
                                            ->viewData(fn() => $this->getAvatarViewData())
                                    ]),
                            ])
                            ->columnSpanFull(),

                    ])->columnSpan(['lg' => 3]),
                Group::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                IconEntry::make('isOnline')
                                    ->label(__('filament/characters.view.online'))
                                    ->true('heroicon-m-check-circle', 'success')
                                    ->false('heroicon-m-x-circle', 'danger')
                                    ->extraAttributes([
                                        'class' => 'animate-pulse',
                                    ])
                                    ->size(IconSize::Medium),
                                TextEntry::make('LastLogout')
                                    ->label(__('filament/characters.view.lastlogout'))
                                    ->state(fn($record) => Carbon::parse($record->LastLogout)?->isFuture() ? __('filament/characters.view.last_never') : Carbon::parse($record->LastLogout)?->diffForHumans()),
                            ]),
                        Section::make(__('filament/characters.section.position_title'))
                            ->description(__('filament/characters.section.position_information_description'))
                            ->schema([
                                TextEntry::make('PosX')
                                    ->label(__('filament/characters.view.pos_x')),
                                TextEntry::make('PosY')
                                    ->label(__('filament/characters.view.pos_y')),
                                TextEntry::make('PosZ')
                                    ->label(__('filament/characters.view.pos_z')),
                                TextEntry::make('LatestRegion')
                                    ->label(__('filament/characters.view.latest_region')),
                            ])->footerActions([
                                Action::make('unstuck')
                                    ->label(__('filament/characters.view.unstuck'))
                                    ->schema([
                                        Select::make('unstuck_position')
                                            ->label(__('filament/characters.view.unstuck_position'))
                                            ->helperText(__('filament/characters.view.unstuck_position_helper'))
                                            ->options([
                                                'jangan' => 'Jangan',
                                                'donwhang' => 'Donwhang',
                                                'hotan' => 'Hotan',
                                                'samarkand' => 'Samarkand',
                                                'constantinople' => 'Constantinople',
                                            ])
                                            ->required()
                                    ])
                                    ->color('gray')
                                    ->requiresConfirmation()
                                    ->modalHeading(__('filament/characters.view.unstuck_modal_heading'))
                                    ->modalDescription(__('filament/characters.view.unstuck_modal_description'))
                                    ->visible(fn($record) => !$record->isOnline && !$record->hasJobSuit)
                                    ->action(function ($record, $data) {
                                        $position = $data['unstuck_position'] ?? 'default';
                                        if ($record->IsOnline) {
                                            Notification::make()
                                                ->title(__('filament/characters.view.unstuck_error_title'))
                                                ->body(__('filament/characters.view.unstuck_error_online_message'))
                                                ->danger()
                                                ->send();
                                            return;
                                        }
                                        if ($record->HasJobSuit) {
                                            Notification::make()
                                                ->title(__('filament/characters.view.unstuck_error_title'))
                                                ->body(__('filament/characters.view.unstuck_error_job_suit_message'))
                                                ->danger()
                                                ->send();
                                            return;
                                        }
                                        $record->unstuckCharacter($position);
                                        Notification::make()
                                            ->title(__('filament/characters.view.unstuck_success_title'))
                                            ->body(__('filament/characters.view.unstuck_success_message'))
                                            ->success()
                                            ->send();
                                    }),
                            ])
                            ->columns(2),

                        Section::make(__('filament/characters.section.job_title'))
                            ->description(__('filament/characters.section.job_information_description'))
                            ->schema([
                                TextEntry::make('NickName16')
                                    ->label(__('filament/characters.view.nickname')),
                                TextEntry::make('JobPenaltyTime')
                                    ->label(__('filament/characters.view.jobpenaltytime')),
                                ViewEntry::make('job-information')
                                    ->view('filament.characters.partials.job-information')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                    ])->columnSpan(['lg' => 2]),
            ])->columns(5);
    }

    private function getCharacter2dImageUrl(): string
    {
        $characterId = (int) ($this->record->RefObjID ?? 0);
        $isIsro = str_contains($this->record::class, 'ISRO');
        $mappedCharacterId = $isIsro
            ? CharacterAvatarMapEnum::mapIsroToVsro($characterId)
            : $characterId;

        $mappedPath = 'images/silkroad/chars_2d/' . $mappedCharacterId . '.png';

        if (file_exists(public_path($mappedPath))) {
            return asset($mappedPath);
        }

        $fallbackPath = 'images/silkroad/chars_2d/' . $characterId . '.png';

        if (file_exists(public_path($fallbackPath))) {
            return asset($fallbackPath);
        }

        return asset('images/silkroad/icon_default.png');
    }
}
