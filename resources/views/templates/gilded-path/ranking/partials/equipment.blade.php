@php
    $equipmentList = collect($equipment ?? []);
    $equipmentBySlot = $equipmentList->keyBy(fn($item) => (int) $item->get('slot'));

    $leftSlots = [6, 0, 1, 4, 9, 11];
    $rightSlots = [7, 2, 3, 5, 10, 12];
@endphp

<div>
    <section class="gp-card p-4 md:p-5">
        <div class="flex flex-col items-center gap-4 md:flex-row md:items-start md:justify-center md:gap-6">
            <div class="grid grid-cols-6 gap-2 md:grid-cols-1">
                @foreach ($leftSlots as $slot)
                    @php
                        $item = $equipmentBySlot->get($slot);
                        $info = $item?->get('info');
                    @endphp

                    <div class="relative" x-data="{ show: false }" @mouseenter="show = true" @mouseleave="show = false"
                        @click.outside="show = false">
                        <button type="button" @click="show = !show"
                            class="relative inline-flex size-11 items-center justify-center border p-0.5 transition gp-card-lowest hover:border-yellow-500"
                            style="border-color: rgba(153, 144, 124, 0.55);" title="Slot {{ $slot }}">
                            <span
                                class="absolute left-0.5 top-0.5 text-[9px] font-semibold leading-none gp-text-outline">
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
                                        class="size-9 object-contain border"
                                        style="border-color: rgba(242, 202, 80, 0.35);">
                                </span>
                            @else
                                <span class="size-9 border border-dashed gp-card-low"
                                    style="border-color: rgba(153, 144, 124, 0.35);"></span>
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
                        $item = $equipmentBySlot->get($slot);
                        $info = $item?->get('info');
                    @endphp

                    <div class="relative" x-data="{ show: false }" @mouseenter="show = true" @mouseleave="show = false"
                        @click.outside="show = false">
                        <button type="button" @click="show = !show"
                            class="relative inline-flex size-11 items-center justify-center border p-0.5 transition gp-card-lowest hover:border-yellow-500"
                            style="border-color: rgba(153, 144, 124, 0.55);" title="Slot {{ $slot }}">
                            <span
                                class="absolute left-0.5 top-0.5 text-[9px] font-semibold leading-none gp-text-outline">
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
                                        class="size-9 object-contain border"
                                        style="border-color: rgba(242, 202, 80, 0.35);">
                                </span>
                            @else
                                <span class="size-9 border border-dashed gp-card-low"
                                    style="border-color: rgba(153, 144, 124, 0.35);"></span>
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
