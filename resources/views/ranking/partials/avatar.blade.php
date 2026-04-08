@php
    $avatarList = collect($avatar ?? []);
    $avatarBySlot = $avatarList->keyBy(fn($item) => (int) $item->get('slot'));

    $leftSlots = [0, 2, 4];
    $rightSlots = [1, 3];
@endphp

<div>
    <section class="rounded-xl border border-gray-200 bg-white p-3 dark:border-gray-700 dark:bg-gray-900">
        <div class="flex flex-col items-center gap-4 md:flex-row md:items-start md:justify-center md:gap-6">
            <div class="grid grid-cols-6 gap-2 md:grid-cols-1">
                @foreach ($leftSlots as $slot)
                    @php
                        $item = $avatarBySlot->get($slot);
                        $info = $item?->get('info');
                    @endphp

                    <div class="relative" x-data="{ show: false }" @mouseenter="show = true" @mouseleave="show = false"
                        @click.outside="show = false">
                        <button type="button" @click="show = !show"
                            class="relative inline-flex size-11 items-center justify-center rounded-sm border bg-gray-100 p-0.5 transition dark:bg-gray-800 border-gray-400 dark:border-gray-600 hover:border-indigo-500 dark:hover:border-indigo-500"
                            title="Slot {{ $slot }}">
                            <span class="absolute left-0.5 top-0.5 text-[9px] font-semibold leading-none text-gray-500">
                                {{ $slot }}
                            </span>

                            @if ($item)
                                <span class="relative inline-flex size-9 items-center justify-center">
                                    @if ($info?->get('SoxType') != 'Normal' && !in_array((int) $info?->get('TypeID2'), [4], true))
                                        <img class="pointer-events-none absolute inset-0 size-9"
                                            src="{{ asset('images/silkroad/item/seal.gif') }}" alt="Seal" />
                                    @endif
                                    <img src="{{ asset('images/silkroad/' . $item->get('icon')) }}"
                                        alt="{{ $info?->get('ItemName') ?? 'Item' }}"
                                        class="size-9 border border-slate-300 object-contain shadow-sm dark:border-slate-600">
                                </span>
                            @else
                                <span
                                    class="size-9 border border-dashed border-gray-300 bg-gray-50 dark:border-gray-600 dark:bg-gray-800"></span>
                            @endif
                        </button>

                        @if ($info)
                            <div x-show="show" x-cloak x-transition.opacity.duration.150ms
                                class="absolute z-50 left-full top-0 ml-2">
                                <x-characters.inventory-tooltip :item="$info" :inline="true" />
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="flex justify-center">
                <div class="p-2">
                    <img src="{{ $characterImage2d ?? asset('images/silkroad/icon_default.png') }}" alt="Character"
                        class="h-64 w-auto max-w-40 object-contain md:h-72 md:max-w-44" />
                </div>
            </div>

            <div class="grid grid-cols-6 gap-2 md:grid-cols-1">
                @foreach ($rightSlots as $slot)
                    @php
                        $item = $avatarBySlot->get($slot);
                        $info = $item?->get('info');
                    @endphp

                    <div class="relative" x-data="{ show: false }" @mouseenter="show = true" @mouseleave="show = false"
                        @click.outside="show = false">
                        <button type="button" @click="show = !show"
                            class="relative inline-flex size-11 items-center justify-center rounded-sm border bg-gray-100 p-0.5 transition dark:bg-gray-800 border-gray-400 dark:border-gray-600 hover:border-indigo-500 dark:hover:border-indigo-500"
                            title="Slot {{ $slot }}">
                            <span class="absolute left-0.5 top-0.5 text-[9px] font-semibold leading-none text-gray-500">
                                {{ $slot }}
                            </span>

                            @if ($item)
                                <span class="relative inline-flex size-9 items-center justify-center">
                                    @if ($info?->get('SoxType') != 'Normal' && !in_array((int) $info?->get('TypeID2'), [4], true))
                                        <img class="pointer-events-none absolute inset-0 size-9"
                                            src="{{ asset('images/silkroad/item/seal.gif') }}" alt="Seal" />
                                    @endif
                                    <img src="{{ asset('images/silkroad/' . $item->get('icon')) }}"
                                        alt="{{ $info?->get('ItemName') ?? 'Item' }}"
                                        class="size-9 border border-slate-300 object-contain shadow-sm dark:border-slate-600">
                                </span>
                            @else
                                <span
                                    class="size-9 border border-dashed border-gray-300 bg-gray-50 dark:border-gray-600 dark:bg-gray-800"></span>
                            @endif
                        </button>

                        @if ($info)
                            <div x-show="show" x-cloak x-transition.opacity.duration.150ms
                                class="absolute z-50 right-full top-0 mr-2">
                                <x-characters.inventory-tooltip :item="$info" :inline="true" />
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</div>
