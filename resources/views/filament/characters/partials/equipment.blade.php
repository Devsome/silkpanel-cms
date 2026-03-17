@php
    $equipmentList = collect($equipment ?? []);
    $equipmentBySlot = $equipmentList->keyBy(fn($item) => (int) $item->get('slot'));

    $leftSlots = [6, 0, 1, 4, 9, 11];
    $rightSlots = [7, 2, 3, 5, 10, 12];
    $allSlots = array_merge($leftSlots, $rightSlots);
@endphp

<div x-data="{ selectedSlot: null }" class="grid gap-4">
    <section class="rounded-xl border border-gray-200 bg-white p-3 dark:border-gray-700 dark:bg-gray-900">
        <div class="flex flex-col items-center gap-4 md:flex-row md:items-start md:justify-center md:gap-6">
            <div class="grid grid-cols-6 gap-2 md:grid-cols-1">
                @foreach ($leftSlots as $slot)
                    @php
                        $item = $equipmentBySlot->get($slot);
                        $info = $item?->get('info');
                    @endphp

                    <button type="button" @click="selectedSlot = {{ $slot }}"
                        class="relative inline-flex size-11 items-center justify-center rounded-sm border bg-gray-100 p-0.5 transition dark:bg-gray-800"
                        :class="selectedSlot === {{ $slot }} ?
                            'border-primary-500 ring-2 ring-primary-500/40' :
                            'border-gray-400 hover:border-gray-500 dark:border-gray-600 dark:hover:border-gray-500'"
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
                        $item = $equipmentBySlot->get($slot);
                        $info = $item?->get('info');
                    @endphp

                    <button type="button" @click="selectedSlot = {{ $slot }}"
                        class="relative inline-flex size-11 items-center justify-center rounded-sm border bg-gray-100 p-0.5 transition dark:bg-gray-800"
                        :class="selectedSlot === {{ $slot }} ?
                            'border-primary-500 ring-2 ring-primary-500/40' :
                            'border-gray-400 hover:border-gray-500 dark:border-gray-600 dark:hover:border-gray-500'"
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
                @endforeach
            </div>
        </div>
    </section>

    <section x-show="selectedSlot !== null" x-cloak class="min-w-90 max-w-130">
        @foreach ($allSlots as $slot)
            @php
                $selected = $equipmentBySlot->get($slot);
                $selectedInfo = $selected?->get('info');
            @endphp

            @if ($selectedInfo)
                <div x-show="selectedSlot === {{ $slot }}" x-cloak>
                    <x-characters.inventory-tooltip :item="$selectedInfo" :inline="true" />
                </div>
            @endif
        @endforeach
    </section>
</div>
