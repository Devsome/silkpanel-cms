<div>
    {{-- Flash messages --}}
    @if (session('webmall_success'))
        <div class="mb-6 ag-alert-success">
            {{ session('webmall_success') }}
        </div>
    @endif
    @if (session('webmall_error'))
        <div class="mb-6 ag-alert-error">
            {{ session('webmall_error') }}
        </div>
    @endif

    {{-- Character selector --}}
    @if ($characters->isNotEmpty())
        <div class="ag-card p-5 mb-8">
            <p class="text-[10px] ag-font-display font-bold ag-text-muted uppercase tracking-widest mb-3">
                {{ __('webmall.ui.purchase_as_character') }}
            </p>
            <div class="flex flex-wrap gap-2">
                @foreach ($characters as $char)
                    <button wire:click="selectCharacter({{ $char->CharID }}, '{{ $char->CharName16 }}')"
                        class="cursor-pointer px-4 py-1.5 rounded ag-font-display text-xs font-bold uppercase tracking-wider transition-all
                            {{ $selectedCharId == $char->CharID
                                ? 'ag-btn-primary'
                                : 'ag-card-low ag-text-muted hover:ag-text-primary' }}">
                        {{ $char->CharName16 }}
                        <span class="ml-1 opacity-60">Lv.{{ $char->CurLevel }}</span>
                    </button>
                @endforeach
            </div>
        </div>
    @else
        <div class="mb-6 ag-alert-warning">
            {{ __('webmall.ui.no_characters') }}
        </div>
    @endif

    {{-- Categories & Items --}}
    @if ($categories->isEmpty())
        <div class="ag-card p-16 text-center">
            <svg class="mx-auto h-12 w-12 mb-4 opacity-30 ag-text-muted" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <p class="ag-font-display font-bold uppercase tracking-widest ag-text-muted">
                {{ __('webmall.ui.empty_heading') }}</p>
            <p class="text-sm ag-text-muted mt-1">{{ __('webmall.ui.empty_description') }}</p>
        </div>
    @else
        {{-- Tab navigation --}}
        <div class="mb-6 overflow-x-auto">
            <nav class="flex gap-1 min-w-max border-b ag-divider pb-0" aria-label="Webmall categories">
                @foreach ($categories as $category)
                    @if ($category->activeItems->isNotEmpty())
                        <button wire:click="$set('activeTab', '{{ $category->slug }}')"
                            class="cursor-pointer px-5 py-2.5 ag-font-display text-xs font-bold uppercase tracking-widest whitespace-nowrap border-b-2 transition-all -mb-px
                                {{ $activeTab === $category->slug
                                    ? 'border-cyan-400 ag-text-primary'
                                    : 'border-transparent ag-text-muted hover:ag-text-primary hover:border-cyan-700/50' }}">
                            {{ $category->name }}
                        </button>
                    @endif
                @endforeach
            </nav>
        </div>

        {{-- Active tab content --}}
        @foreach ($categories as $category)
            @if ($category->activeItems->isNotEmpty() && $activeTab === $category->slug)
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                    @foreach ($category->activeItems as $item)
                        @php
                            $remaining = $item->remainingStock();
                            $soldOut = $remaining !== null && $remaining <= 0;
                            $refObj = $refObjs[$item->ref_item_id] ?? null;
                            $iconPath = \App\Helpers\WebmallItemIconHelper::resolveIcon($refObj?->AssocFileIcon128);
                            $isSeal = $refObj && \App\Helpers\WebmallItemIconHelper::isSeal($refObj);
                            $itemImageUrl = filled($item->custom_image_path)
                                ? asset('storage/' . $item->custom_image_path)
                                : asset('images/silkroad/' . $iconPath);
                        @endphp
                        <div
                            class="relative ag-card p-4 flex flex-col gap-2 transition-all hover:-translate-y-0.5 hover:shadow-2xl {{ $soldOut ? 'opacity-50' : '' }}">

                            {{-- HOT badge --}}
                            @if ($item->is_hot)
                                <span
                                    class="absolute top-2 right-2 inline-flex items-center px-1.5 py-0.5 rounded text-[10px] ag-font-display font-black uppercase tracking-wider bg-red-500 text-white">
                                    HOT
                                </span>
                            @endif

                            {{-- Item icon --}}
                            <div class="flex justify-center mb-1">
                                <div class="relative inline-flex">
                                    @if ($isSeal)
                                        <img class="pointer-events-none absolute inset-0 size-12"
                                            src="{{ asset('images/silkroad/item/seal.gif') }}" />
                                    @endif
                                    <img src="{{ $itemImageUrl }}" alt="{{ $item->item_name_snapshot ?? 'Item' }}"
                                        class="size-12 object-contain rounded p-0.5"
                                        style="border:1px solid rgba(34,211,238,0.2);background:rgba(13,18,36,0.6);"
                                        loading="lazy">
                                </div>
                            </div>

                            {{-- Item name --}}
                            <p class="text-sm font-medium ag-text-surface leading-tight pr-6">
                                {{ $item->item_name_snapshot ?? 'Item #' . $item->ref_item_id }}
                                @if ((int) $item->amount > 1)<span class="ml-1 text-xs font-bold opacity-80">×{{ $item->amount }}</span>@endif
                            </p>

                            {{-- Price --}}
                            <p class="text-sm font-bold ag-font-display
                                {{ $item->isPriceGold() ? 'ag-stat-amber' : 'ag-text-primary' }}">
                                {{ number_format($item->price_value) }}
                                <span class="text-xs font-normal ag-text-muted">
                                    {{ $item->priceTypeLabel() }}
                                </span>
                            </p>

                            {{-- Stock --}}
                            @if ($remaining !== null)
                                <p class="text-xs ag-text-muted">
                                    @if ($soldOut)
                                        <span class="text-red-400 font-bold">{{ __('webmall.ui.sold_out') }}</span>
                                    @else
                                        {{ __('webmall.ui.remaining_stock', ['count' => $remaining]) }}
                                    @endif
                                </p>
                            @endif

                            {{-- SOX type --}}
                            @if ($refObj && isset($refObj->SoxType) && $refObj->SoxType !== 'Normal')
                                <span class="text-xs ag-font-display font-bold ag-stat-amber uppercase tracking-wider">
                                    {{ $refObj->SoxType }}
                                </span>
                            @endif

                            {{-- Req. Level & Tradeable & Gender --}}
                            @if ($refObj)
                                <div class="flex flex-wrap gap-x-3 gap-y-0.5 text-xs ag-text-muted">
                                    @if ($refObj->ReqLevel1)
                                        <span>Lv. {{ $refObj->ReqLevel1 }}+</span>
                                    @endif
                                    @if (!$refObj->CanTrade)
                                        <span class="text-red-400">{{ __('webmall.ui.non_trade') }}</span>
                                    @endif
                                    @if ((int) $refObj->TypeID2 === 1 && !in_array((int) $refObj->TypeID3, [4, 5, 6], true) && !is_null($refObj->ReqGender))
                                        @if ((int) $refObj->ReqGender === 0)
                                            <span class="text-pink-400">{{ __('webmall.ui.female') }}</span>
                                        @else
                                            <span class="text-blue-400">{{ __('webmall.ui.male') }}</span>
                                        @endif
                                    @endif
                                </div>
                            @endif

                            {{-- Date range --}}
                            @if ($item->available_until)
                                <p class="text-xs ag-text-muted">
                                    {{ __('webmall.ui.until', ['date' => $item->available_until->format('d.m.Y H:i')]) }}
                                </p>
                            @endif

                            {{-- Buy button --}}
                            @if (!$soldOut && $selectedCharId)
                                @if ($requireLogout && $selectedCharOnline)
                                    <span
                                        class="mt-auto w-full text-center text-xs py-1.5 px-3 rounded ag-font-display uppercase tracking-wider ag-text-primary"
                                        style="background:rgba(34,211,238,0.05);border:1px solid rgba(34,211,238,0.2);">
                                        {{ __('webmall.error.character_must_be_offline_short') }}
                                    </span>
                                @else
                                    <button wire:click="confirmPurchase({{ $item->id }})"
                                        wire:loading.attr="disabled"
                                        class="cursor-pointer mt-auto ag-btn-primary w-full text-xs py-1.5 px-3 ag-font-display font-bold uppercase tracking-wider transition disabled:opacity-50">
                                        {{ __('webmall.ui.buy') }}
                                    </button>
                                @endif
                            @elseif ($soldOut)
                                <span
                                    class="mt-auto w-full text-center text-xs py-1.5 px-3 rounded ag-card-low ag-text-muted ag-font-display uppercase tracking-wider">
                                    {{ __('webmall.ui.sold_out_button') }}
                                </span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        @endforeach
    @endif

    {{-- Confirmation Modal --}}
    @if ($showConfirmModal && $confirmItem)
        @teleport('body')
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/75 backdrop-blur-sm" x-data
            x-on:keydown.escape.window="$wire.cancelConfirm()">
            <div class="ag-card-glow shadow-2xl w-full max-w-md mx-4 p-6 space-y-4">
                <h3 class="ag-font-display font-black uppercase tracking-widest ag-text-primary text-lg">
                    {{ __('webmall.ui.modal_title') }}
                </h3>

                <div class="flex items-center gap-3">
                    @php
                        $confirmRefObj = $refObjs[$confirmItem->ref_item_id] ?? null;
                        $confirmIcon = \App\Helpers\WebmallItemIconHelper::resolveIcon(
                            $confirmRefObj?->AssocFileIcon128,
                        );
                        $confirmIsSeal = $confirmRefObj && \App\Helpers\WebmallItemIconHelper::isSeal($confirmRefObj);
                        $confirmImageUrl = filled($confirmItem->custom_image_path)
                            ? asset('storage/' . $confirmItem->custom_image_path)
                            : asset('images/silkroad/' . $confirmIcon);
                    @endphp
                    <div class="relative inline-flex shrink-0">
                        @if ($confirmIsSeal)
                            <img class="pointer-events-none absolute inset-0 size-12"
                                src="{{ asset('images/silkroad/item/seal.gif') }}" />
                        @endif
                        <img src="{{ $confirmImageUrl }}" alt="{{ $confirmItem->item_name_snapshot ?? 'Item' }}"
                            class="size-12 object-contain rounded p-0.5"
                            style="border:1px solid rgba(34,211,238,0.2);background:rgba(13,18,36,0.6);">
                    </div>
                    <span class="text-sm font-medium ag-text-surface">
                        {{ $confirmItem->item_name_snapshot ?? 'Item #' . $confirmItem->ref_item_id }}
                    </span>
                </div>

                <dl class="text-sm space-y-2">
                    <div class="flex justify-between">
                        <dt class="ag-text-muted">{{ __('webmall.ui.modal_item') }}</dt>
                        <dd class="font-medium ag-text-surface">
                            {{ $confirmItem->item_name_snapshot ?? 'Item #' . $confirmItem->ref_item_id }}
                        </dd>
                    </div>
                    @if ((int) $confirmItem->amount > 1)
                        <div class="flex justify-between">
                            <dt class="ag-text-muted">{{ __('webmall.ui.modal_amount') }}</dt>
                            <dd class="font-medium ag-text-surface">×{{ $confirmItem->amount }}</dd>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <dt class="ag-text-muted">{{ __('webmall.ui.modal_price') }}</dt>
                        <dd class="font-bold ag-text-primary">
                            {{ number_format($confirmItem->price_value) }}
                            {{ $confirmItem->priceTypeLabel() }}
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="ag-text-muted">{{ __('webmall.ui.modal_character') }}</dt>
                        <dd class="font-medium ag-text-surface">{{ $selectedCharName }}</dd>
                    </div>
                </dl>

                {{-- Balance breakdown --}}
                @if ($confirmBalance !== null)
                    <div class="rounded-lg p-3 text-sm space-y-1 ag-card-low"
                        style="border:1px solid rgba(34,211,238,0.1);">
                        <div class="flex justify-between ag-text-muted">
                            <span>{{ __('webmall.ui.balance_current', ['type' => $confirmItem->priceTypeLabel() . ($confirmItem->price_type === 'gold' && $selectedCharName ? ' (' . $selectedCharName . ')' : '')]) }}</span>
                            <span>{{ number_format($confirmBalance) }}</span>
                        </div>
                        <div class="flex justify-between text-red-400">
                            <span>{{ __('webmall.ui.balance_price') }}</span>
                            <span>{{ number_format($confirmItem->price_value) }}</span>
                        </div>
                        <div class="border-t pt-1 flex justify-between font-bold
                            {{ $confirmBalance - $confirmItem->price_value >= 0 ? 'ag-text-primary' : 'text-red-400' }}"
                            style="border-color:rgba(34,211,238,0.1);">
                            <span>{{ __('webmall.ui.balance_remaining') }}</span>
                            <span>{{ number_format($confirmBalance - $confirmItem->price_value) }}</span>
                        </div>
                    </div>
                @endif

                <p class="text-xs ag-text-muted">
                    {{ __('webmall.ui.modal_disclaimer') }}
                </p>

                @php
                    $insufficientBalance = $confirmBalance !== null && $confirmBalance - $confirmItem->price_value < 0;
                    $blockedByOnline = $requireLogout && $selectedCharOnline;
                    $confirmDisabled = $insufficientBalance || $blockedByOnline;
                @endphp

                @if ($blockedByOnline)
                    <div class="rounded p-3 text-sm ag-text-primary"
                        style="background:rgba(34,211,238,0.05);border:1px solid rgba(34,211,238,0.2);">
                        {{ __('webmall.error.character_must_be_offline') }}
                    </div>
                @endif

                <div class="flex gap-3 justify-end pt-2">
                    <button wire:click="cancelConfirm"
                        class="cursor-pointer px-4 py-2 text-xs ag-font-display font-bold uppercase tracking-wider rounded ag-btn-secondary transition">
                        {{ __('webmall.ui.modal_cancel') }}
                    </button>
                    <button wire:click="executePurchase" wire:loading.attr="disabled" @disabled($confirmDisabled)
                        class="px-4 py-2 text-xs ag-font-display font-bold uppercase tracking-wider rounded transition disabled:opacity-50 disabled:cursor-not-allowed
                            {{ $confirmDisabled ? 'ag-card-low ag-text-muted cursor-not-allowed' : 'cursor-pointer ag-btn-primary' }}">
                        <span wire:loading.remove wire:target="executePurchase">{{ __('webmall.ui.modal_confirm') }}</span>
                        <span wire:loading wire:target="executePurchase">{{ __('webmall.ui.modal_processing') }}</span>
                    </button>
                </div>
            </div>
        </div>
        @endteleport
    @endif
</div>
