<div class="web-market" x-data="{}">

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="wm-alert wm-alert--success">
            <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="wm-alert wm-alert--error">
            <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/></svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- Character Selector --}}
    @if ($characters->isNotEmpty())
    <div class="wm-char-card">
        <p class="wm-char-card__label">{{ __('web-market.purchase_as_character') }}</p>
        <div class="wm-char-card__list">
            @foreach ($characters as $char)
                <button
                    wire:click="selectCharacter({{ $char->CharID }}, '{{ addslashes($char->CharName16) }}')"
                    class="wm-char-pill {{ $selectedCharId === $char->CharID ? 'wm-char-pill--active' : '' }}"
                >
                    {{ $char->CharName16 }}
                    <span class="wm-char-pill__lv">Lv.{{ $char->CurLevel }}</span>
                    @if ($requireLogout && $selectedCharId === $char->CharID && $selectedCharOnline)
                        <span class="wm-char-pill__dot">●</span>
                    @endif
                </button>
            @endforeach
        </div>
    </div>
    @endif

    @if ($requireLogout && $selectedCharOnline)
        <div class="wm-alert wm-alert--warning">
            <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/></svg>
            {{ __('web-market.error.character_must_be_offline') }}
        </div>
    @endif

    {{-- Tab Navigation --}}
    <div class="wm-tabs">
        <button wire:click="setTab('marketplace')" class="wm-tab {{ $activeTab === 'marketplace' ? 'wm-tab--active' : '' }}">
            {{ __('web-market.tabs.marketplace') }}
        </button>
        <button wire:click="setTab('storage')" class="wm-tab {{ $activeTab === 'storage' ? 'wm-tab--active' : '' }}">
            {{ __('web-market.tabs.storage') }}
        </button>
        <button wire:click="setTab('listings')" class="wm-tab {{ $activeTab === 'listings' ? 'wm-tab--active' : '' }}">
            {{ __('web-market.tabs.my_listings') }}
        </button>
    </div>

    {{-- ═══════════ MARKETPLACE TAB ═══════════ --}}
    @if ($activeTab === 'marketplace')
    <div class="wm-section">

        {{-- Filters --}}
        <div class="wm-filters">
            <div class="wm-filters__field">
                <input wire:model.live.debounce.400ms="searchQuery" type="text"
                    placeholder="{{ __('web-market.filter.search') }}" class="wm-input">
            </div>
            <div class="wm-filters__field">
                <select wire:model.live="filterPriceType" class="wm-select">
                    <option value="">{{ __('web-market.filter.all_currencies') }}</option>
                    <option value="gold">{{ __('web-market.filter.gold') }}</option>
                    <option value="silk">{{ __('web-market.filter.silk') }}</option>
                </select>
            </div>

            <div class="wm-filters__field">
                <select wire:model.live="sortBy" class="wm-select">
                    <option value="newest">{{ __('web-market.sort.newest') }}</option>
                    <option value="price_asc">{{ __('web-market.sort.price_asc') }}</option>
                    <option value="price_desc">{{ __('web-market.sort.price_desc') }}</option>
                    <option value="expires_soon">{{ __('web-market.sort.expires_soon') }}</option>
                </select>
            </div>
        </div>

        {{-- Listings Grid --}}
        @if ($listings->isEmpty())
            <div class="wm-empty">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z"/></svg>
                <p>{{ __('web-market.marketplace.empty') }}</p>
            </div>
        @else
            <div class="wm-grid">
                @foreach ($listings as $listing)
                    @php
                        $lData   = $listing->item_data ?? [];
                        $lTip    = $tooltipData[(int) $listing->item_id64] ?? null;
                        $lName   = $lTip?->get('ItemName') ?: ($listing->item_name ?: __('web-market.unknown_item'));
                        $lLevel  = $lTip?->get('ReqLevel1') ?: ($lData['req_level'] ?? null);
                        $lGender = $lTip?->get('Gender');
                        $lSox    = $lTip?->get('SoxType');
                    @endphp
                    <div class="wm-card"
                        x-data="{ tip: false, right: true }"
                        @mouseenter="let r=$event.currentTarget.getBoundingClientRect(); right=r.right+390<window.innerWidth; $el.style.setProperty('--tip-x',(right?r.right+10:window.innerWidth-r.left+10)+'px'); $el.style.setProperty('--tip-y',Math.max(10,Math.min(r.top,window.innerHeight-420))+'px'); tip=true"
                        @mouseleave="tip=false">

                        {{-- Full item stat tooltip --}}
                        @if ($lTip)
                        <div class="wm-tip" :class="right ? 'wm-tip--right' : 'wm-tip--left'" x-show="tip" x-cloak>
                            <div class="wm-tip__stats-wrap">
                                <x-characters.inventory-tooltip :item="$lTip" :inline="true" />
                            </div>
                            <div class="wm-tip__listing-extra">
                                <div class="wm-tip__listing-row">
                                    <span>Seller</span>
                                    <span>{{ $listing->character_name }}</span>
                                </div>
                                <div class="wm-tip__listing-row wm-tip__listing-price--{{ $listing->isGold() ? 'gold' : 'silk' }}">
                                    <span>Price</span>
                                    <span>{{ number_format($listing->price_amount) }} {{ $listing->priceTypeLabel() }}</span>
                                </div>
                                @if ($listing->expires_at)
                                    <div class="wm-tip__listing-row wm-tip__listing-expires">
                                        <span>Expires</span><span>{{ $listing->remaining_time }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        <div class="wm-card__icon-wrap">
                            <img src="{{ asset('images/silkroad/' . $listing->icon_path) }}"
                                 alt="{{ $lName }}" class="wm-card__icon">
                            @if ($listing->opt_level > 0)
                                <span class="wm-card__plus">+{{ $listing->opt_level }}</span>
                            @endif
                        </div>
                        <div class="wm-card__body">
                            <h3 class="wm-card__name">{{ $lName }}</h3>
                            <div class="wm-card__chips">
                                @if ($lLevel)
                                    <span class="wm-chip wm-chip--level">Lv.{{ $lLevel }}</span>
                                @endif
                                @if ($lGender)
                                    <span class="wm-chip wm-chip--gender">{{ $lGender }}</span>
                                @endif
                                @if ($lSox && $lSox !== 'Normal')
                                    <span class="wm-chip wm-chip--sox">{{ $lSox }}</span>
                                @endif
                            </div>
                            <div class="wm-card__meta">
                                <span class="wm-card__seller">{{ $listing->character_name }}</span>
                                @if ($listing->expires_at)
                                    <span class="wm-card__expires">{{ $listing->remaining_time }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="wm-card__footer">
                            <span class="wm-card__price {{ $listing->isGold() ? 'wm-card__price--gold' : 'wm-card__price--silk' }}">
                                {{ number_format($listing->price_amount) }}
                                {{ $listing->priceTypeLabel() }}
                            </span>
                            <button wire:click="openBuyModal({{ $listing->id }})" class="wm-btn wm-btn--primary wm-btn--sm">
                                {{ __('web-market.buy') }}
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="wm-pagination">
                {{ $listings->links() }}
            </div>
        @endif
    </div>
    @endif

    {{-- ═══════════ WEB STORAGE TAB ═══════════ --}}
    @if ($activeTab === 'storage')
    <div class="wm-section">
        <div class="wm-storage-layout">

            {{-- Left: Character Items (Inventory + Storage) --}}
            <div class="wm-storage-panel">
                <h2 class="wm-storage-panel__title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                    {{ $selectedCharName ?: __('web-market.select_character') }}
                </h2>

                @if ($inventoryItems->isNotEmpty())
                <div class="wm-item-group">
                    <h3 class="wm-item-group__label">{{ __('web-market.inventory') }}</h3>
                    <div class="wm-items">
                        @foreach ($inventoryItems as $invItem)
                            @php
                                $invTip = $inventoryTooltipData[(int) $invItem->ItemID] ?? null;
                                $invCanTrade = (bool) ($invItem->CanTrade ?? true);
                            @endphp
                            <div class="wm-item {{ $invCanTrade ? '' : 'wm-item--no-trade' }}"
                                wire:key="inv-{{ $invItem->ItemID }}"
                                x-data="{ tip: false, right: true, tx: '0px', ty: '0px' }"
                                @mouseenter="let r=$event.currentTarget.getBoundingClientRect(); right=r.right+390<window.innerWidth; tx=(right?r.right+10:window.innerWidth-r.left+10)+'px'; ty=Math.max(10,Math.min(r.top,window.innerHeight-420))+'px'; tip=true"
                                @mouseleave="tip=false">
                                @if ($invTip)
                                <div class="wm-tip"
                                    :style="right ? 'left:'+tx+';top:'+ty : 'right:'+tx+';top:'+ty"
                                    x-show="tip" x-cloak>
                                    <x-characters.inventory-tooltip :item="$invTip" :inline="true" />
                                </div>
                                @endif
                                <img src="{{ asset('images/silkroad/' . \App\Helpers\WebmallItemIconHelper::resolveIcon($invItem->AssocFileIcon128 ?? null)) }}"
                                     alt="{{ $invItem->CodeName128 ?? '' }}" class="wm-item__icon">
                                @if (($invItem->OptLevel ?? 0) > 0)
                                    <span class="wm-item__plus">+{{ $invItem->OptLevel }}</span>
                                @endif
                                @if (! $invCanTrade)
                                    <span class="wm-item__lock" aria-label="{{ __('web-market.error.item_not_tradeable') }}">
                                        <svg viewBox="0 0 24 24"><rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
                                    </span>
                                    <div class="wm-item__no-trade-overlay">
                                        <span>Not<br>tradeable</span>
                                    </div>
                                @else
                                    <button wire:click="openTransferModal({{ $invItem->Slot }}, 'inventory')"
                                        class="wm-item__transfer-btn"
                                        {{ ($requireLogout && $selectedCharOnline) ? 'disabled' : '' }}
                                        title="{{ __('web-market.transfer_to_storage') }}">
                                        →
                                    </button>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if ($storageItems->isNotEmpty())
                <div class="wm-item-group">
                    <h3 class="wm-item-group__label">{{ __('web-market.storage_label') }}</h3>
                    <div class="wm-items">
                        @foreach ($storageItems as $storItem)
                            @php
                                $storTip = $inventoryTooltipData[(int) $storItem->ItemID] ?? null;
                                $storCanTrade = (bool) ($storItem->CanTrade ?? true);
                            @endphp
                            <div class="wm-item {{ $storCanTrade ? '' : 'wm-item--no-trade' }}"
                                wire:key="stor-{{ $storItem->ItemID }}"
                                x-data="{ tip: false, right: true, tx: '0px', ty: '0px' }"
                                @mouseenter="let r=$event.currentTarget.getBoundingClientRect(); right=r.right+390<window.innerWidth; tx=(right?r.right+10:window.innerWidth-r.left+10)+'px'; ty=Math.max(10,Math.min(r.top,window.innerHeight-420))+'px'; tip=true"
                                @mouseleave="tip=false">
                                @if ($storTip)
                                <div class="wm-tip"
                                    :style="right ? 'left:'+tx+';top:'+ty : 'right:'+tx+';top:'+ty"
                                    x-show="tip" x-cloak>
                                    <x-characters.inventory-tooltip :item="$storTip" :inline="true" />
                                </div>
                                @endif
                                <img src="{{ asset('images/silkroad/' . \App\Helpers\WebmallItemIconHelper::resolveIcon($storItem->AssocFileIcon128 ?? null)) }}"
                                     alt="{{ $storItem->CodeName128 ?? '' }}" class="wm-item__icon">
                                @if (($storItem->OptLevel ?? 0) > 0)
                                    <span class="wm-item__plus">+{{ $storItem->OptLevel }}</span>
                                @endif
                                @if (! $storCanTrade)
                                    <span class="wm-item__lock" aria-label="{{ __('web-market.error.item_not_tradeable') }}">
                                        <svg viewBox="0 0 24 24"><rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
                                    </span>
                                    <div class="wm-item__no-trade-overlay">
                                        <span>Not<br>tradeable</span>
                                    </div>
                                @else
                                    <button wire:click="openTransferModal({{ $storItem->Slot }}, 'storage')"
                                        class="wm-item__transfer-btn"
                                        {{ ($requireLogout && $selectedCharOnline) ? 'disabled' : '' }}
                                        title="{{ __('web-market.transfer_to_storage') }}">
                                        →
                                    </button>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if ($inventoryItems->isEmpty() && $storageItems->isEmpty())
                    <div class="wm-empty wm-empty--sm">
                        <p>{{ __('web-market.storage.no_items') }}</p>
                    </div>
                @endif
            </div>

            {{-- Right: Web Storage --}}
            <div class="wm-storage-panel">
                <h2 class="wm-storage-panel__title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
                    {{ __('web-market.web_storage') }}
                    @php
                        $wsCount = $webStorage->count();
                        $wsCountClass = $wsCount >= $webStorageLimit
                            ? 'wm-storage-panel__count--full'
                            : ($wsCount >= $webStorageLimit * 0.8
                                ? 'wm-storage-panel__count--warn'
                                : 'wm-storage-panel__count--ok');
                    @endphp
                    <span class="wm-storage-panel__count {{ $wsCountClass }}">({{ $wsCount }}/{{ $webStorageLimit }})</span>
                </h2>

                @if ($webStorage->isEmpty())
                    <div class="wm-empty wm-empty--sm">
                        <p>{{ __('web-market.storage.empty') }}</p>
                    </div>
                @else
                    <div class="wm-storage-list">
                        @foreach ($webStorage as $wsItem)
                            @php
                                $wsData = $wsItem->item_data ?? [];
                                $wsTip  = $tooltipData[(int) $wsItem->item_id64] ?? null;
                                $wsName = $wsTip?->get('ItemName') ?: ($wsItem->item_name ?: __('web-market.unknown_item'));
                            @endphp
                            <div class="wm-storage-item {{ $wsItem->isListed() ? 'wm-storage-item--listed' : '' }}"
                                x-data="{ tip: false, right: true }"
                                @mouseenter="let r=$event.currentTarget.getBoundingClientRect(); right=r.right+390<window.innerWidth; $el.style.setProperty('--tip-x',(right?r.right+10:window.innerWidth-r.left+10)+'px'); $el.style.setProperty('--tip-y',Math.max(10,Math.min(r.top,window.innerHeight-420))+'px'); tip=true"
                                @mouseleave="tip=false">
                                @if ($wsTip)
                                <div class="wm-tip" :class="right ? 'wm-tip--right' : 'wm-tip--left'" x-show="tip" x-cloak>
                                    <x-characters.inventory-tooltip :item="$wsTip" :inline="true" />
                                </div>
                                @endif

                                <img src="{{ asset('images/silkroad/' . $wsItem->icon_path) }}"
                                     alt="{{ $wsName }}" class="wm-storage-item__icon">
                                <div class="wm-storage-item__info">
                                    <span class="wm-storage-item__name">
                                        {{ $wsName }}
                                        @if ($wsItem->opt_level > 0)
                                            <span class="wm-badge wm-badge--plus">+{{ $wsItem->opt_level }}</span>
                                        @endif
                                    </span>
                                    @if ($wsItem->isListed())
                                        <span class="wm-badge wm-badge--listed">{{ __('web-market.listed') }}</span>
                                    @endif
                                </div>
                                @if (! $wsItem->isListed())
                                    <div class="wm-storage-item__actions">
                                        <button wire:click="openSellModal({{ $wsItem->id }})"
                                            class="wm-btn wm-btn--primary wm-btn--xs"
                                            title="{{ __('web-market.sell') }}">
                                            {{ __('web-market.sell') }}
                                        </button>
                                        <button wire:click="openReturnModal({{ $wsItem->id }})"
                                            class="wm-btn wm-btn--ghost wm-btn--xs"
                                            {{ ($requireLogout && $selectedCharOnline) ? 'disabled' : '' }}
                                            title="{{ __('web-market.return_to_inventory') }}">
                                            ← {{ __('web-market.return') }}
                                        </button>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- ═══════════ MY LISTINGS TAB ═══════════ --}}
    @if ($activeTab === 'listings')
    <div class="wm-section">
        @if ($myListings->isEmpty())
            <div class="wm-empty">
                <p>{{ __('web-market.my_listings.empty') }}</p>
            </div>
        @else
            <div class="wm-listings-table">
                <table class="wm-table">
                    <thead>
                        <tr>
                            <th>{{ __('web-market.table.item') }}</th>
                            <th>{{ __('web-market.table.price') }}</th>
                            <th>{{ __('web-market.table.status') }}</th>
                            <th>{{ __('web-market.table.seller') }}</th>
                            <th>{{ __('web-market.table.buyer') }}</th>
                            <th>{{ __('web-market.table.expires') }}</th>
                            <th>{{ __('web-market.table.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($myListings as $listing)
                            @php $mlName = ($tooltipData[(int) $listing->item_id64] ?? null)?->get('ItemName') ?: ($listing->item_name ?: __('web-market.unknown_item')); @endphp
                            <tr class="wm-table__row wm-table__row--{{ $listing->status->value }}">
                                <td>
                                    <div class="wm-table__item">
                                        <img src="{{ asset('images/silkroad/' . $listing->icon_path) }}"
                                             alt="{{ $mlName }}" class="wm-table__icon">
                                        <span>
                                            {{ $mlName }}
                                            @if ($listing->opt_level > 0)
                                                <span class="wm-badge wm-badge--plus">+{{ $listing->opt_level }}</span>
                                            @endif
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <span class="{{ $listing->isGold() ? 'wm-price--gold' : 'wm-price--silk' }}">
                                        {{ number_format($listing->price_amount) }} {{ $listing->priceTypeLabel() }}
                                    </span>
                                    @if ($listing->fee_amount > 0)
                                        <small class="wm-fee">-{{ number_format($listing->fee_amount) }} fee</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="wm-status wm-status--{{ $listing->status->value }}">
                                        {{ $listing->status->getLabel() }}
                                    </span>
                                </td>
                                <td>
                                    <span class="wm-table__buyer">{{ $listing->character_name }}</span>
                                </td>
                                <td>
                                    @if ($listing->transaction)
                                        <span class="wm-table__buyer">{{ $listing->transaction->buyer_character_name }}</span>
                                    @else
                                        <span class="wm-table__buyer wm-table__buyer--none">—</span>
                                    @endif
                                </td>
                                <td>{{ $listing->expires_at?->format('d.m.Y H:i') ?? '—' }}</td>
                                <td>
                                    @if ($listing->isActive())
                                        <button wire:click="openCancelModal({{ $listing->id }})"
                                            class="wm-btn wm-btn--danger wm-btn--xs">
                                            {{ __('web-market.cancel_listing') }}
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="wm-pagination">{{ $myListings->links() }}</div>
            </div>
        @endif
    </div>
    @endif

    {{-- ═══════════ MODALS ═══════════ --}}

    {{-- Buy Modal (webmall-style) --}}

    @if ($showBuyModal && $buyListing)
    @php
        $remaining  = $buyBalance !== null ? $buyBalance - $buyListing->price_amount : null;
        $canAfford  = $remaining === null || $remaining >= 0;
        $buyTip     = $tooltipData[(int) $buyListing->item_id64] ?? null;
        $buyName    = $buyTip?->get('ItemName') ?: ($buyListing->item_name ?: __('web-market.unknown_item'));
    @endphp
    <div class="wm-modal-backdrop" wire:click.self="$set('showBuyModal', false)">
        <div class="wm-modal">
            <h2 class="wm-modal__title">{{ __('web-market.modal.buy.title') }}</h2>

            {{-- Item header --}}
            <div class="wm-modal__item-preview">
                <img src="{{ asset('images/silkroad/' . $buyListing->icon_path) }}"
                     alt="{{ $buyName }}" class="wm-modal__item-icon">
                <div>
                    <strong>{{ $buyName }}</strong>
                    @if ($buyListing->opt_level > 0)
                        <span class="wm-badge wm-badge--plus">+{{ $buyListing->opt_level }}</span>
                    @endif
                </div>
            </div>

            {{-- Key details --}}
            <dl class="wm-modal__dl">
                <div class="wm-modal__dl-row">
                    <dt>{{ __('web-market.modal.buy.balance') }}</dt>
                    <dd>{{ $buyListing->priceTypeLabel() }}</dd>
                </div>
                <div class="wm-modal__dl-row">
                    <dt>Price</dt>
                    <dd class="{{ $buyListing->isGold() ? 'wm-price--gold' : 'wm-price--silk' }}">
                        {{ number_format($buyListing->price_amount) }} {{ $buyListing->priceTypeLabel() }}
                    </dd>
                </div>
                <div class="wm-modal__dl-row">
                    <dt>Seller</dt>
                    <dd>{{ $buyListing->character_name }}</dd>
                </div>
                @if ($selectedCharName)
                <div class="wm-modal__dl-row">
                    <dt>Character</dt>
                    <dd>{{ $selectedCharName }}</dd>
                </div>
                @endif
            </dl>

            {{-- Balance breakdown --}}
            @if ($buyBalance !== null)
            <div class="wm-modal__breakdown">
                <div class="wm-modal__breakdown-row">
                    <span>Current {{ $buyListing->priceTypeLabel() }}</span>
                    <span>{{ number_format($buyBalance) }}</span>
                </div>
                <div class="wm-modal__breakdown-row wm-modal__breakdown-row--cost">
                    <span>Cost</span>
                    <span>−{{ number_format($buyListing->price_amount) }}</span>
                </div>
                <div class="wm-modal__breakdown-sep"></div>
                <div class="wm-modal__breakdown-row wm-modal__breakdown-row--total {{ $canAfford ? '' : 'wm-modal__breakdown-row--negative' }}">
                    <span>After Purchase</span>
                    <span>{{ number_format($remaining) }}</span>
                </div>
            </div>
            @endif

            <p class="wm-modal__note">{{ __('web-market.modal.buy.delivered_to_storage') }}</p>

            <div class="wm-modal__actions">
                <button wire:click="$set('showBuyModal', false)" class="wm-btn wm-btn--ghost">
                    {{ __('web-market.cancel') }}
                </button>
                <button wire:click="confirmBuy" wire:loading.attr="disabled"
                    {{ ! $canAfford ? 'disabled' : '' }}
                    class="wm-btn wm-btn--primary {{ ! $canAfford ? 'wm-btn--disabled' : '' }}">
                    <span wire:loading.remove>{{ __('web-market.modal.buy.confirm') }}</span>
                    <span wire:loading>{{ __('web-market.loading') }}</span>
                </button>
            </div>
        </div>
    </div>
    @endif


    {{-- Transfer to Web Storage Modal --}}

    @if ($showTransferModal)
    <div class="wm-modal-backdrop" wire:click.self="$set('showTransferModal', false)">
        <div class="wm-modal">
            <h2 class="wm-modal__title">{{ __('web-market.modal.transfer.title') }}</h2>
            <p class="wm-modal__body">{{ __('web-market.modal.transfer.body', ['source' => $transferSourceType]) }}</p>
            <div class="wm-modal__actions">
                <button wire:click="$set('showTransferModal', false)" class="wm-btn wm-btn--ghost">
                    {{ __('web-market.cancel') }}
                </button>
                <button wire:click="confirmTransfer" wire:loading.attr="disabled" class="wm-btn wm-btn--primary">
                    <span wire:loading.remove>{{ __('web-market.modal.transfer.confirm') }}</span>
                    <span wire:loading>{{ __('web-market.loading') }}</span>
                </button>
            </div>
        </div>
    </div>
    @endif


    {{-- Return from Web Storage Modal --}}

    @if ($showReturnModal && $returnItem)
    @php
        $returnTip  = $tooltipData[(int) $returnItem->item_id64] ?? null;
        $returnName = $returnTip?->get('ItemName') ?: ($returnItem->item_name ?: __('web-market.unknown_item'));
    @endphp
    <div class="wm-modal-backdrop" wire:click.self="$set('showReturnModal', false)">
        <div class="wm-modal">
            <h2 class="wm-modal__title">{{ __('web-market.modal.return.title') }}</h2>
            <div class="wm-modal__item-preview">
                <img src="{{ asset('images/silkroad/' . $returnItem->icon_path) }}"
                     alt="{{ $returnName }}" class="wm-modal__item-icon">
                <div>
                    <strong>{{ $returnName }}</strong>
                    @if ($returnItem->opt_level > 0) <span class="wm-badge wm-badge--plus">+{{ $returnItem->opt_level }}</span> @endif
                    <p class="wm-modal__note">{{ __('web-market.modal.return.body') }}</p>
                </div>
            </div>
            <div class="wm-modal__actions">
                <button wire:click="$set('showReturnModal', false)" class="wm-btn wm-btn--ghost">
                    {{ __('web-market.cancel') }}
                </button>
                <button wire:click="confirmReturn" wire:loading.attr="disabled" class="wm-btn wm-btn--primary">
                    <span wire:loading.remove>{{ __('web-market.modal.return.confirm') }}</span>
                    <span wire:loading>{{ __('web-market.loading') }}</span>
                </button>
            </div>
        </div>
    </div>
    @endif


    {{-- Sell Modal --}}

    @if ($showSellModal && $webStorageItem)
    @php
        $sellData    = $webStorageItem->item_data ?? [];
        $sellTip     = $tooltipData[(int) $webStorageItem->item_id64] ?? null;
        $sellName    = $sellTip?->get('ItemName') ?: ($webStorageItem->item_name ?: __('web-market.unknown_item'));
        $sellReqLvl  = $sellTip?->get('ReqLevel1') ?: ($sellData['req_level'] ?? null);
    @endphp
    <div class="wm-modal-backdrop" wire:click.self="$set('showSellModal', false)">
        <div class="wm-modal wm-modal--wide">
            <h2 class="wm-modal__title">{{ __('web-market.modal.sell.title') }}</h2>
            <div class="wm-modal__item-preview"
                x-data="{ tip: false, right: true }"
                @mouseenter="let r=$event.currentTarget.getBoundingClientRect(); right=r.right+390<window.innerWidth; $el.style.setProperty('--tip-x',(right?r.right+10:window.innerWidth-r.left+10)+'px'); $el.style.setProperty('--tip-y',Math.max(10,Math.min(r.top,window.innerHeight-420))+'px'); tip=true"
                @mouseleave="tip=false">
                @if ($sellTip)
                <div class="wm-tip" :class="right ? 'wm-tip--right' : 'wm-tip--left'" x-show="tip" x-cloak>
                    <x-characters.inventory-tooltip :item="$sellTip" :inline="true" />
                </div>
                @endif
                <img src="{{ asset('images/silkroad/' . $webStorageItem->icon_path) }}"
                     alt="{{ $sellName }}" class="wm-modal__item-icon">
                <div>
                    <strong>{{ $sellName }}</strong>
                    @if ($webStorageItem->opt_level > 0) <span class="wm-badge wm-badge--plus">+{{ $webStorageItem->opt_level }}</span> @endif
                    @if ($sellReqLvl)
                        <div class="wm-modal__item-meta">Req. Level {{ $sellReqLvl }}</div>
                    @endif
                </div>
            </div>
            <div class="wm-modal__form">
                <div class="wm-form-group">
                    <label class="wm-label">{{ __('web-market.modal.sell.price_type') }}</label>
                    <select wire:model="sellPriceType" class="wm-select">
                        @if ($allowGold)
                            <option value="gold">Gold</option>
                        @endif
                        @if ($allowSilk)
                            @foreach ($silkTypeOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="wm-form-group">
                    <label class="wm-label">{{ __('web-market.modal.sell.price') }}</label>
                    <input wire:model="sellPriceAmount" type="number" min="1" class="wm-input"
                        placeholder="{{ __('web-market.modal.sell.price_placeholder') }}">
                    @error('sellPriceAmount') <span class="wm-error">{{ $message }}</span> @enderror
                </div>
                <div class="wm-form-group">
                    <label class="wm-label">{{ __('web-market.modal.sell.duration') }} ({{ __('web-market.hours') }})</label>
                    <input wire:model="sellDurationHours" type="number" min="1" max="{{ $maxDuration }}" class="wm-input">
                    <small class="wm-help">{{ __('web-market.modal.sell.max_duration', ['max' => $maxDuration]) }}</small>
                </div>
                <div class="wm-form-group">
                    <label class="wm-label">{{ __('web-market.modal.sell.description') }}</label>
                    <textarea wire:model="sellDescription" class="wm-input" rows="2"
                        placeholder="{{ __('web-market.modal.sell.description_placeholder') }}"></textarea>
                </div>
            </div>
            <div class="wm-modal__actions">
                <button wire:click="$set('showSellModal', false)" class="wm-btn wm-btn--ghost">
                    {{ __('web-market.cancel') }}
                </button>
                <button wire:click="confirmSell" wire:loading.attr="disabled" class="wm-btn wm-btn--primary">
                    <span wire:loading.remove>{{ __('web-market.modal.sell.confirm') }}</span>
                    <span wire:loading>{{ __('web-market.loading') }}</span>
                </button>
            </div>
        </div>
    </div>
    @endif


    {{-- Cancel Listing Modal --}}

    @if ($showCancelModal)
    <div class="wm-modal-backdrop" wire:click.self="$set('showCancelModal', false)">
        <div class="wm-modal">
            <h2 class="wm-modal__title">{{ __('web-market.modal.cancel.title') }}</h2>
            <p class="wm-modal__body">{{ __('web-market.modal.cancel.body') }}</p>
            <div class="wm-modal__actions">
                <button wire:click="$set('showCancelModal', false)" class="wm-btn wm-btn--ghost">
                    {{ __('web-market.cancel') }}
                </button>
                <button wire:click="confirmCancelListing" wire:loading.attr="disabled" class="wm-btn wm-btn--danger">
                    <span wire:loading.remove>{{ __('web-market.modal.cancel.confirm') }}</span>
                    <span wire:loading>{{ __('web-market.loading') }}</span>
                </button>
            </div>
        </div>
    </div>
    @endif


</div>

<style>
[x-cloak] { display: none !important; }

.web-market { --wm-gold: #d97706; --wm-silk: #7c3aed; --wm-success: #16a34a; --wm-danger: #dc2626; --wm-warn: #ca8a04; }

/* ── Alerts ── */
.wm-alert { display:flex;align-items:center;gap:.5rem;padding:.75rem 1rem;border-radius:.5rem;margin-bottom:1rem;font-size:.9rem; }
.wm-alert svg { width:1.25rem;height:1.25rem;flex-shrink:0; }
.wm-alert--success { background:#f0fdf4;color:#166534;border:1px solid #bbf7d0; }
.wm-alert--error { background:#fef2f2;color:#991b1b;border:1px solid #fecaca; }
.wm-alert--warning { background:#fffbeb;color:#92400e;border:1px solid #fde68a; }

/* ── Character selector (webmall-style card) ── */
.wm-char-card { background:#fff;border:1px solid rgba(0,0,0,.1);border-radius:.75rem;padding:1rem 1.25rem;margin-bottom:1.25rem;box-shadow:0 1px 3px rgba(0,0,0,.06); }
.wm-char-card__label { font-size:.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;opacity:.5;margin-bottom:.6rem; }
.wm-char-card__list { display:flex;flex-wrap:wrap;gap:.5rem; }
.wm-char-pill { display:inline-flex;align-items:center;gap:.3rem;padding:.35rem .85rem;border-radius:9999px;border:1px solid rgba(0,0,0,.12);background:rgba(0,0,0,.04);cursor:pointer;font-size:.85rem;font-weight:600;transition:all .15s;color:inherit; }
.wm-char-pill:hover { background:rgba(0,0,0,.08); }
.wm-char-pill--active { background:var(--accent,#6366f1);color:#fff;border-color:transparent; }
.wm-char-pill__lv { font-size:.75rem;font-weight:400;opacity:.75; }
.wm-char-pill__dot { font-size:.65rem;color:#ef4444; }

/* ── Tabs ── */
.wm-tabs { display:flex;gap:.25rem;margin-bottom:1.5rem;border-bottom:2px solid rgba(0,0,0,.08); }
.wm-tab { padding:.6rem 1rem;border:none;background:transparent;cursor:pointer;font-size:.9rem;color:inherit;opacity:.6;position:relative;transition:all .15s; }
.wm-tab:hover { opacity:1; }
.wm-tab--active { opacity:1;font-weight:600; }
.wm-tab--active::after { content:'';position:absolute;bottom:-2px;left:0;right:0;height:2px;background:var(--accent,#6366f1); }

/* ── Filters / Section ── */
.wm-section { min-height:200px; }
.wm-filters { display:flex;gap:.75rem;margin-bottom:1.25rem;flex-wrap:wrap; }
.wm-filters__field { flex:1;min-width:160px; }
.wm-input,.wm-select,.wm-textarea { width:100%;padding:.5rem .75rem;border-radius:.375rem;border:1px solid rgba(0,0,0,.15);background:rgba(0,0,0,.03);font-size:.9rem;transition:border-color .15s; }
.wm-input:focus,.wm-select:focus { outline:none;border-color:var(--accent,#6366f1); }

/* ── Marketplace grid ── */
.wm-grid { display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:1rem; }
.wm-card { border:1px solid rgba(0,0,0,.1);border-radius:.75rem;display:flex;flex-direction:column;transition:top .15s,box-shadow .15s;position:relative;top:0; }
.wm-card:hover { top:-2px;box-shadow:0 4px 20px rgba(0,0,0,.1); }
.wm-card__icon-wrap { position:relative;padding:1rem;display:flex;justify-content:center;background:rgba(0,0,0,.04);border-radius:.75rem .75rem 0 0; }
.wm-card__icon { width:48px;height:48px;object-fit:contain; }
.wm-card__plus { position:absolute;top:.4rem;right:.4rem;background:#1d4ed8;color:#fff;font-size:.7rem;font-weight:700;padding:.1rem .35rem;border-radius:.25rem; }
.wm-card__body { flex:1;padding:.75rem; }
.wm-card__name { font-size:.9rem;font-weight:600;margin-bottom:.375rem;line-height:1.3; }
.wm-card__meta { display:flex;justify-content:space-between;font-size:.75rem;opacity:.6; }
.wm-card__footer { padding:.75rem;border-top:1px solid rgba(0,0,0,.06);display:flex;align-items:center;justify-content:space-between; }
.wm-card__price { font-weight:700;font-size:.9rem; }
.wm-card__price--gold { color:var(--wm-gold); }
.wm-card__price--silk { color:var(--wm-silk); }

/* ── Item tooltip (fixed-position, left/right of item) ── */
.wm-tip {
    position:fixed;
    top:var(--tip-y,0px);
    z-index:9999;
    pointer-events:none;
    max-width:min(380px,calc(100vw - 20px));
    width:max-content;
}
.wm-tip--right { left:var(--tip-x,0px); right:auto; }
.wm-tip--left  { right:var(--tip-x,0px); left:auto; }
/* Override inventory-tooltip's min-w-90 (360px) so it respects our max-width */
.wm-tip > div,
.wm-tip__stats-wrap > div {
    min-width:0 !important;
    max-width:100% !important;
    width:100% !important;
}
/* Strip the bottom radius of the inventory-tooltip box so listing-extra connects flush */
.wm-tip__stats-wrap > div { border-bottom-left-radius:0!important; border-bottom-right-radius:0!important; border-bottom:none!important; }
/* Extra listing info appended below the stat tooltip */
.wm-tip__listing-extra {
    background:#0b1020;
    border:1px solid rgba(100,116,139,.35);
    border-top:none;
    border-radius:0 0 .5rem .5rem;
    padding:6px 12px;
    font-size:.8rem;
    color:#d1d5db;
}
.wm-tip__listing-row { display:flex;justify-content:space-between;gap:1rem;line-height:1.6; }
.wm-tip__listing-price--gold span:last-child { color:#f59e0b;font-weight:700; }
.wm-tip__listing-price--silk span:last-child { color:#a78bfa;font-weight:700; }
.wm-tip__listing-expires { color:#6b7280;font-size:.75rem; }

/* ── Empty state ── */
.wm-empty { display:flex;flex-direction:column;align-items:center;justify-content:center;padding:3rem;opacity:.4;gap:.5rem; }
.wm-empty svg { width:3rem;height:3rem; }
.wm-empty--sm { padding:1.5rem; }
.wm-pagination { margin-top:1.5rem; }

/* ── Storage layout ── */
.wm-storage-layout { display:grid;grid-template-columns:1fr 1fr;gap:1.5rem; }
@media(max-width:768px){.wm-storage-layout{grid-template-columns:1fr;}}
.wm-storage-panel { border:1px solid rgba(0,0,0,.1);border-radius:.75rem;padding:1rem; }
.wm-storage-panel__title { display:flex;align-items:center;gap:.5rem;font-size:1rem;font-weight:700;margin-bottom:1rem; }
.wm-storage-panel__title svg { width:1.25rem;height:1.25rem;opacity:.7; }
.wm-storage-panel__count { font-size:.8rem;font-weight:500; }
.wm-storage-panel__count--ok   { color:inherit;opacity:.5; }
.wm-storage-panel__count--warn { color:#d97706; }
.wm-storage-panel__count--full { color:#dc2626;font-weight:700; }
.wm-item-group { margin-bottom:1rem; }
.wm-item-group__label { font-size:.75rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;opacity:.5;margin-bottom:.5rem; }
.wm-items { display:flex;flex-wrap:wrap;gap:.5rem; }
.wm-item { position:relative;width:48px;height:48px;cursor:pointer; }
.wm-item__icon { width:100%;height:100%;object-fit:contain;border:1px solid rgba(0,0,0,.1);border-radius:.25rem; }
.wm-item__plus { position:absolute;top:-4px;right:-4px;background:#1d4ed8;color:#fff;font-size:.6rem;font-weight:700;padding:.05rem .25rem;border-radius:.2rem;pointer-events:none; }
.wm-item__transfer-btn { position:absolute;inset:0;background:rgba(0,0,0,.4);color:#fff;border:none;cursor:pointer;border-radius:.25rem;font-size:1.1rem;display:flex;align-items:center;justify-content:center;opacity:0;transition:opacity .15s; }
.wm-item:hover .wm-item__transfer-btn { opacity:1; }
.wm-item--no-trade { cursor:default; }
.wm-item--no-trade .wm-item__icon { filter:grayscale(1);opacity:.45; }
.wm-item__lock { position:absolute;bottom:-4px;left:-4px;width:16px;height:16px;background:#dc2626;border-radius:50%;display:flex;align-items:center;justify-content:center;pointer-events:none;box-shadow:0 1px 3px rgba(0,0,0,.4);z-index:2; }
.wm-item__lock svg { width:9px;height:9px;color:#fff;fill:none;stroke:currentColor;stroke-width:2.5;stroke-linecap:round;stroke-linejoin:round; }
.wm-item__no-trade-overlay { position:absolute;inset:0;background:rgba(0,0,0,.55);border-radius:.25rem;display:flex;align-items:center;justify-content:center;opacity:0;transition:opacity .15s;pointer-events:none;z-index:1; }
.wm-item--no-trade:hover .wm-item__no-trade-overlay { opacity:1; }
.wm-item__no-trade-overlay span { font-size:.42rem;color:#fff;font-weight:700;text-align:center;line-height:1.25;text-transform:uppercase;letter-spacing:.03em; }
.wm-storage-list { display:flex;flex-direction:column;gap:.5rem;max-height:480px;overflow-y:auto; }
.wm-storage-item { display:flex;align-items:center;gap:.75rem;padding:.5rem .75rem;border:1px solid rgba(0,0,0,.08);border-radius:.5rem;transition:background .15s;position:relative; }
.wm-storage-item:hover { background:rgba(0,0,0,.03); }
.wm-storage-item--listed { opacity:.6; }
.wm-storage-item__icon { width:48px;height:48px;object-fit:contain;flex-shrink:0; }
.wm-storage-item__info { flex:1;min-width:0; }
.wm-storage-item__name { font-size:.85rem;font-weight:600;display:flex;align-items:center;gap:.375rem;flex-wrap:wrap; }
.wm-storage-item__actions { display:flex;gap:.375rem;flex-shrink:0; }

/* ── Badges ── */
.wm-badge { font-size:.7rem;font-weight:700;padding:.1rem .35rem;border-radius:.2rem; }
.wm-badge--plus { background:#dbeafe;color:#1d4ed8; }
.wm-badge--listed { background:#fef3c7;color:#92400e; }

/* ── Card meta chips (level / gender / sox) ── */
.wm-card__chips { display:flex;flex-wrap:wrap;gap:.25rem;margin:.25rem 0 .3rem; }
.wm-chip { display:inline-flex;align-items:center;padding:.05rem .35rem;border-radius:.2rem;font-size:.68rem;font-weight:600;white-space:nowrap; }
.wm-chip--level { background:rgba(0,0,0,.07);color:inherit;opacity:.75; }
.wm-chip--gender { background:#eff6ff;color:#1e40af; }
.wm-chip--sox { background:#fef9c3;color:#854d0e; }

/* ── My Listings table ── */
.wm-listings-table { overflow-x:auto; }
.wm-table { width:100%;border-collapse:collapse;font-size:.9rem; }
.wm-table th { padding:.6rem .75rem;text-align:left;font-weight:600;font-size:.8rem;text-transform:uppercase;letter-spacing:.03em;opacity:.6;border-bottom:2px solid rgba(0,0,0,.1); }
.wm-table td { padding:.75rem;border-bottom:1px solid rgba(0,0,0,.06); }
.wm-table__item { display:flex;align-items:center;gap:.5rem; }
.wm-table__icon { width:48px;height:48px;object-fit:contain; }
.wm-table__buyer { font-size:.85rem;font-weight:600; }
.wm-table__buyer--none { opacity:.35; }
.wm-price--gold { color:var(--wm-gold);font-weight:700; }
.wm-price--silk { color:var(--wm-silk);font-weight:700; }
.wm-fee { display:block;font-size:.75rem;opacity:.5; }
.wm-status { font-size:.75rem;font-weight:600;padding:.2rem .5rem;border-radius:.25rem; }
.wm-status--active { background:#dcfce7;color:#166534; }
.wm-status--sold { background:#dbeafe;color:#1e40af; }
.wm-status--expired { background:#fef3c7;color:#92400e; }
.wm-status--cancelled { background:#fee2e2;color:#991b1b; }

/* ── Buttons ── */
.wm-btn { display:inline-flex;align-items:center;justify-content:center;padding:.5rem 1rem;border-radius:.375rem;font-size:.9rem;font-weight:600;border:none;cursor:pointer;transition:all .15s;white-space:nowrap; }
.wm-btn--primary { background:var(--accent,#6366f1);color:#fff; }
.wm-btn--primary:hover:not(:disabled) { filter:brightness(1.1); }
.wm-btn--danger { background:var(--wm-danger);color:#fff; }
.wm-btn--ghost { background:transparent;border:1px solid rgba(0,0,0,.15); }
.wm-btn--ghost:hover { background:rgba(0,0,0,.06); }
.wm-btn--sm { padding:.375rem .75rem;font-size:.85rem; }
.wm-btn--xs { padding:.25rem .5rem;font-size:.78rem; }
.wm-btn:disabled,.wm-btn--disabled { opacity:.5;cursor:not-allowed; }

/* ── Modals ── */
.wm-modal-backdrop { position:fixed;inset:0;background:rgba(0,0,0,.55);backdrop-filter:blur(2px);display:flex;align-items:center;justify-content:center;z-index:9998;padding:1rem; }
.wm-modal { background:#fff;border-radius:.75rem;padding:1.5rem;width:100%;max-width:440px;box-shadow:0 20px 60px rgba(0,0,0,.25); }
.wm-modal--wide { max-width:540px; }
.wm-modal__title { font-size:1.1rem;font-weight:700;margin-bottom:1.1rem; }
.wm-modal__item-preview { display:flex;align-items:flex-start;gap:1rem;margin-bottom:1rem;cursor:default; }
.wm-modal__item-icon { width:60px;height:60px;object-fit:contain;flex-shrink:0;border:1px solid rgba(0,0,0,.08);border-radius:.375rem;padding:2px; }
.wm-modal__item-meta { font-size:.78rem;opacity:.55;margin-top:.15rem; }

/* Modal key-value list */
.wm-modal__dl { display:flex;flex-direction:column;gap:.35rem;font-size:.88rem;margin-bottom:1rem; }
.wm-modal__dl-row { display:flex;justify-content:space-between;align-items:center; }
.wm-modal__dl-row dt { opacity:.6; }
.wm-modal__dl-row dd { font-weight:600; }

/* Balance breakdown box */
.wm-modal__breakdown { background:rgba(0,0,0,.04);border:1px solid rgba(0,0,0,.1);border-radius:.5rem;padding:.75rem 1rem;font-size:.88rem;margin-bottom:.75rem;display:flex;flex-direction:column;gap:.3rem; }
.wm-modal__breakdown-row { display:flex;justify-content:space-between;align-items:center;color:inherit;opacity:.8; }
.wm-modal__breakdown-row--cost { color:var(--wm-danger); opacity:1; }
.wm-modal__breakdown-sep { height:1px;background:rgba(0,0,0,.1);margin:.2rem 0; }
.wm-modal__breakdown-row--total { font-weight:700;opacity:1; }
.wm-modal__breakdown-row--negative { color:var(--wm-danger); }

.wm-modal__note { font-size:.8rem;opacity:.55;margin-bottom:.5rem; }
.wm-modal__body { font-size:.9rem;margin-bottom:1rem;opacity:.8; }
.wm-modal__actions { display:flex;justify-content:flex-end;gap:.75rem;margin-top:1.25rem; }
.wm-modal__form { display:flex;flex-direction:column;gap:.75rem;margin-bottom:1rem; }
.wm-form-group { display:flex;flex-direction:column;gap:.25rem; }
.wm-label { font-size:.8rem;font-weight:600;opacity:.7; }
.wm-help { font-size:.75rem;opacity:.5; }
.wm-error { font-size:.75rem;color:var(--wm-danger); }

/* ── Dark mode ── */
@media(prefers-color-scheme:dark){
  .wm-char-card { background:#1f2937;border-color:rgba(255,255,255,.1); }
  .wm-char-pill { background:rgba(255,255,255,.06);border-color:rgba(255,255,255,.12);color:inherit; }
  .wm-char-pill:hover { background:rgba(255,255,255,.12); }
  .wm-modal { background:#1f2937;color:#f9fafb; }
  .wm-modal__breakdown { background:rgba(255,255,255,.05);border-color:rgba(255,255,255,.1); }
  .wm-modal__breakdown-sep { background:rgba(255,255,255,.1); }
  .wm-card { border-color:rgba(255,255,255,.1); }
  .wm-card__icon-wrap { background:rgba(255,255,255,.04); }
  .wm-chip--level { background:rgba(255,255,255,.08);opacity:1; }
  .wm-chip--gender { background:rgba(59,130,246,.15);color:#93c5fd; }
  .wm-chip--sox { background:rgba(234,179,8,.15);color:#fde68a; }
  .wm-input,.wm-select { background:rgba(255,255,255,.05);border-color:rgba(255,255,255,.15);color:inherit; }
  .wm-storage-item { border-color:rgba(255,255,255,.08); }
  .wm-storage-panel { border-color:rgba(255,255,255,.1); }
  .wm-table th { border-bottom-color:rgba(255,255,255,.1); }
  .wm-table td { border-bottom-color:rgba(255,255,255,.06); }
  .wm-btn--ghost { border-color:rgba(255,255,255,.2); }
  .wm-modal__dl-row dd { color:#f9fafb; }
}
</style>
