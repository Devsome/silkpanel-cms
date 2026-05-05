@php
    $slotStart = 13;
    $slotsPerPage = 32;
    $maxPages = 3;

    $inventoryBySlot = collect($inventory)->filter()->keyBy(fn($item) => (int) data_get($item, 'info.Slot', -1));
@endphp

<div x-data="{ page: 0, maxPages: {{ $maxPages }}, selectedPage: 0, selectedIndex: null, selectedSlot: null, confirmingDelete: false }" class="grid gap-3">
    @for ($page = 0; $page < $maxPages; $page++)

        <section x-show="page === {{ $page }}" x-cloak>
            <div class="flex items-start gap-3.5">
                <div class="grid grid-cols-4 gap-1">
                    @for ($index = 0; $index < 32; $index++)
                        @php
                            $slot = $slotStart + $page * $slotsPerPage + $index;
                            $item = $inventoryBySlot->get($slot);
                            $info = $item ? $item->get('info') : null;
                        @endphp

                        <article class="relative overflow-visible" title="{{ $slot !== null ? 'Slot ' . $slot : '' }}">
                            @if ($item)
                                <div class="flex items-center">
                                    <div class="relative inline-flex cursor-pointer flex-col items-center"
                                        @click="selectedPage = {{ $page }}; selectedIndex = {{ $index }}; selectedSlot = {{ $slot }}; confirmingDelete = false"
                                        :class="selectedPage === {{ $page }} && selectedIndex === {{ $index }} ?
                                            'rounded ring-2 ring-primary-500 ring-offset-1 ring-offset-white' : ''">

                                        @if ($info?->get('SoxType') != 'Normal' && !in_array((int) $info?->get('TypeID2'), [4], true))
                                            <img class="pointer-events-none absolute inset-0 size-8"
                                                src="{{ asset('images/silkroad/item/seal.gif') }}" />
                                        @endif

                                        <img src="{{ asset('images/silkroad/' . $item->get('icon')) }}"
                                            alt="{{ $info?->get('ItemName') ?? 'Item' }}"
                                            class="size-8 rounded border border-gray-300 bg-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 object-contain">

                                        @if ((int) $item->get('amount') > 0)
                                            <span
                                                class="pointer-events-none absolute top-0 left-0 text-[10px] font-bold leading-none text-white drop-shadow-[0_0_1px_rgba(0,0,0,0.9)]">
                                                {{ (int) $item->get('amount') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div
                                    class="size-8 rounded border border-gray-300 bg-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 text-[12px] text-gray-400">
                                </div>
                            @endif
                        </article>
                    @endfor
                </div>

                <section x-show="selectedPage === {{ $page }} && selectedIndex !== null" x-cloak
                    class="min-w-90 max-w-130">

                    @for ($index = 0; $index < 32; $index++)
                        @php
                            $slot = $slotStart + $page * $slotsPerPage + $index;
                            $selectedItem = $inventoryBySlot->get($slot);
                            $selectedInfo = $selectedItem ? $selectedItem->get('info') : null;
                        @endphp
                        @if ($selectedItem)
                            <div x-show="selectedIndex === {{ $index }}" x-cloak>
                                <x-characters.inventory-tooltip :item="$selectedInfo" :inline="true" />

                                <div class="mt-3 border-t border-gray-200 pt-3 dark:border-gray-700">
                                    <template x-if="!confirmingDelete">
                                        <button type="button" @click="confirmingDelete = true"
                                            class="inline-flex items-center gap-1.5 rounded-md border border-danger-300 bg-white px-3 py-1.5 text-sm font-medium text-danger-600 shadow-sm hover:bg-danger-50 dark:border-danger-600 dark:bg-gray-800 dark:text-danger-400 dark:hover:bg-danger-900/20">
                                            <x-heroicon-o-trash class="size-4" />
                                            {{ __('filament/characters.view.delete_item') }}
                                        </button>
                                    </template>
                                    <template x-if="confirmingDelete">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-medium text-danger-600 dark:text-danger-400">
                                                {{ __('filament/characters.view.delete_item_confirm') }}
                                            </span>
                                            <button type="button"
                                                @click="$wire.dispatch('deleteInventoryItem', { slot: selectedSlot }); confirmingDelete = false; selectedIndex = null; selectedSlot = null"
                                                class="inline-flex items-center rounded-md bg-danger-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-danger-700">
                                                {{ __('filament/characters.view.delete_item_confirm_yes') }}
                                            </button>
                                            <button type="button" @click="confirmingDelete = false"
                                                class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300">
                                                {{ __('filament/characters.view.delete_item_confirm_cancel') }}
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        @endif
                    @endfor
                </section>
            </div>
        </section>
    @endfor

    <div class="flex flex-wrap items-center gap-2">
        <template x-for="p in maxPages" :key="p">
            <button type="button" @click="page = p - 1" class="rounded-md border px-2.5 py-1.5"
                :class="page === (p - 1) ?
                    'border-gray-900 bg-gray-900 text-white dark:border-gray-200 dark:bg-gray-200 dark:text-gray-900' :
                    'border-gray-300 bg-white text-gray-900 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200'">
                <span x-text="p"></span>
            </button>
        </template>
    </div>
</div>
