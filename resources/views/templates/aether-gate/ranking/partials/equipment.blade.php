@php
    $equipmentList = collect($equipment ?? []);
    $equipmentBySlot = $equipmentList->keyBy(fn($item) => (int) $item->get('slot'));

    $slotNames = [
        0  => 'Helm',   1  => 'Armor',  2  => 'Shoulder', 3  => 'Pants',
        4  => 'Boots',  5  => 'Weapon', 6  => 'Shield',   7  => 'Ring',
        8  => 'Ring',   9  => 'Earring',10 => 'Earring',  11 => 'Necklace',
        12 => 'Ring',   13 => 'Special',
    ];

    $leftSlots  = [6, 0, 1, 4, 9, 11];
    $rightSlots = [7, 2, 3, 5, 10, 12];
@endphp

{{-- Slot macro: side = 'right' | 'left' --}}
@php
    $renderSlot = function(int $slot, string $side) use ($equipmentBySlot, $slotNames): array {
        $item  = $equipmentBySlot->get($slot);
        $info  = $item?->get('info');
        $isSox = $info && $info->get('SoxType') !== 'Normal' && !in_array((int) $info->get('TypeID2'), [4], true);
        return compact('item','info','isSox');
    };
@endphp

<div class="ag-card">
    {{-- Section header --}}
    <div class="px-6 py-4 border-b ag-divider flex items-center gap-2">
        <div class="w-1 h-4" style="background: var(--ag-secondary);"></div>
        <p class="ag-section-eyebrow" style="color: var(--ag-secondary);">{{ __('ranking.equipment') }}</p>
        <span class="ml-auto text-xs ag-text-muted">{{ $equipmentList->count() }} / {{ count($leftSlots) + count($rightSlots) }} {{ __('ranking.slots_equipped') }}</span>
    </div>

    <div class="p-6">
        <div class="flex flex-col items-center gap-6 md:flex-row md:items-start md:justify-center md:gap-8">

            {{-- Left slots --}}
            <div class="flex flex-row gap-2 md:flex-col md:gap-3">
                @foreach ($leftSlots as $slot)
                    @php ['item' => $item, 'info' => $info, 'isSox' => $isSox] = $renderSlot($slot, 'right'); @endphp
                    <div class="flex flex-col items-center gap-1">
                        <div x-data="{ show: false, tx: 0, ty: 0 }"
                             @mouseenter="show = true; let r = $el.getBoundingClientRect(); tx = r.right + 10; ty = r.top"
                             @mouseleave="show = false">

                            <button type="button"
                                class="ag-slot-btn {{ $item ? 'has-item' : '' }} {{ $isSox ? 'ag-item-sox' : '' }}">
                                @if ($item)
                                    <span class="relative inline-flex size-10 items-center justify-center">
                                        @if ($isSox)
                                            <img class="pointer-events-none absolute inset-0 size-10 opacity-70"
                                                src="{{ asset('images/silkroad/item/seal.gif') }}" alt="Seal" />
                                        @endif
                                        <img src="{{ asset('images/silkroad/' . $item->get('icon')) }}"
                                            alt="{{ $info?->get('ItemName') ?? 'Item' }}"
                                            class="size-10 object-contain">
                                    </span>
                                @else
                                    <span class="ag-slot-empty"></span>
                                @endif
                            </button>

                            @if ($info)
                                <template x-teleport="body">
                                    <div x-show="show" x-cloak
                                         x-transition:enter="transition ease-out duration-100"
                                         x-transition:enter-start="opacity-0 scale-95"
                                         x-transition:enter-end="opacity-100 scale-100"
                                         :style="`position:fixed;left:${tx}px;top:${ty}px;z-index:9999;pointer-events:none;`">
                                        <x-characters.inventory-tooltip :item="$info" :inline="true" />
                                    </div>
                                </template>
                            @endif
                        </div>
                        <span class="text-[9px] ag-text-muted uppercase tracking-wider leading-none">
                            {{ $slotNames[$slot] ?? 'Slot '.$slot }}
                        </span>
                    </div>
                @endforeach
            </div>

            {{-- Character portrait --}}
            <div class="relative flex-shrink-0">
                <div class="relative flex items-center justify-center"
                    style="width: 180px; height: 260px; background: linear-gradient(135deg, rgba(6,8,15,0.9), rgba(6,24,56,0.7)); border: 1px solid rgba(34,211,238,0.2);">
                    <div style="position:absolute;top:6px;left:6px;width:12px;height:12px;border-top:1.5px solid rgba(34,211,238,0.4);border-left:1.5px solid rgba(34,211,238,0.4);"></div>
                    <div style="position:absolute;top:6px;right:6px;width:12px;height:12px;border-top:1.5px solid rgba(34,211,238,0.4);border-right:1.5px solid rgba(34,211,238,0.4);"></div>
                    <div style="position:absolute;bottom:6px;left:6px;width:12px;height:12px;border-bottom:1.5px solid rgba(34,211,238,0.4);border-left:1.5px solid rgba(34,211,238,0.4);"></div>
                    <div style="position:absolute;bottom:6px;right:6px;width:12px;height:12px;border-bottom:1.5px solid rgba(34,211,238,0.4);border-right:1.5px solid rgba(34,211,238,0.4);"></div>
                    <div class="absolute bottom-0 left-0 right-0 h-20" style="background: radial-gradient(ellipse at 50% 100%, rgba(34,211,238,0.1) 0%, transparent 70%);"></div>
                    <img src="{{ $characterImage2d ?? asset('images/silkroad/icon_default.png') }}"
                        alt="Character"
                        class="h-56 w-auto max-w-[160px] object-contain object-bottom relative z-10"
                        style="filter: drop-shadow(0 0 12px rgba(34,211,238,0.15));">
                </div>
            </div>

            {{-- Right slots --}}
            <div class="flex flex-row gap-2 md:flex-col md:gap-3">
                @foreach ($rightSlots as $slot)
                    @php ['item' => $item, 'info' => $info, 'isSox' => $isSox] = $renderSlot($slot, 'left'); @endphp
                    <div class="flex flex-col items-center gap-1">
                        <div x-data="{ show: false, tx: 0, ty: 0 }"
                             @mouseenter="show = true; let r = $el.getBoundingClientRect(); tx = r.left - 10; ty = r.top"
                             @mouseleave="show = false">

                            <button type="button"
                                class="ag-slot-btn {{ $item ? 'has-item' : '' }} {{ $isSox ? 'ag-item-sox' : '' }}">
                                @if ($item)
                                    <span class="relative inline-flex size-10 items-center justify-center">
                                        @if ($isSox)
                                            <img class="pointer-events-none absolute inset-0 size-10 opacity-70"
                                                src="{{ asset('images/silkroad/item/seal.gif') }}" alt="Seal" />
                                        @endif
                                        <img src="{{ asset('images/silkroad/' . $item->get('icon')) }}"
                                            alt="{{ $info?->get('ItemName') ?? 'Item' }}"
                                            class="size-10 object-contain">
                                    </span>
                                @else
                                    <span class="ag-slot-empty"></span>
                                @endif
                            </button>

                            @if ($info)
                                <template x-teleport="body">
                                    <div x-show="show" x-cloak
                                         x-transition:enter="transition ease-out duration-100"
                                         x-transition:enter-start="opacity-0 scale-95"
                                         x-transition:enter-end="opacity-100 scale-100"
                                         :style="`position:fixed;right:${window.innerWidth - tx}px;top:${ty}px;z-index:9999;pointer-events:none;`">
                                        <x-characters.inventory-tooltip :item="$info" :inline="true" />
                                    </div>
                                </template>
                            @endif
                        </div>
                        <span class="text-[9px] ag-text-muted uppercase tracking-wider leading-none">
                            {{ $slotNames[$slot] ?? 'Slot '.$slot }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
