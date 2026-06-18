{{--
    $drops: Collection with ItemCode, ItemNameRaw, ItemNameENG, [$ratioField], DropAmountMin?, DropAmountMax?
    $ratioField: field name for the ratio value
    $ratioIsAbsolute: true = the ratio IS the drop probability (0–1), false = relative SelectRatio weight
--}}
<div class="overflow-x-auto">
    <table class="w-full text-sm text-left">
        <thead>
            <tr class="border-b border-gray-200 dark:border-gray-700">
                <th class="px-4 py-2 font-semibold text-gray-700 dark:text-gray-300">{{ __('filament/monster-drops.col_item') }}</th>
                <th class="px-4 py-2 font-semibold text-gray-700 dark:text-gray-300">{{ __('filament/monster-drops.col_item_code') }}</th>
                <th class="px-4 py-2 font-semibold text-gray-700 dark:text-gray-300 text-right">
                    {{ $ratioIsAbsolute ? __('filament/monster-drops.col_drop_rate') : __('filament/monster-drops.col_weight') }}
                </th>
                @if($ratioIsAbsolute && isset($drops->first()->DropAmountMin))
                    <th class="px-4 py-2 font-semibold text-gray-700 dark:text-gray-300 text-right">{{ __('filament/monster-drops.col_amount') }}</th>
                @endif
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
            @foreach($drops as $drop)
                @php
                    $itemName = ($drop->ItemNameENG && $drop->ItemNameENG !== '0')
                        ? $drop->ItemNameENG
                        : $drop->ItemCode;
                    $ratio = $drop->{$ratioField} ?? 0;
                    $pct = round($ratio * 100, 4);
                    $isDisabled = ($showDisabled ?? false) && isset($drop->CanDrop) && $drop->CanDrop === 0;
                @endphp
                <tr @class(['hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors', 'opacity-40' => $isDisabled])>
                    <td class="px-4 py-2 font-medium text-gray-900 dark:text-gray-100">
                        {{ $itemName ?: '—' }}
                        @if($isDisabled)
                            <span class="ml-1 text-xs text-red-400 dark:text-red-500">disabled</span>
                        @endif
                    </td>
                    <td class="px-4 py-2 font-mono text-xs text-gray-400">
                        {{ $drop->ItemCode }}
                    </td>
                    <td class="px-4 py-2 text-right">
                        @if($ratioIsAbsolute)
                            <x-filament::badge
                                color="{{ $pct >= 50 ? 'success' : ($pct >= 10 ? 'warning' : ($pct >= 1 ? 'info' : 'gray')) }}"
                            >
                                {{ $pct }}%
                            </x-filament::badge>
                        @else
                            <span class="text-gray-600 dark:text-gray-300 tabular-nums">{{ $ratio }}</span>
                        @endif
                    </td>
                    @if($ratioIsAbsolute && isset($drop->DropAmountMin))
                        <td class="px-4 py-2 text-right text-gray-600 dark:text-gray-300 tabular-nums">
                            @if($drop->DropAmountMin === $drop->DropAmountMax)
                                {{ $drop->DropAmountMin }}
                            @else
                                {{ $drop->DropAmountMin }}–{{ $drop->DropAmountMax }}
                            @endif
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
