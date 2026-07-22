<div class="nswm font-mono" x-data="{}">

    {{-- ═══════════ Flash Messages ═══════════ --}}
    @if (session('success'))
        <div class="mb-6 p-4 border border-violet-500/40 bg-violet-500/10 flex items-center gap-2 text-sm text-violet-300">
            <svg class="w-5 h-5 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-6 p-4 border border-red-500/40 bg-red-500/10 flex items-center gap-2 text-sm text-red-300">
            <svg class="w-5 h-5 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/></svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- ═══════════ Character Selector ═══════════ --}}
    @if ($characters->isNotEmpty())
        <div class="bg-zinc-900 border border-violet-500/20 p-5 mb-6">
            <p class="text-xs font-bold uppercase tracking-[0.25em] text-violet-400/70 mb-3">{{ __('web-market.purchase_as_character') }}</p>
            <div class="flex flex-wrap gap-2">
                @foreach ($characters as $char)
                    <button wire:click="selectCharacter({{ $char->CharID }}, '{{ addslashes($char->CharName16) }}')"
                        class="cursor-pointer px-4 py-1.5 text-xs font-bold uppercase tracking-wider transition inline-flex items-center gap-1.5
                            {{ $selectedCharId === $char->CharID
                                ? 'bg-linear-to-r from-violet-600 to-fuchsia-600 text-white shadow-[0_0_15px_rgba(139,92,246,0.4)]'
                                : 'border border-zinc-700 text-zinc-400 hover:text-violet-300 hover:border-violet-500/40' }}">
                        {{ $char->CharName16 }}
                        <span class="opacity-60 font-normal">Lv.{{ $char->CurLevel }}</span>
                        @if ($requireLogout && $selectedCharId === $char->CharID && $selectedCharOnline)
                            <span class="w-2 h-2 rounded-full bg-red-400"></span>
                        @endif
                    </button>
                @endforeach
            </div>
        </div>
    @endif

    @if ($requireLogout && $selectedCharOnline)
        <div class="mb-6 p-4 border border-amber-500/30 bg-amber-500/10 flex items-center gap-2 text-sm text-amber-300">
            <svg class="w-5 h-5 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/></svg>
            {{ __('web-market.error.character_must_be_offline') }}
        </div>
    @endif

    {{-- ═══════════ Tab Navigation ═══════════ --}}
    <div class="mb-6 overflow-x-auto border-b border-zinc-800">
        <nav class="flex gap-0 min-w-max">
            @foreach (['marketplace' => __('web-market.tabs.marketplace'), 'storage' => __('web-market.tabs.storage'), 'listings' => __('web-market.tabs.my_listings')] as $tabKey => $tabLabel)
                <button wire:click="setTab('{{ $tabKey }}')"
                    class="cursor-pointer px-5 py-2.5 text-xs font-bold uppercase tracking-widest whitespace-nowrap border-b-2 transition-all -mb-px
                        {{ $activeTab === $tabKey
                            ? 'border-violet-500 text-violet-400'
                            : 'border-transparent text-zinc-500 hover:text-zinc-200 hover:border-violet-700/50' }}">
                    {{ $tabLabel }}
                </button>
            @endforeach
        </nav>
    </div>

    {{-- ═══════════ MARKETPLACE TAB ═══════════ --}}
    @if ($activeTab === 'marketplace')
    <div class="min-h-[200px]">

        {{-- Filters --}}
        <div class="flex flex-wrap gap-3 mb-6">
            <div class="flex-1 min-w-[160px]">
                <input wire:model.live.debounce.400ms="searchQuery" type="text" placeholder="{{ __('web-market.filter.search') }}"
                    class="w-full bg-zinc-950/50 border border-zinc-800 text-zinc-200 px-3 py-2 text-sm focus:border-violet-500 focus:outline-none placeholder:text-zinc-600">
            </div>
            <div class="flex-1 min-w-[160px]">
                <select wire:model.live="filterPriceType" class="w-full bg-zinc-950/50 border border-zinc-800 text-zinc-200 px-3 py-2 text-sm focus:border-violet-500 focus:outline-none">
                    <option value="">{{ __('web-market.filter.all_currencies') }}</option>
                    <option value="gold">{{ __('web-market.filter.gold') }}</option>
                    <option value="silk">{{ __('web-market.filter.silk') }}</option>
                </select>
            </div>
            <div class="flex-1 min-w-[160px]">
                <select wire:model.live="sortBy" class="w-full bg-zinc-950/50 border border-zinc-800 text-zinc-200 px-3 py-2 text-sm focus:border-violet-500 focus:outline-none">
                    <option value="newest">{{ __('web-market.sort.newest') }}</option>
                    <option value="price_asc">{{ __('web-market.sort.price_asc') }}</option>
                    <option value="price_desc">{{ __('web-market.sort.price_desc') }}</option>
                    <option value="expires_soon">{{ __('web-market.sort.expires_soon') }}</option>
                </select>
            </div>
        </div>

        {{-- Listings Grid --}}
        @if ($listings->isEmpty())
            <div class="bg-zinc-900 border border-zinc-800 p-16 text-center flex flex-col items-center gap-3">
                <svg class="h-12 w-12 text-zinc-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z"/></svg>
                <p class="text-xs uppercase tracking-[0.3em] text-zinc-600">{{ __('web-market.marketplace.empty') }}</p>
            </div>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
                @foreach ($listings as $listing)
                    @php
                        $lData   = $listing->item_data ?? [];
                        $lTip    = $tooltipData[(int) $listing->item_id64] ?? null;
                        $lName   = $lTip?->get('ItemName') ?: ($listing->item_name ?: __('web-market.unknown_item'));
                        $lLevel  = $lTip?->get('ReqLevel1') ?: ($lData['req_level'] ?? null);
                        $lGender = $lTip?->get('Gender');
                        $lSox    = $lTip?->get('SoxType');
                    @endphp
                    <div class="relative bg-zinc-900 border border-violet-500/15 hover:border-violet-500/35 hover:shadow-[0_0_20px_rgba(139,92,246,0.08)] transition flex flex-col"
                        x-data="{ tip: false, right: true }"
                        @mouseenter="let r=$event.currentTarget.getBoundingClientRect(); right=r.right+390<window.innerWidth; $el.style.setProperty('--tip-x',(right?r.right+10:window.innerWidth-r.left+10)+'px'); $el.style.setProperty('--tip-y',Math.max(10,Math.min(r.top,window.innerHeight-420))+'px'); tip=true"
                        @mouseleave="tip=false">

                        @if ($lTip)
                        <div class="wmx-tip" :class="right ? 'wmx-tip--right' : 'wmx-tip--left'" x-show="tip" x-cloak>
                            <div class="wmx-tip__stats-wrap">
                                <x-characters.inventory-tooltip :item="$lTip" :inline="true" />
                            </div>
                            <div class="wmx-tip__extra">
                                <div class="flex justify-between gap-4 leading-relaxed">
                                    <span class="text-zinc-500">{{ __('web-market.table.seller') }}</span>
                                    <span class="text-zinc-200">{{ $listing->character_name }}</span>
                                </div>
                                <div class="flex justify-between gap-4 leading-relaxed">
                                    <span class="text-zinc-500">{{ __('web-market.table.price') }}</span>
                                    <span class="font-bold {{ $listing->isGold() ? 'text-amber-400' : 'text-violet-400' }}">{{ number_format($listing->price_amount) }} {{ $listing->priceTypeLabel() }}</span>
                                </div>
                                @if ($listing->expires_at)
                                    <div class="flex justify-between gap-4 leading-relaxed text-zinc-600 text-xs">
                                        <span>{{ __('web-market.table.expires') }}</span><span>{{ $listing->remaining_time }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        <div class="relative p-4 flex justify-center bg-black/40">
                            <img src="{{ asset('images/silkroad/' . $listing->icon_path) }}" alt="{{ $lName }}" class="w-12 h-12 object-contain">
                            @if ($listing->opt_level > 0)
                                <span class="absolute top-1.5 right-1.5 px-1.5 text-[0.62rem] font-black bg-linear-to-r from-violet-600 to-fuchsia-600 text-white">+{{ $listing->opt_level }}</span>
                            @endif
                        </div>

                        <div class="flex-1 p-3">
                            <h3 class="text-sm font-medium text-zinc-200 leading-tight mb-1.5">{{ $lName }}</h3>
                            <div class="flex flex-wrap gap-1 mb-1.5">
                                @if ($lLevel)<span class="text-[0.68rem] font-semibold px-1.5 bg-zinc-800 text-zinc-400">Lv.{{ $lLevel }}</span>@endif
                                @if ($lGender)<span class="text-[0.68rem] font-semibold px-1.5" style="background:rgba(59,130,246,.15);color:#93c5fd;">{{ $lGender }}</span>@endif
                                @if ($lSox && $lSox !== 'Normal')<span class="text-[0.68rem] font-semibold px-1.5 text-amber-400" style="background:rgba(234,179,8,.15);">{{ $lSox }}</span>@endif
                            </div>
                            <div class="flex justify-between text-xs text-zinc-600">
                                <span>{{ $listing->character_name }}</span>
                                @if ($listing->expires_at)<span>{{ $listing->remaining_time }}</span>@endif
                            </div>
                        </div>

                        <div class="p-3 border-t border-zinc-800 flex items-center justify-between gap-2">
                            <span class="font-bold text-sm {{ $listing->isGold() ? 'text-amber-400' : 'text-violet-400' }}">{{ number_format($listing->price_amount) }} {{ $listing->priceTypeLabel() }}</span>
                            <button wire:click="openBuyModal({{ $listing->id }})"
                                class="cursor-pointer px-3 py-1.5 text-xs font-bold uppercase tracking-wider bg-linear-to-r from-violet-600 to-fuchsia-600 text-white hover:from-violet-500 hover:to-fuchsia-500 transition shadow-[0_0_10px_rgba(139,92,246,0.3)]">{{ __('web-market.buy') }}</button>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-6">{{ $listings->links() }}</div>
        @endif
    </div>
    @endif

    {{-- ═══════════ WEB STORAGE TAB ═══════════ --}}
    @if ($activeTab === 'storage')
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Left: Character Items --}}
        <div class="bg-zinc-900 border border-violet-500/20 p-4">
            <h2 class="flex items-center gap-2 font-bold uppercase tracking-wider text-violet-400 mb-4 text-sm">
                <svg class="w-5 h-5 opacity-70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                {{ $selectedCharName ?: __('web-market.select_character') }}
            </h2>

            @foreach (['inventory' => ['items' => $inventoryItems, 'label' => __('web-market.inventory')], 'storage' => ['items' => $storageItems, 'label' => __('web-market.storage_label')]] as $srcType => $group)
                @if ($group['items']->isNotEmpty())
                <div class="mb-4">
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-2">{{ $group['label'] }}</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($group['items'] as $charItem)
                            @php
                                $ciTip = $inventoryTooltipData[(int) $charItem->ItemID] ?? null;
                                $ciCanTrade = (bool) ($charItem->CanTrade ?? true);
                            @endphp
                            <div class="relative w-12 h-12 {{ $ciCanTrade ? 'cursor-pointer group' : 'cursor-default' }}"
                                wire:key="{{ $srcType }}-{{ $charItem->ItemID }}"
                                x-data="{ tip: false, right: true, tx: '0px', ty: '0px' }"
                                @mouseenter="let r=$event.currentTarget.getBoundingClientRect(); right=r.right+390<window.innerWidth; tx=(right?r.right+10:window.innerWidth-r.left+10)+'px'; ty=Math.max(10,Math.min(r.top,window.innerHeight-420))+'px'; tip=true"
                                @mouseleave="tip=false">
                                @if ($ciTip)
                                <div class="wmx-tip" :style="right ? 'left:'+tx+';top:'+ty : 'right:'+tx+';top:'+ty" x-show="tip" x-cloak>
                                    <x-characters.inventory-tooltip :item="$ciTip" :inline="true" />
                                </div>
                                @endif
                                <img src="{{ asset('images/silkroad/' . \App\Helpers\WebmallItemIconHelper::resolveIcon($charItem->AssocFileIcon128 ?? null)) }}"
                                     alt="{{ $charItem->CodeName128 ?? '' }}"
                                     class="w-full h-full object-contain border border-zinc-700 bg-black/60 {{ $ciCanTrade ? '' : 'grayscale opacity-45' }}">
                                @if (($charItem->OptLevel ?? 0) > 0)
                                    <span class="absolute -top-1 -right-1 px-1 text-[0.55rem] font-black bg-linear-to-r from-violet-600 to-fuchsia-600 text-white">+{{ $charItem->OptLevel }}</span>
                                @endif
                                @if (! $ciCanTrade)
                                    <span class="absolute -bottom-1 -left-1 w-4 h-4 rounded-full bg-red-600 flex items-center justify-center shadow z-[2]">
                                        <svg class="w-2.5 h-2.5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
                                    </span>
                                @else
                                    <button wire:click="openTransferModal({{ $charItem->Slot }}, '{{ $srcType }}')"
                                        class="absolute inset-0 flex items-center justify-center bg-black/60 text-violet-300 text-lg opacity-0 group-hover:opacity-100 transition-opacity"
                                        {{ ($requireLogout && $selectedCharOnline) ? 'disabled' : '' }}
                                        title="{{ __('web-market.transfer_to_storage') }}">→</button>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            @endforeach

            @if ($inventoryItems->isEmpty() && $storageItems->isEmpty())
                <div class="py-6 text-center text-sm text-zinc-600">{{ __('web-market.storage.no_items') }}</div>
            @endif
        </div>

        {{-- Right: Web Storage --}}
        <div class="bg-zinc-900 border border-violet-500/20 p-4">
            @php
                $wsCount = $webStorage->count();
                $wsCountColor = $wsCount >= $webStorageLimit ? 'text-red-400' : ($wsCount >= $webStorageLimit * 0.8 ? 'text-amber-400' : 'text-zinc-600');
            @endphp
            <h2 class="flex items-center gap-2 font-bold uppercase tracking-wider text-violet-400 mb-4 text-sm">
                <svg class="w-5 h-5 opacity-70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
                {{ __('web-market.web_storage') }}
                <span class="text-xs font-semibold ml-auto {{ $wsCountColor }}">({{ $wsCount }}/{{ $webStorageLimit }})</span>
            </h2>

            @if ($webStorage->isEmpty())
                <div class="py-6 text-center text-sm text-zinc-600">{{ __('web-market.storage.empty') }}</div>
            @else
                <div class="flex flex-col gap-2 max-h-[480px] overflow-y-auto">
                    @foreach ($webStorage as $wsItem)
                        @php
                            $wsTip  = $tooltipData[(int) $wsItem->item_id64] ?? null;
                            $wsName = $wsTip?->get('ItemName') ?: ($wsItem->item_name ?: __('web-market.unknown_item'));
                        @endphp
                        <div class="relative flex items-center gap-3 px-3 py-2 border border-zinc-800 bg-zinc-950/40 {{ $wsItem->isListed() ? 'opacity-60' : '' }}"
                            x-data="{ tip: false, right: true }"
                            @mouseenter="let r=$event.currentTarget.getBoundingClientRect(); right=r.right+390<window.innerWidth; $el.style.setProperty('--tip-x',(right?r.right+10:window.innerWidth-r.left+10)+'px'); $el.style.setProperty('--tip-y',Math.max(10,Math.min(r.top,window.innerHeight-420))+'px'); tip=true"
                            @mouseleave="tip=false">
                            @if ($wsTip)
                            <div class="wmx-tip" :class="right ? 'wmx-tip--right' : 'wmx-tip--left'" x-show="tip" x-cloak>
                                <x-characters.inventory-tooltip :item="$wsTip" :inline="true" />
                            </div>
                            @endif
                            <img src="{{ asset('images/silkroad/' . $wsItem->icon_path) }}" alt="{{ $wsName }}" class="w-12 h-12 object-contain shrink-0">
                            <div class="flex-1 min-w-0">
                                <span class="text-sm font-medium text-zinc-200 flex items-center gap-1.5 flex-wrap">
                                    {{ $wsName }}
                                    @if ($wsItem->opt_level > 0)<span class="px-1.5 text-[0.62rem] font-black bg-linear-to-r from-violet-600 to-fuchsia-600 text-white">+{{ $wsItem->opt_level }}</span>@endif
                                </span>
                                @if ($wsItem->isListed())<span class="text-[0.68rem] font-semibold uppercase tracking-wider text-violet-400">{{ __('web-market.listed') }}</span>@endif
                            </div>
                            @if (! $wsItem->isListed())
                                <div class="flex gap-1.5 shrink-0">
                                    <button wire:click="openSellModal({{ $wsItem->id }})"
                                        class="cursor-pointer px-2.5 py-1 text-[0.72rem] font-bold uppercase tracking-wider bg-linear-to-r from-violet-600 to-fuchsia-600 text-white hover:from-violet-500 hover:to-fuchsia-500 transition"
                                        title="{{ __('web-market.sell') }}">{{ __('web-market.sell') }}</button>
                                    <button wire:click="openReturnModal({{ $wsItem->id }})"
                                        class="cursor-pointer px-2.5 py-1 text-[0.72rem] font-bold uppercase tracking-wider border border-zinc-700 text-zinc-400 hover:text-white hover:border-zinc-500 transition"
                                        {{ ($requireLogout && $selectedCharOnline) ? 'disabled' : '' }}
                                        title="{{ __('web-market.return_to_inventory') }}">← {{ __('web-market.return') }}</button>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
    @endif

    {{-- ═══════════ MY LISTINGS TAB ═══════════ --}}
    @if ($activeTab === 'listings')
    <div class="min-h-[200px]">
        @if ($myListings->isEmpty())
            <div class="bg-zinc-900 border border-zinc-800 p-16 text-center text-zinc-600 uppercase tracking-widest font-bold text-xs">
                {{ __('web-market.my_listings.empty') }}
            </div>
        @else
            <div class="bg-zinc-900 border border-violet-500/20 p-4 overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-zinc-800 text-left">
                            @foreach ([__('web-market.table.item'), __('web-market.table.price'), __('web-market.table.status'), __('web-market.table.seller'), __('web-market.table.buyer'), __('web-market.table.expires'), __('web-market.table.actions')] as $th)
                                <th class="px-3 py-2 text-xs font-bold uppercase tracking-wider text-zinc-500">{{ $th }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($myListings as $listing)
                            @php
                                $mlName = ($tooltipData[(int) $listing->item_id64] ?? null)?->get('ItemName') ?: ($listing->item_name ?: __('web-market.unknown_item'));
                                $statusColors = ['active' => 'text-emerald-400', 'sold' => 'text-violet-400', 'expired' => 'text-amber-400', 'cancelled' => 'text-red-400'];
                                $mlStatusColor = $statusColors[$listing->status->value] ?? 'text-zinc-400';
                            @endphp
                            <tr class="border-b border-zinc-800/60">
                                <td class="px-3 py-2">
                                    <div class="flex items-center gap-2">
                                        <img src="{{ asset('images/silkroad/' . $listing->icon_path) }}" alt="{{ $mlName }}" class="w-12 h-12 object-contain">
                                        <span class="text-zinc-200 flex items-center gap-1.5">
                                            {{ $mlName }}
                                            @if ($listing->opt_level > 0)<span class="px-1.5 text-[0.62rem] font-black bg-linear-to-r from-violet-600 to-fuchsia-600 text-white">+{{ $listing->opt_level }}</span>@endif
                                        </span>
                                    </div>
                                </td>
                                <td class="px-3 py-2">
                                    <span class="font-bold {{ $listing->isGold() ? 'text-amber-400' : 'text-violet-400' }}">{{ number_format($listing->price_amount) }} {{ $listing->priceTypeLabel() }}</span>
                                    @if ($listing->fee_amount > 0)<small class="block text-xs text-zinc-600">-{{ number_format($listing->fee_amount) }} fee</small>@endif
                                </td>
                                <td class="px-3 py-2"><span class="text-xs font-bold uppercase tracking-wider {{ $mlStatusColor }}">{{ $listing->status->getLabel() }}</span></td>
                                <td class="px-3 py-2"><span class="text-zinc-400">{{ $listing->character_name }}</span></td>
                                <td class="px-3 py-2">
                                    @if ($listing->transaction)<span class="text-zinc-400">{{ $listing->transaction->buyer_character_name }}</span>
                                    @else<span class="text-zinc-700">—</span>@endif
                                </td>
                                <td class="px-3 py-2 text-zinc-500">{{ $listing->expires_at?->format('d.m.Y H:i') ?? '—' }}</td>
                                <td class="px-3 py-2">
                                    @if ($listing->isActive())
                                        <button wire:click="openCancelModal({{ $listing->id }})"
                                            class="cursor-pointer px-2.5 py-1 text-[0.72rem] font-bold uppercase tracking-wider bg-red-600/80 hover:bg-red-600 text-white transition">{{ __('web-market.cancel_listing') }}</button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-6">{{ $myListings->links() }}</div>
            </div>
        @endif
    </div>
    @endif

    {{-- ═══════════ MODALS ═══════════ --}}

    {{-- Buy Modal --}}
    @if ($showBuyModal && $buyListing)
    @php
        $remaining  = $buyBalance !== null ? $buyBalance - $buyListing->price_amount : null;
        $canAfford  = $remaining === null || $remaining >= 0;
        $buyTip     = $tooltipData[(int) $buyListing->item_id64] ?? null;
        $buyName    = $buyTip?->get('ItemName') ?: ($buyListing->item_name ?: __('web-market.unknown_item'));
    @endphp
    @teleport('body')
    <div class="fixed inset-0 z-[9998] flex items-center justify-center bg-black/80 backdrop-blur-sm p-4 font-mono" wire:click.self="$set('showBuyModal', false)">
        <div class="bg-zinc-900 border border-violet-500/30 shadow-[0_0_60px_rgba(139,92,246,0.2)] w-full max-w-md p-6 space-y-4">
            <p class="text-xs uppercase tracking-[0.3em] text-violet-400/70">{{ __('web-market.modal.buy.title') }}</p>
            <div class="flex items-start gap-4">
                <img src="{{ asset('images/silkroad/' . $buyListing->icon_path) }}" alt="{{ $buyName }}" class="w-14 h-14 object-contain border border-zinc-700 bg-black/60 p-0.5">
                <div><strong class="text-zinc-200 flex items-center gap-1.5">{{ $buyName }}
                    @if ($buyListing->opt_level > 0)<span class="px-1.5 text-[0.62rem] font-black bg-linear-to-r from-violet-600 to-fuchsia-600 text-white">+{{ $buyListing->opt_level }}</span>@endif</strong></div>
            </div>
            <div class="h-px bg-linear-to-r from-violet-500/40 to-transparent"></div>
            <dl class="text-sm space-y-2">
                <div class="flex justify-between"><dt class="text-zinc-500 uppercase tracking-wider text-xs">{{ __('web-market.modal.buy.balance') }}</dt><dd class="font-medium text-zinc-300">{{ $buyListing->priceTypeLabel() }}</dd></div>
                <div class="flex justify-between"><dt class="text-zinc-500 uppercase tracking-wider text-xs">{{ __('web-market.table.price') }}</dt>
                    <dd class="font-bold {{ $buyListing->isGold() ? 'text-amber-400' : 'text-violet-400' }}">{{ number_format($buyListing->price_amount) }} {{ $buyListing->priceTypeLabel() }}</dd></div>
                <div class="flex justify-between"><dt class="text-zinc-500 uppercase tracking-wider text-xs">{{ __('web-market.table.seller') }}</dt><dd class="text-zinc-300">{{ $buyListing->character_name }}</dd></div>
                @if ($selectedCharName)<div class="flex justify-between"><dt class="text-zinc-500 uppercase tracking-wider text-xs">{{ __('web-market.table.buyer') }}</dt><dd class="text-zinc-300">{{ $selectedCharName }}</dd></div>@endif
            </dl>
            @if ($buyBalance !== null)
            <div class="border border-zinc-800 bg-zinc-950/50 p-3 text-sm space-y-1">
                <div class="flex justify-between text-zinc-500 text-xs"><span>{{ __('web-market.modal.buy.balance') }}</span><span>{{ number_format($buyBalance) }}</span></div>
                <div class="flex justify-between text-red-400 text-xs"><span>{{ __('web-market.table.price') }}</span><span>−{{ number_format($buyListing->price_amount) }}</span></div>
                <div class="border-t border-zinc-800 pt-1 flex justify-between font-bold text-xs {{ $canAfford ? 'text-violet-400' : 'text-red-400' }}">
                    <span>{{ __('web-market.modal.buy.confirm') }}</span><span>{{ number_format($remaining) }}</span>
                </div>
            </div>
            @endif
            <p class="text-xs text-zinc-600">{{ __('web-market.modal.buy.delivered_to_storage') }}</p>
            <div class="flex gap-3 justify-end pt-2">
                <button wire:click="$set('showBuyModal', false)" class="cursor-pointer px-4 py-2 text-xs font-bold uppercase tracking-wider border border-zinc-700 text-zinc-400 hover:text-white hover:border-zinc-500 transition">{{ __('web-market.cancel') }}</button>
                <button wire:click="confirmBuy" wire:loading.attr="disabled" {{ ! $canAfford ? 'disabled' : '' }}
                    class="px-4 py-2 text-xs font-bold uppercase tracking-wider transition disabled:opacity-50 disabled:cursor-not-allowed {{ $canAfford ? 'cursor-pointer bg-linear-to-r from-violet-600 to-fuchsia-600 text-white shadow-[0_0_15px_rgba(139,92,246,0.4)] hover:from-violet-500 hover:to-fuchsia-500' : 'bg-zinc-800 text-zinc-500 cursor-not-allowed' }}">
                    <span wire:loading.remove wire:target="confirmBuy">{{ __('web-market.modal.buy.confirm') }}</span>
                    <span wire:loading wire:target="confirmBuy">{{ __('web-market.loading') }}</span>
                </button>
            </div>
        </div>
    </div>
    @endteleport
    @endif

    {{-- Transfer Modal --}}
    @if ($showTransferModal)
    @teleport('body')
    <div class="fixed inset-0 z-[9998] flex items-center justify-center bg-black/80 backdrop-blur-sm p-4 font-mono" wire:click.self="$set('showTransferModal', false)">
        <div class="bg-zinc-900 border border-violet-500/30 shadow-[0_0_60px_rgba(139,92,246,0.2)] w-full max-w-md p-6 space-y-4">
            <p class="text-xs uppercase tracking-[0.3em] text-violet-400/70">{{ __('web-market.modal.transfer.title') }}</p>
            <p class="text-sm text-zinc-400">{{ __('web-market.modal.transfer.body', ['source' => $transferSourceType]) }}</p>
            <div class="flex gap-3 justify-end pt-2">
                <button wire:click="$set('showTransferModal', false)" class="cursor-pointer px-4 py-2 text-xs font-bold uppercase tracking-wider border border-zinc-700 text-zinc-400 hover:text-white hover:border-zinc-500 transition">{{ __('web-market.cancel') }}</button>
                <button wire:click="confirmTransfer" wire:loading.attr="disabled" class="cursor-pointer px-4 py-2 text-xs font-bold uppercase tracking-wider bg-linear-to-r from-violet-600 to-fuchsia-600 text-white hover:from-violet-500 hover:to-fuchsia-500 transition shadow-[0_0_15px_rgba(139,92,246,0.4)] disabled:opacity-50">
                    <span wire:loading.remove wire:target="confirmTransfer">{{ __('web-market.modal.transfer.confirm') }}</span>
                    <span wire:loading wire:target="confirmTransfer">{{ __('web-market.loading') }}</span>
                </button>
            </div>
        </div>
    </div>
    @endteleport
    @endif

    {{-- Return Modal --}}
    @if ($showReturnModal && $returnItem)
    @php
        $returnTip  = $tooltipData[(int) $returnItem->item_id64] ?? null;
        $returnName = $returnTip?->get('ItemName') ?: ($returnItem->item_name ?: __('web-market.unknown_item'));
    @endphp
    @teleport('body')
    <div class="fixed inset-0 z-[9998] flex items-center justify-center bg-black/80 backdrop-blur-sm p-4 font-mono" wire:click.self="$set('showReturnModal', false)">
        <div class="bg-zinc-900 border border-violet-500/30 shadow-[0_0_60px_rgba(139,92,246,0.2)] w-full max-w-md p-6 space-y-4">
            <p class="text-xs uppercase tracking-[0.3em] text-violet-400/70">{{ __('web-market.modal.return.title') }}</p>
            <div class="flex items-start gap-4">
                <img src="{{ asset('images/silkroad/' . $returnItem->icon_path) }}" alt="{{ $returnName }}" class="w-14 h-14 object-contain border border-zinc-700 bg-black/60 p-0.5">
                <div>
                    <strong class="text-zinc-200 flex items-center gap-1.5">{{ $returnName }}
                        @if ($returnItem->opt_level > 0)<span class="px-1.5 text-[0.62rem] font-black bg-linear-to-r from-violet-600 to-fuchsia-600 text-white">+{{ $returnItem->opt_level }}</span>@endif</strong>
                    <p class="text-xs text-zinc-500 mt-1">{{ __('web-market.modal.return.body') }}</p>
                </div>
            </div>
            <div class="flex gap-3 justify-end pt-2">
                <button wire:click="$set('showReturnModal', false)" class="cursor-pointer px-4 py-2 text-xs font-bold uppercase tracking-wider border border-zinc-700 text-zinc-400 hover:text-white hover:border-zinc-500 transition">{{ __('web-market.cancel') }}</button>
                <button wire:click="confirmReturn" wire:loading.attr="disabled" class="cursor-pointer px-4 py-2 text-xs font-bold uppercase tracking-wider bg-linear-to-r from-violet-600 to-fuchsia-600 text-white hover:from-violet-500 hover:to-fuchsia-500 transition shadow-[0_0_15px_rgba(139,92,246,0.4)] disabled:opacity-50">
                    <span wire:loading.remove wire:target="confirmReturn">{{ __('web-market.modal.return.confirm') }}</span>
                    <span wire:loading wire:target="confirmReturn">{{ __('web-market.loading') }}</span>
                </button>
            </div>
        </div>
    </div>
    @endteleport
    @endif

    {{-- Sell Modal --}}
    @if ($showSellModal && $webStorageItem)
    @php
        $sellData    = $webStorageItem->item_data ?? [];
        $sellTip     = $tooltipData[(int) $webStorageItem->item_id64] ?? null;
        $sellName    = $sellTip?->get('ItemName') ?: ($webStorageItem->item_name ?: __('web-market.unknown_item'));
        $sellReqLvl  = $sellTip?->get('ReqLevel1') ?: ($sellData['req_level'] ?? null);
    @endphp
    @teleport('body')
    <div class="fixed inset-0 z-[9998] flex items-center justify-center bg-black/80 backdrop-blur-sm p-4 font-mono" wire:click.self="$set('showSellModal', false)">
        <div class="bg-zinc-900 border border-violet-500/30 shadow-[0_0_60px_rgba(139,92,246,0.2)] w-full max-w-lg p-6 space-y-4">
            <p class="text-xs uppercase tracking-[0.3em] text-violet-400/70">{{ __('web-market.modal.sell.title') }}</p>
            <div class="flex items-start gap-4">
                <img src="{{ asset('images/silkroad/' . $webStorageItem->icon_path) }}" alt="{{ $sellName }}" class="w-14 h-14 object-contain border border-zinc-700 bg-black/60 p-0.5">
                <div>
                    <strong class="text-zinc-200 flex items-center gap-1.5">{{ $sellName }}
                        @if ($webStorageItem->opt_level > 0)<span class="px-1.5 text-[0.62rem] font-black bg-linear-to-r from-violet-600 to-fuchsia-600 text-white">+{{ $webStorageItem->opt_level }}</span>@endif</strong>
                    @if ($sellReqLvl)<div class="text-xs text-zinc-500 mt-0.5">Req. Level {{ $sellReqLvl }}</div>@endif
                </div>
            </div>
            <div class="flex flex-col gap-3">
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-semibold uppercase tracking-wider text-zinc-500">{{ __('web-market.modal.sell.price_type') }}</label>
                    <select wire:model="sellPriceType" class="w-full bg-zinc-950/50 border border-zinc-800 text-zinc-200 px-3 py-2 text-sm focus:border-violet-500 focus:outline-none">
                        @if ($allowGold)<option value="gold">Gold</option>@endif
                        @if ($allowSilk)@foreach ($silkTypeOptions as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach @endif
                    </select>
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-semibold uppercase tracking-wider text-zinc-500">{{ __('web-market.modal.sell.price') }}</label>
                    <input wire:model="sellPriceAmount" type="number" min="1" class="w-full bg-zinc-950/50 border border-zinc-800 text-zinc-200 px-3 py-2 text-sm focus:border-violet-500 focus:outline-none" placeholder="{{ __('web-market.modal.sell.price_placeholder') }}">
                    @error('sellPriceAmount') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-semibold uppercase tracking-wider text-zinc-500">{{ __('web-market.modal.sell.duration') }} ({{ __('web-market.hours') }})</label>
                    <input wire:model="sellDurationHours" type="number" min="1" max="{{ $maxDuration }}" class="w-full bg-zinc-950/50 border border-zinc-800 text-zinc-200 px-3 py-2 text-sm focus:border-violet-500 focus:outline-none">
                    <small class="text-xs text-zinc-600">{{ __('web-market.modal.sell.max_duration', ['max' => $maxDuration]) }}</small>
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-semibold uppercase tracking-wider text-zinc-500">{{ __('web-market.modal.sell.description') }}</label>
                    <textarea wire:model="sellDescription" rows="2" class="w-full bg-zinc-950/50 border border-zinc-800 text-zinc-200 px-3 py-2 text-sm focus:border-violet-500 focus:outline-none" placeholder="{{ __('web-market.modal.sell.description_placeholder') }}"></textarea>
                </div>
            </div>
            <div class="flex gap-3 justify-end pt-2">
                <button wire:click="$set('showSellModal', false)" class="cursor-pointer px-4 py-2 text-xs font-bold uppercase tracking-wider border border-zinc-700 text-zinc-400 hover:text-white hover:border-zinc-500 transition">{{ __('web-market.cancel') }}</button>
                <button wire:click="confirmSell" wire:loading.attr="disabled" class="cursor-pointer px-4 py-2 text-xs font-bold uppercase tracking-wider bg-linear-to-r from-violet-600 to-fuchsia-600 text-white hover:from-violet-500 hover:to-fuchsia-500 transition shadow-[0_0_15px_rgba(139,92,246,0.4)] disabled:opacity-50">
                    <span wire:loading.remove wire:target="confirmSell">{{ __('web-market.modal.sell.confirm') }}</span>
                    <span wire:loading wire:target="confirmSell">{{ __('web-market.loading') }}</span>
                </button>
            </div>
        </div>
    </div>
    @endteleport
    @endif

    {{-- Cancel Modal --}}
    @if ($showCancelModal)
    @teleport('body')
    <div class="fixed inset-0 z-[9998] flex items-center justify-center bg-black/80 backdrop-blur-sm p-4 font-mono" wire:click.self="$set('showCancelModal', false)">
        <div class="bg-zinc-900 border border-violet-500/30 shadow-[0_0_60px_rgba(139,92,246,0.2)] w-full max-w-md p-6 space-y-4">
            <p class="text-xs uppercase tracking-[0.3em] text-violet-400/70">{{ __('web-market.modal.cancel.title') }}</p>
            <p class="text-sm text-zinc-400">{{ __('web-market.modal.cancel.body') }}</p>
            <div class="flex gap-3 justify-end pt-2">
                <button wire:click="$set('showCancelModal', false)" class="cursor-pointer px-4 py-2 text-xs font-bold uppercase tracking-wider border border-zinc-700 text-zinc-400 hover:text-white hover:border-zinc-500 transition">{{ __('web-market.cancel') }}</button>
                <button wire:click="confirmCancelListing" wire:loading.attr="disabled" class="cursor-pointer px-4 py-2 text-xs font-bold uppercase tracking-wider bg-red-600/80 hover:bg-red-600 text-white transition disabled:opacity-50">
                    <span wire:loading.remove wire:target="confirmCancelListing">{{ __('web-market.modal.cancel.confirm') }}</span>
                    <span wire:loading wire:target="confirmCancelListing">{{ __('web-market.loading') }}</span>
                </button>
            </div>
        </div>
    </div>
    @endteleport
    @endif
</div>

@once
<style>
    [x-cloak] { display: none !important; }
    .wmx-tip { position: fixed; top: var(--tip-y,0px); z-index: 9999; pointer-events: none; max-width: min(380px, calc(100vw - 20px)); width: max-content; }
    .wmx-tip--right { left: var(--tip-x,0px); right: auto; }
    .wmx-tip--left  { right: var(--tip-x,0px); left: auto; }
    .wmx-tip > div, .wmx-tip__stats-wrap > div { min-width: 0 !important; max-width: 100% !important; width: 100% !important; }
    .wmx-tip__stats-wrap > div { border-bottom-left-radius: 0 !important; border-bottom-right-radius: 0 !important; border-bottom: none !important; }
    .wmx-tip__extra { background: #09090b; border: 1px solid rgba(139,92,246,0.35); border-top: none; padding: 6px 12px; font-size: .8rem; }
</style>
@endonce
