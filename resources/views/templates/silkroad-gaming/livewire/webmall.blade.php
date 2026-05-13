<div>
    {{-- Flash messages --}}
    @if (session('webmall_success'))
        <div class="mb-6 rounded-xl border border-emerald-800/40 bg-emerald-900/20 p-4">
            <p class="text-sm text-emerald-300">{{ session('webmall_success') }}</p>
        </div>
    @endif
    @if (session('webmall_error'))
        <div class="mb-6 rounded-xl border border-red-800/40 bg-red-900/20 p-4">
            <p class="text-sm text-red-300">{{ session('webmall_error') }}</p>
        </div>
    @endif

    {{-- Character selector --}}
    @if ($characters->isNotEmpty())
        <div class="rounded-2xl border border-gray-800 bg-gray-900/50 backdrop-blur p-5 mb-8">
            <p class="text-xs font-bold uppercase tracking-widest text-emerald-400/70 mb-3">
                {{ __('webmall.ui.purchase_as_character') }}
            </p>
            <div class="flex flex-wrap gap-2">
                @foreach ($characters as $char)
                    <button wire:click="selectCharacter({{ $char->CharID }}, '{{ $char->CharName16 }}')"
                        class="cursor-pointer px-4 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider transition-all
                            {{ $selectedCharId == $char->CharID
                                ? 'bg-gradient-to-r from-emerald-500 to-cyan-500 text-gray-950 shadow-lg shadow-emerald-500/20'
                                : 'border border-gray-700 bg-gray-900/60 text-gray-400 hover:text-white hover:border-emerald-500/40' }}">
                        {{ $char->CharName16 }}
                        <span class="ml-1 opacity-60">Lv.{{ $char->CurLevel }}</span>
                    </button>
                @endforeach
            </div>
        </div>
    @else
        <div class="mb-6 rounded-xl border border-yellow-800/30 bg-yellow-900/10 p-4">
            <p class="text-sm text-yellow-300">{{ __('webmall.ui.no_characters') }}</p>
        </div>
    @endif

    {{-- Categories & Items --}}
    @if ($categories->isEmpty())
        <div class="rounded-2xl border border-gray-800 bg-gray-900/50 p-16 text-center">
            <svg class="mx-auto h-12 w-12 mb-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <p class="font-bold uppercase tracking-widest text-gray-500">{{ __('webmall.ui.empty_heading') }}</p>
            <p class="text-sm text-gray-600 mt-1">{{ __('webmall.ui.empty_description') }}</p>
        </div>
    @else
        {{-- Tab navigation --}}
        <div class="mb-6 overflow-x-auto">
            <nav class="flex gap-1 min-w-max border-b border-gray-800 pb-0" aria-label="Webmall categories">
                @foreach ($categories as $category)
                    @if ($category->activeItems->isNotEmpty())
                        <button wire:click="$set('activeTab', '{{ $category->slug }}')"
                            class="cursor-pointer px-5 py-2.5 text-xs font-bold uppercase tracking-widest whitespace-nowrap border-b-2 transition-all -mb-px
                                {{ $activeTab === $category->slug
                                    ? 'border-emerald-500 text-emerald-400'
                                    : 'border-transparent text-gray-500 hover:text-white hover:border-emerald-700/50' }}">
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
                        @endphp
                        <div
                            class="relative rounded-2xl border border-gray-800 bg-gray-900/50 backdrop-blur p-4 flex flex-col gap-2 transition-all hover:-translate-y-0.5 hover:border-emerald-500/30 hover:shadow-lg hover:shadow-emerald-900/20 {{ $soldOut ? 'opacity-50' : '' }}">

                            @if ($item->is_hot)
                                <span
                                    class="absolute top-2 right-2 inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-red-500 text-white">
                                    HOT
                                </span>
                            @endif

                            <div class="flex justify-center mb-1">
                                <div class="relative inline-flex">
                                    @if ($isSeal)
                                        <img class="pointer-events-none absolute inset-0 size-12"
                                            src="{{ asset('images/silkroad/item/seal.gif') }}" />
                                    @endif
                                    <img src="{{ asset('images/silkroad/' . $iconPath) }}"
                                        alt="{{ $item->item_name_snapshot ?? 'Item' }}"
                                        class="size-12 object-contain rounded border border-gray-700 bg-black/40 p-0.5"
                                        loading="lazy">
                                </div>
                            </div>

                            <p class="text-sm font-medium text-gray-100 leading-tight pr-6">
                                {{ $item->item_name_snapshot ?? 'Item #' . $item->ref_item_id }}
                            </p>

                            <p
                                class="text-sm font-bold
                                {{ $item->isPriceGold() ? 'text-amber-400' : 'text-emerald-400' }}">
                                {{ number_format($item->price_value) }}
                                <span class="text-xs font-normal text-gray-500">{{ $item->priceTypeLabel() }}</span>
                            </p>

                            @if ($remaining !== null)
                                <p class="text-xs text-gray-500">
                                    @if ($soldOut)
                                        <span class="text-red-400 font-bold">{{ __('webmall.ui.sold_out') }}</span>
                                    @else
                                        {{ __('webmall.ui.remaining_stock', ['count' => $remaining]) }}
                                    @endif
                                </p>
                            @endif

                            @if ($refObj && isset($refObj->SoxType) && $refObj->SoxType !== 'Normal')
                                <span
                                    class="text-xs font-bold text-yellow-400 uppercase tracking-wider">{{ $refObj->SoxType }}</span>
                            @endif

                            @if ($refObj)
                                <div class="flex flex-wrap gap-x-3 gap-y-0.5 text-xs text-gray-600">
                                    @if ($refObj->ReqLevel1)
                                        <span>Lv. {{ $refObj->ReqLevel1 }}+</span>
                                    @endif
                                    @if (!$refObj->CanTrade)
                                        <span class="text-red-400">{{ __('webmall.ui.non_trade') }}</span>
                                    @endif
                                    @if ((int) $refObj->TypeID2 === 1 && !in_array((int) $refObj->TypeID3, [4, 6], true) && !is_null($refObj->ReqGender))
                                        @if ((int) $refObj->ReqGender === 0)
                                            <span class="text-pink-400">{{ __('webmall.ui.female') }}</span>
                                        @else
                                            <span class="text-blue-400">{{ __('webmall.ui.male') }}</span>
                                        @endif
                                    @endif
                                </div>
                            @endif

                            @if ($item->available_until)
                                <p class="text-xs text-gray-600">
                                    {{ __('webmall.ui.until', ['date' => $item->available_until->format('d.m.Y H:i')]) }}
                                </p>
                            @endif

                            @if (!$soldOut && $selectedCharId)
                                @if ($requireLogout && $selectedCharOnline)
                                    <span
                                        class="mt-auto w-full text-center text-xs py-1.5 px-3 rounded-lg bg-yellow-900/20 border border-yellow-800/30 text-yellow-400">
                                        {{ __('webmall.error.character_must_be_offline_short') }}
                                    </span>
                                @else
                                    <button wire:click="confirmPurchase({{ $item->id }})"
                                        wire:loading.attr="disabled"
                                        class="cursor-pointer mt-auto w-full text-xs py-1.5 px-3 rounded-lg bg-gradient-to-r from-emerald-500 to-cyan-500 text-gray-950 font-bold uppercase tracking-wider hover:brightness-110 transition disabled:opacity-50">
                                        {{ __('webmall.ui.buy') }}
                                    </button>
                                @endif
                            @elseif ($soldOut)
                                <span
                                    class="mt-auto w-full text-center text-xs py-1.5 px-3 rounded-lg bg-gray-900/60 border border-gray-800 text-gray-600 uppercase tracking-wider">
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
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm" x-data
            x-on:keydown.escape.window="$wire.cancelConfirm()">
            <div class="rounded-2xl border border-gray-700 bg-gray-900 shadow-2xl w-full max-w-md mx-4 p-6 space-y-4">
                <h3 class="font-bold uppercase tracking-widest text-white text-lg">
                    {{ __('webmall.ui.modal_title') }}
                </h3>

                <div class="flex items-center gap-3">
                    @php
                        $confirmRefObj = $refObjs[$confirmItem->ref_item_id] ?? null;
                        $confirmIcon = \App\Helpers\WebmallItemIconHelper::resolveIcon(
                            $confirmRefObj?->AssocFileIcon128,
                        );
                        $confirmIsSeal = $confirmRefObj && \App\Helpers\WebmallItemIconHelper::isSeal($confirmRefObj);
                    @endphp
                    <div class="relative inline-flex shrink-0">
                        @if ($confirmIsSeal)
                            <img class="pointer-events-none absolute inset-0 size-12"
                                src="{{ asset('images/silkroad/item/seal.gif') }}" />
                        @endif
                        <img src="{{ asset('images/silkroad/' . $confirmIcon) }}"
                            alt="{{ $confirmItem->item_name_snapshot ?? 'Item' }}"
                            class="size-12 object-contain rounded border border-gray-700 bg-black/40 p-0.5">
                    </div>
                    <span class="text-sm font-medium text-gray-100">
                        {{ $confirmItem->item_name_snapshot ?? 'Item #' . $confirmItem->ref_item_id }}
                    </span>
                </div>

                <dl class="text-sm space-y-2">
                    <div class="flex justify-between">
                        <dt class="text-gray-400">{{ __('webmall.ui.modal_item') }}</dt>
                        <dd class="font-medium text-gray-100">
                            {{ $confirmItem->item_name_snapshot ?? 'Item #' . $confirmItem->ref_item_id }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-400">{{ __('webmall.ui.modal_price') }}</dt>
                        <dd class="font-bold text-emerald-400">{{ number_format($confirmItem->price_value) }}
                            {{ $confirmItem->priceTypeLabel() }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-400">{{ __('webmall.ui.modal_character') }}</dt>
                        <dd class="font-medium text-gray-100">{{ $selectedCharName }}</dd>
                    </div>
                </dl>

                @if ($confirmBalance !== null)
                    <div class="rounded-xl border border-gray-800 bg-gray-900/80 p-3 text-sm space-y-1">
                        <div class="flex justify-between text-gray-400">
                            <span>{{ __('webmall.ui.balance_current', ['type' => $confirmItem->priceTypeLabel() . ($confirmItem->price_type === 'gold' && $selectedCharName ? ' (' . $selectedCharName . ')' : '')]) }}</span>
                            <span>{{ number_format($confirmBalance) }}</span>
                        </div>
                        <div class="flex justify-between text-red-400">
                            <span>{{ __('webmall.ui.balance_price') }}</span>
                            <span>{{ number_format($confirmItem->price_value) }}</span>
                        </div>
                        <div
                            class="border-t border-gray-800 pt-1 flex justify-between font-bold
                            {{ $confirmBalance - $confirmItem->price_value >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                            <span>{{ __('webmall.ui.balance_remaining') }}</span>
                            <span>{{ number_format($confirmBalance - $confirmItem->price_value) }}</span>
                        </div>
                    </div>
                @endif

                <p class="text-xs text-gray-600">{{ __('webmall.ui.modal_disclaimer') }}</p>

                @php
                    $insufficientBalance = $confirmBalance !== null && $confirmBalance - $confirmItem->price_value < 0;
                    $blockedByOnline = $requireLogout && $selectedCharOnline;
                    $confirmDisabled = $insufficientBalance || $blockedByOnline;
                @endphp

                @if ($blockedByOnline)
                    <div class="rounded-xl border border-yellow-800/30 bg-yellow-900/10 p-3 text-sm text-yellow-400">
                        {{ __('webmall.error.character_must_be_offline') }}
                    </div>
                @endif

                <div class="flex gap-3 justify-end pt-2">
                    <button wire:click="cancelConfirm"
                        class="cursor-pointer px-4 py-2 text-xs font-bold uppercase tracking-wider rounded-lg border border-gray-700 bg-gray-900/60 text-gray-400 hover:text-white hover:border-gray-600 transition">
                        {{ __('webmall.ui.modal_cancel') }}
                    </button>
                    <button wire:click="executePurchase" wire:loading.attr="disabled" @disabled($confirmDisabled)
                        class="px-4 py-2 text-xs font-bold uppercase tracking-wider rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed
                            {{ $confirmDisabled ? 'bg-gray-800 text-gray-500 cursor-not-allowed' : 'cursor-pointer bg-gradient-to-r from-emerald-500 to-cyan-500 text-gray-950 hover:brightness-110' }}">
                        <span wire:loading.remove
                            wire:target="executePurchase">{{ __('webmall.ui.modal_confirm') }}</span>
                        <span wire:loading wire:target="executePurchase">{{ __('webmall.ui.modal_processing') }}</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
