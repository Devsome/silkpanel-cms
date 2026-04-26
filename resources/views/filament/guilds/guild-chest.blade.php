@php
    $slotStart = 0;
    $slotsPerPage = 30;
    $maxPages = 4;
    $guildChestItemBySlot = collect($items)->filter()->keyBy(fn($item) => (int) data_get($item, 'info.Slot', -1));
@endphp

<div x-data="{ page: 0, maxPages: {{ $maxPages }}, selectedPage: 0, selectedIndex: null }" class="grid gap-3">
    @for ($page = 0; $page < $maxPages; $page++)
        <section x-show="page === {{ $page }}" x-cloak class="p-3">
            <div class="flex items-start gap-3.5 justify-center">
                <div class="grid grid-cols-6 gap-0.5">
                    @for ($index = 0; $index < 30; $index++)
                        @php
                            $slot = $slotStart + $page * $slotsPerPage + $index;
                            $item = $guildChestItemBySlot->get($slot);
                            $info = $item ? $item->get('info') : null;
                        @endphp
                        <article class="relative overflow-visible" x-data="{ show: false, flipLeft: false }"
                            @mouseenter="
                                const rect = $el.getBoundingClientRect();
                                flipLeft = (rect.right + 370) > window.innerWidth;
                                show = true;
                            "
                            @mouseleave="show = false" title="{{ $slot !== null ? 'Slot ' . $slot : '' }}">
                            @if ($item)
                                <div class="flex items-center">
                                    <div class="relative inline-flex cursor-pointer flex-col items-center"
                                        @click="selectedPage = {{ $page }}; selectedIndex = {{ $index }}"
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

                            @if ($info)
                                <div x-show="show" x-cloak x-transition.opacity.duration.150ms
                                    :class="flipLeft ? 'absolute z-50 right-full top-0 mr-2' :
                                        'absolute z-50 left-full top-0 ml-2'">
                                    <x-characters.inventory-tooltip :item="$info" :inline="true" />
                                </div>
                            @endif
                        </article>
                    @endfor
                </div>
            </div>
        </section>
    @endfor

    <div class="flex flex-wrap items-center gap-2 justify-center">
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
