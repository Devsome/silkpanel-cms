<?php

namespace App\Livewire;

use App\Enums\MarketListingStatusEnum;
use App\Enums\SilkTypeEnum;
use App\Enums\SilkTypeIsroEnum;
use App\Enums\WebStorageSourceTypeEnum;
use App\Models\MarketListing;
use App\Models\Setting;
use App\Models\WebStorage;
use App\Services\ItemTooltipService;
use App\Services\MarketListingService;
use App\Services\MarketPurchaseService;
use App\Services\WebStorageService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use SilkPanel\SilkroadModels\Models\Shard\Inventory;
use Throwable;

class WebMarket extends Component
{
    use WithPagination, WithoutUrlPagination;

    // Tab state: marketplace, storage, listings
    public string $activeTab = 'marketplace';

    // Character selection
    public ?int $selectedCharId = null;
    public string $selectedCharName = '';

    // Marketplace filters
    public string $searchQuery = '';
    public string $filterPriceType = '';
    public int $filterOptLevel = 0;
    public string $sortBy = 'newest';

    // Modals
    public bool $showBuyModal = false;
    public ?int $buyListingId = null;

    public bool $showSellModal = false;
    public ?int $sellWebStorageId = null;
    public string $sellPriceType = 'gold';
    public string $sellPriceAmount = '';
    public int $sellDurationHours = 24;
    public string $sellDescription = '';

    public bool $showTransferModal = false;
    public ?int $transferSlot = null;
    public string $transferSourceType = 'inventory';

    public bool $showReturnModal = false;
    public ?int $returnWebStorageId = null;

    public bool $showCancelModal = false;
    public ?int $cancelListingId = null;

    protected WebStorageService $webStorageService;
    protected MarketListingService $listingService;
    protected MarketPurchaseService $purchaseService;

    public function boot(
        WebStorageService $webStorageService,
        MarketListingService $listingService,
        MarketPurchaseService $purchaseService,
    ): void {
        $this->webStorageService = $webStorageService;
        $this->listingService = $listingService;
        $this->purchaseService = $purchaseService;
    }

    public function mount(): void
    {
        $first = $this->getCharacters()->first();
        if ($first) {
            $this->selectedCharId = $first->CharID;
            $this->selectedCharName = $first->CharName16;
        }

        $defaultDuration = (int) Setting::get('web_market_default_duration_hours', 24);
        $this->sellDurationHours = $defaultDuration;

        $isIsro = config('silkpanel.version') === 'isro';
        $this->sellPriceType = $isIsro ? '1' : 'gold';
    }

    public function selectCharacter(int $charId, string $charName): void
    {
        $this->selectedCharId = $charId;
        $this->selectedCharName = $charName;
        $this->burstInventoryCache($charId);
        $this->resetPage();
        $this->closeAllModals();
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        if ($tab === 'storage' && $this->selectedCharId) {
            $this->burstInventoryCache($this->selectedCharId);
        }
        $this->resetPage();
        $this->closeAllModals();
    }

    private function burstInventoryCache(int $charId): void
    {
        // Only _Inventory (bag) is cached; _Chest (character storage) is queried raw.
        Inventory::forgetInventoryCache($charId, 99, 13, null);
    }

    // ───── Marketplace Actions ─────

    public function openBuyModal(int $listingId): void
    {
        $listing = MarketListing::find($listingId);
        if (! $listing || ! $listing->isActive()) {
            session()->flash('error', __('web-market.error.listing_not_available'));
            return;
        }

        if ($listing->user_id === Auth::id()) {
            session()->flash('error', __('web-market.error.cannot_buy_own'));
            return;
        }

        $this->buyListingId = $listingId;
        $this->showBuyModal = true;
    }

    public function confirmBuy(): void
    {
        if (! $this->buyListingId || ! $this->selectedCharId) {
            $this->closeAllModals();
            return;
        }

        $user = Auth::user();
        $listing = MarketListing::find($this->buyListingId);

        if (! $listing) {
            session()->flash('error', __('web-market.error.listing_not_found'));
            $this->closeAllModals();
            return;
        }

        $result = $this->purchaseService->purchase(
            buyer: $user,
            buyerCharId: $this->selectedCharId,
            buyerCharName: $this->selectedCharName,
            listing: $listing,
        );

        $this->closeAllModals();

        if ($result['success']) {
            $itemName = app(ItemTooltipService::class)->getItemName((int) $listing->item_id64)
                ?: $listing->item_name;
            session()->flash('success', __('web-market.success.purchased', ['item' => $itemName]));
        } else {
            session()->flash('error', $this->resolveError($result['error']));
        }
    }

    // ───── Web Storage Transfer Actions ─────

    public function openTransferModal(int $slot, string $sourceType): void
    {
        if (! (bool) Setting::get('web_storage_enabled', false)) {
            session()->flash('error', __('web-market.error.web_storage_disabled'));
            return;
        }

        $this->transferSlot = $slot;
        $this->transferSourceType = $sourceType;
        $this->showTransferModal = true;
    }

    public function confirmTransfer(): void
    {
        if (! $this->selectedCharId || $this->transferSlot === null) {
            $this->closeAllModals();
            return;
        }

        $user = Auth::user();
        $sourceType = WebStorageSourceTypeEnum::tryFrom($this->transferSourceType) ?? WebStorageSourceTypeEnum::INVENTORY;

        $result = $this->webStorageService->transferToWebStorage(
            user: $user,
            charId: $this->selectedCharId,
            charName: $this->selectedCharName,
            slot: $this->transferSlot,
            sourceType: $sourceType,
        );

        $this->closeAllModals();

        if ($result['success']) {
            session()->flash('success', __('web-market.success.transferred_to_storage'));
        } else {
            session()->flash('error', $this->resolveError($result['error']));
        }
    }

    public function openReturnModal(int $webStorageId): void
    {
        $this->returnWebStorageId = $webStorageId;
        $this->showReturnModal = true;
    }

    public function confirmReturn(): void
    {
        if (! $this->returnWebStorageId || ! $this->selectedCharId) {
            $this->closeAllModals();
            return;
        }

        $user = Auth::user();
        $item = WebStorage::find($this->returnWebStorageId);

        if (! $item || $item->user_id !== $user->id) {
            session()->flash('error', __('web-market.error.item_not_found'));
            $this->closeAllModals();
            return;
        }

        $result = $this->webStorageService->transferFromWebStorage(
            user: $user,
            charId: $this->selectedCharId,
            charName: $this->selectedCharName,
            webStorageItem: $item,
            targetType: WebStorageSourceTypeEnum::INVENTORY,
        );

        $this->closeAllModals();

        if ($result['success']) {
            session()->flash('success', __('web-market.success.transferred_from_storage'));
        } else {
            session()->flash('error', $this->resolveError($result['error']));
        }
    }

    // ───── Listing Actions ─────

    public function openSellModal(int $webStorageId): void
    {
        if (! (bool) Setting::get('marketplace_enabled', false)) {
            session()->flash('error', __('web-market.error.marketplace_disabled'));
            return;
        }

        $item = WebStorage::find($webStorageId);
        if (! $item || $item->user_id !== Auth::id()) {
            session()->flash('error', __('web-market.error.item_not_found'));
            return;
        }

        if ($item->isListed()) {
            session()->flash('error', __('web-market.error.item_already_listed'));
            return;
        }

        $this->sellWebStorageId = $webStorageId;
        $this->sellPriceAmount = '';
        $this->showSellModal = true;
    }

    public function confirmSell(): void
    {
        if (! $this->sellWebStorageId || ! $this->selectedCharId) {
            $this->closeAllModals();
            return;
        }

        $priceAmount = (int) $this->sellPriceAmount;
        if ($priceAmount < 1) {
            $this->addError('sellPriceAmount', __('web-market.error.price_invalid'));
            return;
        }

        $user = Auth::user();
        $item = WebStorage::find($this->sellWebStorageId);

        if (! $item || $item->user_id !== $user->id) {
            session()->flash('error', __('web-market.error.item_not_found'));
            $this->closeAllModals();
            return;
        }

        $result = $this->listingService->createListing(
            user: $user,
            charId: $this->selectedCharId,
            webStorageItem: $item,
            priceType: $this->sellPriceType,
            priceAmount: $priceAmount,
            durationHours: $this->sellDurationHours,
            description: blank($this->sellDescription) ? null : $this->sellDescription,
        );

        $this->closeAllModals();

        if ($result['success']) {
            session()->flash('success', __('web-market.success.listing_created'));
        } else {
            session()->flash('error', $this->resolveError($result['error']));
        }
    }

    public function openCancelModal(int $listingId): void
    {
        $this->cancelListingId = $listingId;
        $this->showCancelModal = true;
    }

    public function confirmCancelListing(): void
    {
        if (! $this->cancelListingId) {
            $this->closeAllModals();
            return;
        }

        $user = Auth::user();
        $listing = MarketListing::find($this->cancelListingId);

        if (! $listing || $listing->user_id !== $user->id) {
            session()->flash('error', __('web-market.error.listing_not_found'));
            $this->closeAllModals();
            return;
        }

        $result = $this->listingService->cancelListing($user, $listing);
        $this->closeAllModals();

        if ($result['success']) {
            session()->flash('success', __('web-market.success.listing_cancelled'));
        } else {
            session()->flash('error', $this->resolveError($result['error']));
        }
    }

    private function closeAllModals(): void
    {
        $this->showBuyModal = false;
        $this->showSellModal = false;
        $this->showTransferModal = false;
        $this->showReturnModal = false;
        $this->showCancelModal = false;
        $this->buyListingId = null;
        $this->sellWebStorageId = null;
        $this->returnWebStorageId = null;
        $this->cancelListingId = null;
        $this->transferSlot = null;
        $this->resetErrorBag();
    }

    private function resolveError(?string $error): string
    {
        $key = 'web-market.error.' . ($error ?? 'unexpected_error');
        $translated = __($key);

        return $translated === $key
            ? __('web-market.error.unexpected_error')
            : $translated;
    }

    private function getCharacters(): \Illuminate\Support\Collection
    {
        try {
            return Auth::user()
                ->shardUsers()
                ->get()
                ->filter(fn($c) => $c->CharID != 0 && $c->CharName16 !== 'dummy')
                ->sortByDesc('CurLevel')
                ->values();
        } catch (Throwable) {
            return collect();
        }
    }

    public function render()
    {
        $user = Auth::user();
        $isIsro = config('silkpanel.version') === 'isro';
        $characters = $this->getCharacters();

        $requireLogout = (bool) Setting::get('web_market_require_logout', false);
        $selectedCharOnline = false;
        if ($requireLogout && $this->selectedCharId) {
            try {
                $selectedChar = $user->shardUsers()->wherePivot('CharID', $this->selectedCharId)->first();
                $selectedCharOnline = (bool) ($selectedChar?->isOnline ?? false);
            } catch (Throwable) {
                $selectedCharOnline = false;
            }
        }

        // ── Marketplace ──
        $listingsQuery = MarketListing::active()
            ->where('user_id', '!=', $user->id)
            ->with('user');

        if ($this->searchQuery !== '') {
            $listingsQuery->where('item_name', 'like', '%' . $this->searchQuery . '%');
        }
        if ($this->filterPriceType !== '') {
            if ($this->filterPriceType === 'gold') {
                $listingsQuery->where('price_type', 'gold');
            } else {
                $listingsQuery->where('price_type', '!=', 'gold');
            }
        }
        if ($this->filterOptLevel > 0) {
            $listingsQuery->where('opt_level', '>=', $this->filterOptLevel);
        }

        $listingsQuery = match ($this->sortBy) {
            'price_asc' => $listingsQuery->orderBy('price_amount'),
            'price_desc' => $listingsQuery->orderByDesc('price_amount'),
            'expires_soon' => $listingsQuery->orderBy('expires_at'),
            default => $listingsQuery->orderByDesc('created_at'),
        };

        $listings = $this->activeTab === 'marketplace'
            ? $listingsQuery->paginate(20)
            : collect();

        // ── Web Storage ──
        $webStorage = $this->activeTab === 'storage'
            ? WebStorage::where('user_id', $user->id)->latest()->get()
            : collect();

        $inventoryItems = collect();
        $storageItems = collect();

        if ($this->activeTab === 'storage' && $this->selectedCharId) {
            $inventoryModel = app(\SilkPanel\SilkroadModels\Models\Shard\Inventory::class);
            $shardConn = \App\Enums\DatabaseNameEnums::SRO_SHARD->value;
            try {
                $inventoryItems = $inventoryModel->getInventory($this->selectedCharId, 99, 13, null);
            } catch (Throwable) {
                $inventoryItems = collect();
            }
            try {
                // Character storage lives in _Chest (keyed by UserJID), not in _Inventory.
                $userJidRow = DB::connection($shardConn)->selectOne(
                    'SELECT UserJID FROM dbo._User WITH (NOLOCK) WHERE CharID = ?',
                    [$this->selectedCharId]
                );
                if ($userJidRow) {
                    $chestRows = DB::connection($shardConn)->select(
                        'SELECT ch.Slot, ch.ItemID, it.OptLevel,
                                roc.CodeName128, roc.AssocFileIcon128, roc.CanTrade
                         FROM dbo._Chest ch
                         JOIN dbo._Items it ON it.ID64 = ch.ItemID
                         JOIN dbo._RefObjCommon roc ON roc.ID = it.RefItemId
                         WHERE ch.UserJID = ? AND ch.ItemID > 0
                         ORDER BY ch.Slot ASC',
                        [$userJidRow->UserJID]
                    );
                    $storageItems = collect($chestRows);
                }
            } catch (Throwable) {
                $storageItems = collect();
            }
        }

        // ── Own Listings ──
        $myListings = $this->activeTab === 'listings'
            ? MarketListing::where('user_id', $user->id)
                ->with('transaction')
                ->orderByDesc('created_at')
                ->paginate(20)
            : collect();

        // Balance for buy modal
        $buyListing = $this->buyListingId ? MarketListing::find($this->buyListingId) : null;
        $buyBalance = null;
        if ($buyListing) {
            $buyBalance = $this->getBalance($user, $this->selectedCharId, $buyListing->price_type);
        }

        // Settings
        $allowGold = (bool) Setting::get('web_market_allow_gold', true);
        $allowSilk = (bool) Setting::get('web_market_allow_silk', true);
        $maxDuration = (int) Setting::get('web_market_max_duration_hours', 336);
        $defaultDuration = (int) Setting::get('web_market_default_duration_hours', 24);
        $webStorageLimit = (int) Setting::get('web_market_max_storage_items', 50);

        $allSilkOptions = $isIsro
            ? collect(SilkTypeIsroEnum::cases())->mapWithKeys(fn($c) => [(string) $c->value => (string) $c->getLabel()])
            : collect(SilkTypeEnum::cases())->mapWithKeys(fn($c) => [$c->value => (string) $c->getLabel()]);

        $configuredSilkType = Setting::get('web_market_silk_type');
        // Guard legacy array values from old ->multiple() select
        if (is_array($configuredSilkType)) {
            $configuredSilkType = $configuredSilkType[0] ?? null;
        }
        $configuredSilkType = blank($configuredSilkType) ? null : (string) $configuredSilkType;

        $silkTypeOptions = $configuredSilkType
            ? $allSilkOptions->only($configuredSilkType)->all()
            : $allSilkOptions->all();

        $webStorageItem = $this->sellWebStorageId ? WebStorage::find($this->sellWebStorageId) : null;
        $returnItem = $this->returnWebStorageId ? WebStorage::find($this->returnWebStorageId) : null;

        // ── Tooltip data ──
        $tooltipService = app(ItemTooltipService::class);

        $tooltipId64s = collect()
            ->merge($webStorage->pluck('item_id64'))
            ->when($this->activeTab === 'marketplace', fn($c) => $c->merge($listings->pluck('item_id64')))
            ->when($this->activeTab === 'listings', fn($c) => $c->merge($myListings->pluck('item_id64')))
            ->when($buyListing, fn($c) => $c->push($buyListing->item_id64))
            ->filter()
            ->unique()
            ->map(fn($v) => (int) $v)
            ->values()
            ->all();

        $tooltipData = count($tooltipId64s) > 0
            ? $tooltipService->forItemIds($tooltipId64s)
            : collect();

        // Tooltip data for game inventory (bag) + chest items.
        // inventoryItems = Eloquent collection (has ItemID), storageItems = raw stdClass collection (has ItemID).
        // Use concat() not merge() — Eloquent merge() deduplicates by primary key (CharID).
        if ($inventoryItems->isNotEmpty() || $storageItems->isNotEmpty()) {
            $invId64s = $inventoryItems->pluck('ItemID')
                ->concat($storageItems->pluck('ItemID'))
                ->map(fn ($id) => (int) $id)
                ->filter(fn ($id) => $id > 0)
                ->unique()
                ->values()
                ->all();
            $inventoryTooltipData = $tooltipService->forItemIds($invId64s);
        } else {
            $inventoryTooltipData = collect();
        }

        return view('template::livewire.web-market', compact(
            'characters', 'listings', 'webStorage', 'inventoryItems', 'storageItems',
            'myListings', 'buyListing', 'buyBalance', 'requireLogout', 'selectedCharOnline',
            'allowGold', 'allowSilk', 'silkTypeOptions', 'maxDuration', 'defaultDuration',
            'webStorageItem', 'returnItem', 'isIsro',
            'tooltipData', 'inventoryTooltipData', 'webStorageLimit',
        ));
    }

    private function getBalance(mixed $user, ?int $charId, string $priceType): ?int
    {
        try {
            if ($priceType === 'gold') {
                $char = $user->shardUsers()->wherePivot('CharID', $charId)->first();
                return $char ? (int) $char->RemainGold : null;
            }

            $isIsro = config('silkpanel.version') === 'isro';
            if ($isIsro) {
                $jcash = $user->muuser?->JCash;
                if (! $jcash) {
                    return null;
                }
                return match (SilkTypeIsroEnum::tryFrom((int) $priceType)) {
                    SilkTypeIsroEnum::SILK_TYPE_NORMAL  => (int) $jcash->Silk,
                    SilkTypeIsroEnum::SILK_TYPE_PREMIUM => (int) $jcash->PremiumSilk,
                    default                             => null,
                };
            }

            $skSilk = $user->getSkSilk;
            if (! $skSilk) {
                return null;
            }
            return match (SilkTypeEnum::tryFrom($priceType)) {
                SilkTypeEnum::SILK_OWN   => (int) $skSilk->silk_own,
                SilkTypeEnum::SILK_GIFT  => (int) $skSilk->silk_gift,
                SilkTypeEnum::SILK_POINT => (int) $skSilk->silk_point,
                default                  => null,
            };
        } catch (Throwable) {
            return null;
        }
    }
}
