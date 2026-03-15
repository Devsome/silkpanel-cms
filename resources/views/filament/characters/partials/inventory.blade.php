@php
    $allItems = collect($inventory)->values();
    $pages = $allItems->chunk(32);
@endphp

<div x-data="{ page: 0, maxPages: 3, selectedPage: 0, selectedIndex: null }" class="grid gap-3">
    <div class="flex flex-wrap items-center gap-2">
        <button type="button" @click="page = Math.max(0, page - 1)" :disabled="page === 0"
            class="rounded-md border border-gray-300 bg-white px-2.5 py-1.5 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-200 disabled:cursor-not-allowed disabled:opacity-50">
            {{ __('filament/characters.inventory.back') }}
        </button>

        <template x-for="p in maxPages" :key="p">
            <button type="button" @click="page = p - 1" class="rounded-md border px-2.5 py-1.5"
                :class="page === (p - 1) ?
                    'border-gray-900 bg-gray-900 text-white dark:border-gray-200 dark:bg-gray-200 dark:text-gray-900' :
                    'border-gray-300 bg-white text-gray-900 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200'">
                <span x-text="p"></span>
            </button>
        </template>

        <button type="button" @click="page = Math.min(maxPages - 1, page + 1)" :disabled="page === (maxPages - 1)"
            class="rounded-md border border-gray-300 bg-white px-2.5 py-1.5 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-200 disabled:cursor-not-allowed disabled:opacity-50">
            {{ __('filament/characters.inventory.next') }}
        </button>
    </div>

    @for ($page = 0; $page < 3; $page++)
        @php
            $pageItems = ($pages->get($page) ?? collect())->values();
        @endphp

        <section x-show="page === {{ $page }}" x-cloak class="p-3">
            <div class="flex items-start gap-3.5">
                <div class="grid grid-cols-4 gap-1">
                    @for ($index = 0; $index < 32; $index++)
                        @php
                            $item = $pageItems->get($index);
                            $info = $item ? $item->get('info') : null;
                        @endphp

                        <article class="relative overflow-visible">
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
                                                class="mt-0.5 text-[10px] font-semibold leading-none text-gray-700 dark:text-gray-200">
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
                            $selectedItem = $pageItems->get($index);
                            $selectedInfo = $selectedItem ? $selectedItem->get('info') : null;
                        @endphp
                        @if ($selectedItem)
                            <div x-show="selectedIndex === {{ $index }}" x-cloak>
                                <x-characters.inventory-tooltip :item="$selectedInfo" :inline="true" />
                            </div>
                        @endif
                    @endfor
                </section>
            </div>
        </section>
    @endfor
</div>
