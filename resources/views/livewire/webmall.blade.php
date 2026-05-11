<div>
    {{-- Flash messages --}}
    @if (session('webmall_success'))
        <div
            class="mb-4 p-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 text-green-800 dark:text-green-300 rounded-lg text-sm">
            {{ session('webmall_success') }}
        </div>
    @endif
    @if (session('webmall_error'))
        <div
            class="mb-4 p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 text-red-800 dark:text-red-300 rounded-lg text-sm">
            {{ session('webmall_error') }}
        </div>
    @endif

    {{-- Character selector --}}
    @if ($characters->isNotEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wide">
                {{ __('webmall.ui.purchase_as_character') }}</p>
            <div class="flex flex-wrap gap-2">
                @foreach ($characters as $char)
                    <button wire:click="selectCharacter({{ $char->CharID }}, '{{ $char->CharName16 }}')"
                        class="cursor-pointer px-3 py-1.5 rounded-full text-sm font-medium transition
                            {{ $selectedCharId == $char->CharID
                                ? 'bg-indigo-600 text-white'
                                : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                        {{ $char->CharName16 }}
                        <span class="ml-1 text-xs opacity-75">Lv.{{ $char->CurLevel }}</span>
                    </button>
                @endforeach
            </div>
        </div>
    @else
        <div
            class="mb-6 p-4 bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-700 text-yellow-800 dark:text-yellow-300 rounded-lg text-sm">
            {{ __('webmall.ui.no_characters') }}
        </div>
    @endif

    {{-- Categories & Items --}}
    @if ($categories->isEmpty())
        <div class="text-center py-16 text-gray-500 dark:text-gray-400">
            <svg class="mx-auto h-12 w-12 mb-4 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <p class="font-medium">{{ __('webmall.ui.empty_heading') }}</p>
            <p class="text-sm mt-1">{{ __('webmall.ui.empty_description') }}</p>
        </div>
    @else
        {{-- Tab navigation --}}
        <div class="mb-6 border-b border-gray-200 dark:border-gray-700 overflow-x-auto">
            <nav class="flex gap-1 min-w-max" aria-label="Webmall categories">
                @foreach ($categories as $category)
                    @if ($category->activeItems->isNotEmpty())
                        <button wire:click="$set('activeTab', '{{ $category->slug }}')"
                            class="cursor-pointer px-4 py-2.5 text-sm font-medium whitespace-nowrap border-b-2 transition-colors
                                {{ $activeTab === $category->slug
                                    ? 'border-indigo-600 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400'
                                    : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:border-gray-300 dark:hover:border-gray-500' }}">
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
                            class="relative bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 flex flex-col gap-2 {{ $soldOut ? 'opacity-60' : '' }}">

                            {{-- HOT badge --}}
                            @if ($item->is_hot)
                                <span
                                    class="absolute top-2 right-2 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-bold bg-red-500 text-white">
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
                                    <img src="{{ asset('images/silkroad/' . $iconPath) }}"
                                        alt="{{ $item->item_name_snapshot ?? 'Item' }}"
                                        class="size-12 object-contain rounded border border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-900 p-0.5"
                                        loading="lazy">
                                </div>
                            </div>

                            {{-- Item name --}}
                            <p class="text-sm font-medium text-gray-900 dark:text-white leading-tight pr-8">
                                {{ $item->item_name_snapshot ?? 'Item #' . $item->ref_item_id }}
                            </p>

                            {{-- Price --}}
                            <p
                                class="text-sm font-semibold
                                @if ($item->isPriceGold()) text-amber-600 dark:text-amber-400
                                @else text-indigo-600 dark:text-indigo-400 @endif">
                                {{ number_format($item->price_value) }}
                                <span class="text-xs font-normal text-gray-500 dark:text-gray-400">
                                    {{ $item->priceTypeLabel() }}
                                </span>
                            </p>

                            {{-- Stock --}}
                            @if ($remaining !== null)
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    @if ($soldOut)
                                        <span class="text-red-500 font-medium">{{ __('webmall.ui.sold_out') }}</span>
                                    @else
                                        {{ __('webmall.ui.remaining_stock', ['count' => $remaining]) }}
                                    @endif
                                </p>
                            @endif

                            {{-- SOX type --}}
                            @if ($refObj && isset($refObj->SoxType) && $refObj->SoxType !== 'Normal')
                                <span class="text-xs font-semibold text-yellow-400 dark:text-yellow-300">
                                    {{ $refObj->SoxType }}
                                </span>
                            @endif

                            {{-- Req. Level & Tradeable --}}
                            @if ($refObj)
                                <div class="flex flex-wrap gap-x-3 gap-y-0.5 text-xs text-gray-500 dark:text-gray-400">
                                    @if ($refObj->ReqLevel1)
                                        <span>Lv. {{ $refObj->ReqLevel1 }}+</span>
                                    @endif
                                    @if (!$refObj->CanTrade)
                                        <span
                                            class="text-red-500 dark:text-red-400">{{ __('webmall.ui.non_trade') }}</span>
                                    @endif
                                    @if ((int) $refObj->TypeID2 === 1 && !in_array((int) $refObj->TypeID3, [4, 6], true) && !is_null($refObj->ReqGender))
                                        @if ((int) $refObj->ReqGender === 0)
                                            <span
                                                class="text-pink-500 dark:text-pink-400">{{ __('webmall.ui.female') }}</span>
                                        @else
                                            <span
                                                class="text-blue-500 dark:text-blue-400">{{ __('webmall.ui.male') }}</span>
                                        @endif
                                    @endif
                                </div>
                            @endif

                            {{-- Date range --}}
                            @if ($item->available_until)
                                <p class="text-xs text-gray-400 dark:text-gray-500">
                                    {{ __('webmall.ui.until', ['date' => $item->available_until->format('d.m.Y H:i')]) }}
                                </p>
                            @endif

                            {{-- Buy button --}}
                            @if (!$soldOut && $selectedCharId)
                                @if ($requireLogout && $selectedCharOnline)
                                    <span
                                        class="mt-auto w-full text-center text-xs py-1.5 px-3 rounded bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300 border border-amber-300 dark:border-amber-700">
                                        {{ __('webmall.error.character_must_be_offline_short') }}
                                    </span>
                                @else
                                    <button wire:click="confirmPurchase({{ $item->id }})"
                                        wire:loading.attr="disabled"
                                        class="cursor-pointer mt-auto w-full text-sm py-1.5 px-3 rounded bg-indigo-600 hover:bg-indigo-700 text-white font-medium transition disabled:opacity-50">
                                        {{ __('webmall.ui.buy') }}
                                    </button>
                                @endif
                            @elseif ($soldOut)
                                <span
                                    class="mt-auto w-full text-center text-sm py-1.5 px-3 rounded bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
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
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm" x-data
            x-on:keydown.escape.window="$wire.cancelConfirm()">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md mx-4 p-6 space-y-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('webmall.ui.modal_title') }}</h3>

                <div class="flex items-center gap-3">
                    @php
                        $confirmRefObj = $refObjs[$confirmItem->ref_item_id] ?? null;
                        $confirmIcon = \App\Helpers\WebmallItemIconHelper::resolveIcon(
                            $confirmRefObj?->AssocFileIcon128,
                        );
                        $confirmIsSeal = $confirmRefObj && \App\Helpers\WebmallItemIconHelper::isSeal($confirmRefObj);
                    @endphp
                    <div class="relative inline-flex flex-shrink-0">
                        @if ($confirmIsSeal)
                            <img class="pointer-events-none absolute inset-0 size-12"
                                src="{{ asset('images/silkroad/item/seal.gif') }}" />
                        @endif
                        <img src="{{ asset('images/silkroad/' . $confirmIcon) }}"
                            alt="{{ $confirmItem->item_name_snapshot ?? 'Item' }}"
                            class="size-12 object-contain rounded border border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-900 p-0.5">
                    </div>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ $confirmItem->item_name_snapshot ?? 'Item #' . $confirmItem->ref_item_id }}
                    </span>
                </div>

                <dl class="text-sm space-y-2">
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('webmall.ui.modal_item') }}</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">
                            {{ $confirmItem->item_name_snapshot ?? 'Item #' . $confirmItem->ref_item_id }}
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('webmall.ui.modal_price') }}</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">
                            {{ number_format($confirmItem->price_value) }}
                            {{ $confirmItem->priceTypeLabel() }}
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('webmall.ui.modal_character') }}</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">{{ $selectedCharName }}</dd>
                    </div>
                </dl>

                {{-- Balance breakdown --}}
                @if ($confirmBalance !== null)
                    <div
                        class="rounded-lg bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 p-3 text-sm space-y-1">
                        <div class="flex justify-between text-gray-600 dark:text-gray-300">
                            <span>{{ __('webmall.ui.balance_current', ['type' => $confirmItem->priceTypeLabel() . ($confirmItem->price_type === 'gold' && $selectedCharName ? ' (' . $selectedCharName . ')' : '')]) }}</span>
                            <span>{{ number_format($confirmBalance) }}</span>
                        </div>
                        <div class="flex justify-between text-red-500 dark:text-red-400">
                            <span>{{ __('webmall.ui.balance_price') }}</span>
                            <span>{{ number_format($confirmItem->price_value) }}</span>
                        </div>
                        <div
                            class="border-t border-gray-200 dark:border-gray-600 pt-1 flex justify-between font-semibold
                            {{ $confirmBalance - $confirmItem->price_value >= 0 ? 'text-gray-900 dark:text-white' : 'text-red-600 dark:text-red-400' }}">
                            <span>{{ __('webmall.ui.balance_remaining') }}</span>
                            <span>{{ number_format($confirmBalance - $confirmItem->price_value) }}</span>
                        </div>
                    </div>
                @endif

                <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ __('webmall.ui.modal_disclaimer') }}
                </p>

                @php
                    $insufficientBalance = $confirmBalance !== null && $confirmBalance - $confirmItem->price_value < 0;
                    $blockedByOnline = $requireLogout && $selectedCharOnline;
                    $confirmDisabled = $insufficientBalance || $blockedByOnline;
                @endphp

                @if ($blockedByOnline)
                    <div
                        class="rounded-lg bg-amber-50 dark:bg-amber-900/30 border border-amber-300 dark:border-amber-700 p-3 text-sm text-amber-700 dark:text-amber-300">
                        {{ __('webmall.error.character_must_be_offline') }}
                    </div>
                @endif

                <div class="flex gap-3 justify-end pt-2">
                    <button wire:click="cancelConfirm"
                        class="cursor-pointer px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        {{ __('webmall.ui.modal_cancel') }}
                    </button>
                    <button wire:click="executePurchase" wire:loading.attr="disabled" @disabled($confirmDisabled)
                        class="px-4 py-2 text-sm rounded-lg font-medium transition disabled:opacity-50 disabled:cursor-not-allowed
                            {{ $confirmDisabled ? 'bg-gray-400 dark:bg-gray-600 text-white cursor-not-allowed' : 'cursor-pointer bg-indigo-600 hover:bg-indigo-700 text-white' }}">
                        <span wire:loading.remove
                            wire:target="executePurchase">{{ __('webmall.ui.modal_confirm') }}</span>
                        <span wire:loading wire:target="executePurchase">{{ __('webmall.ui.modal_processing') }}</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
