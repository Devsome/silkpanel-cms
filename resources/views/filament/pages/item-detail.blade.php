<x-filament-panels::page>
    @php $item = $this->getItem(); @endphp

    {{-- Back link --}}
    <div class="mb-2">
        <a href="{{ \App\Filament\Pages\Items::getUrl() }}"
            class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
            <x-heroicon-o-arrow-left class="h-4 w-4" />
            {{ __('filament/items.back_to_items') }}
        </a>
    </div>

    @if (!$item)
        <x-filament::section>
            <div class="py-8 text-center text-gray-400">
                {{ __('filament/items.item_not_found', ['id' => $id]) }}
            </div>
        </x-filament::section>
    @else
        @php
            $name = $item->NameENG && $item->NameENG !== '0' ? $item->NameENG : $item->CodeName128;
            $iconUrl = \App\Filament\Pages\ItemDetail::iconUrl($item->AssocFileIcon128 ?? '');
            $stats = $item->getRefObjItem;

            // Detect item class from stats (more reliable than TypeID3 alone)
            $hasWeaponStats = $stats && (($stats->PAttackMin_L ?? 0) > 0 || ($stats->MAttackMin_L ?? 0) > 0);
            $hasArmorStats = $stats && (($stats->PD_L ?? 0) > 0 || ($stats->MD_L ?? 0) > 0 || ($stats->ER_L ?? 0) > 0);
            $hasAbsorbStats = $stats && (($stats->PAR_L ?? 0) > 0 || ($stats->MAR_L ?? 0) > 0);
        @endphp

        {{-- Header card --}}
        <x-filament::section>
            <div class="flex items-start gap-5">
                {{-- Icon --}}
                <div class="shrink-0 rounded-xl bg-gray-100 dark:bg-gray-700/60 p-3 shadow-inner">
                    <img src="{{ $iconUrl }}" alt="{{ $name }}" class="h-16 w-16 object-contain"
                        onerror="this.src='{{ asset('images/silkroad/icon_default.png') }}'" />
                </div>

                {{-- Info --}}
                <div class="min-w-0 flex-1">
                    @php
                        $soxLabel = \App\Filament\Pages\ItemDetail::soxLabel(
                            $item->CodeName128,
                            (int) ($stats?->ItemClass ?? 0),
                        );
                    @endphp
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 truncate">{{ $name }}
                        </h2>
                        <x-filament::badge
                            color="{{ \App\Filament\Pages\ItemDetail::typeColor((int) $item->TypeID2, (int) $item->TypeID3) }}">
                            {{ \App\Filament\Pages\ItemDetail::typeName((int) $item->TypeID2, (int) $item->TypeID3, (int) $item->TypeID4) }}
                        </x-filament::badge>
                        @if ($soxLabel)
                            <x-filament::badge color="{{ \App\Filament\Pages\ItemDetail::soxColor($soxLabel) }}">
                                {{ $soxLabel }}
                            </x-filament::badge>
                        @elseif($item->Rarity > 0)
                            @php $rl = \App\Filament\Pages\ItemDetail::rarityLabel((int)$item->Rarity); @endphp
                            @if ($rl)
                                <x-filament::badge
                                    color="{{ \App\Filament\Pages\ItemDetail::rarityColor((int) $item->Rarity) }}">
                                    {{ $rl }}
                                </x-filament::badge>
                            @endif
                        @endif
                        @if ($item->CashItem)
                            <x-filament::badge color="success">Cash Item</x-filament::badge>
                        @endif
                        @if ($stats?->TwoHanded)
                            <x-filament::badge color="gray">2-Handed</x-filament::badge>
                        @endif
                    </div>
                    <div class="mt-1 font-mono text-sm text-gray-400 dark:text-gray-500">{{ $item->CodeName128 }}</div>

                    {{-- Quick stats row --}}
                    <div class="mt-3 flex flex-wrap gap-x-5 gap-y-1.5 text-sm">
                        @if ($item->ReqLevel1 > 0)
                            <div class="flex items-center gap-1.5 text-gray-500 dark:text-gray-400">
                                <span class="font-semibold text-gray-700 dark:text-gray-300">Lv
                                    {{ $item->ReqLevel1 }}</span>
                            </div>
                        @endif
                        @if ($stats?->ReqStr > 0)
                            <div class="flex items-center gap-1.5 text-gray-500 dark:text-gray-400">
                                <span>STR <span
                                        class="font-semibold text-gray-700 dark:text-gray-300">{{ $stats->ReqStr }}</span></span>
                            </div>
                        @endif
                        @if ($stats?->ReqInt > 0)
                            <div class="flex items-center gap-1.5 text-gray-500 dark:text-gray-400">
                                <span>INT <span
                                        class="font-semibold text-gray-700 dark:text-gray-300">{{ $stats->ReqInt }}</span></span>
                            </div>
                        @endif
                        <div class="flex items-center gap-1.5 text-gray-500 dark:text-gray-400">
                            <x-heroicon-o-user class="h-4 w-4 text-gray-400" />
                            <span>{{ \App\Filament\Pages\ItemDetail::countryLabel((int) $item->Country) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </x-filament::section>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

            {{-- Combat / Item Stats --}}
            @if ($stats && ($hasWeaponStats || $hasArmorStats || $hasAbsorbStats || $stats->Dur_L || $stats->MaxMagicOptCount))
                <x-filament::section>
                    <x-slot name="heading">{{ __('filament/items.section_stats') }}</x-slot>

                    <dl class="divide-y divide-gray-100 dark:divide-gray-700/60">
                        {{-- Weapon stats --}}
                        @if ($hasWeaponStats)
                            @if (($stats->PAttackMin_L ?? 0) > 0)
                                <div class="flex items-center justify-between py-2">
                                    <dt class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ __('filament/items.field_phy_atk') }}</dt>
                                    <dd class="text-sm font-semibold text-gray-900 dark:text-gray-100 tabular-nums">
                                        {{ $stats->PAttackMin_L }} – {{ $stats->PAttackMax_U }}
                                    </dd>
                                </div>
                            @endif
                            @if (($stats->MAttackMin_L ?? 0) > 0)
                                <div class="flex items-center justify-between py-2">
                                    <dt class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ __('filament/items.field_mag_atk') }}</dt>
                                    <dd class="text-sm font-semibold text-gray-900 dark:text-gray-100 tabular-nums">
                                        {{ $stats->MAttackMin_L }} – {{ $stats->MAttackMax_U }}
                                    </dd>
                                </div>
                            @endif
                            @if (($stats->HR_L ?? 0) > 0)
                                <div class="flex items-center justify-between py-2">
                                    <dt class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ __('filament/items.field_hit_rate') }}</dt>
                                    <dd class="text-sm font-semibold text-gray-900 dark:text-gray-100 tabular-nums">
                                        {{ $stats->HR_L }} – {{ $stats->HR_U }}
                                    </dd>
                                </div>
                            @endif
                            @if (($stats->CHR_L ?? 0) > 0)
                                <div class="flex items-center justify-between py-2">
                                    <dt class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ __('filament/items.field_crit_rate') }}</dt>
                                    <dd class="text-sm font-semibold text-gray-900 dark:text-gray-100 tabular-nums">
                                        {{ $stats->CHR_L }} – {{ $stats->CHR_U }}
                                    </dd>
                                </div>
                            @endif
                        @endif

                        {{-- Armor / Defense stats --}}
                        @if ($hasArmorStats || $hasAbsorbStats)
                            @if (($stats->PD_L ?? 0) > 0)
                                <div class="flex items-center justify-between py-2">
                                    <dt class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ __('filament/items.field_phy_def') }}</dt>
                                    <dd class="text-sm font-semibold text-gray-900 dark:text-gray-100 tabular-nums">
                                        {{ $stats->PD_L }} – {{ $stats->PD_U }}
                                    </dd>
                                </div>
                            @endif
                            @if (($stats->MD_L ?? 0) > 0)
                                <div class="flex items-center justify-between py-2">
                                    <dt class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ __('filament/items.field_mag_def') }}</dt>
                                    <dd class="text-sm font-semibold text-gray-900 dark:text-gray-100 tabular-nums">
                                        {{ $stats->MD_L }} – {{ $stats->MD_U }}
                                    </dd>
                                </div>
                            @endif
                            @if (($stats->ER_L ?? 0) > 0)
                                <div class="flex items-center justify-between py-2">
                                    <dt class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ __('filament/items.field_evade_rate') }}</dt>
                                    <dd class="text-sm font-semibold text-gray-900 dark:text-gray-100 tabular-nums">
                                        {{ $stats->ER_L }} – {{ $stats->ER_U }}
                                    </dd>
                                </div>
                            @endif
                            @if (($stats->PAR_L ?? 0) > 0)
                                <div class="flex items-center justify-between py-2">
                                    <dt class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ __('filament/items.field_phy_absorb') }}</dt>
                                    <dd class="text-sm font-semibold text-gray-900 dark:text-gray-100 tabular-nums">
                                        {{ $stats->PAR_L }} – {{ $stats->PAR_U }}
                                    </dd>
                                </div>
                            @endif
                            @if (($stats->MAR_L ?? 0) > 0)
                                <div class="flex items-center justify-between py-2">
                                    <dt class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ __('filament/items.field_mag_absorb') }}</dt>
                                    <dd class="text-sm font-semibold text-gray-900 dark:text-gray-100 tabular-nums">
                                        {{ $stats->MAR_L }} – {{ $stats->MAR_U }}
                                    </dd>
                                </div>
                            @endif
                        @endif

                        {{-- Durability (all equipment) --}}
                        @if (($stats->Dur_L ?? 0) > 0)
                            <div class="flex items-center justify-between py-2">
                                <dt class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('filament/items.field_durability') }}</dt>
                                <dd class="text-sm font-semibold text-gray-900 dark:text-gray-100 tabular-nums">
                                    {{ $stats->Dur_L }} – {{ $stats->Dur_U }}
                                </dd>
                            </div>
                        @endif

                        {{-- Max sockets --}}
                        @if (($stats->MaxMagicOptCount ?? 0) > 0)
                            <div class="flex items-center justify-between py-2">
                                <dt class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('filament/items.field_max_sockets') }}</dt>
                                <dd class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $stats->MaxMagicOptCount }}
                                </dd>
                            </div>
                        @endif
                    </dl>
                </x-filament::section>
            @endif

            {{-- Overview & Flags --}}
            <div class="space-y-6">
                <x-filament::section>
                    <x-slot name="heading">{{ __('filament/items.section_overview') }}</x-slot>

                    <dl class="divide-y divide-gray-100 dark:divide-gray-700/60">
                        <div class="flex items-center justify-between py-2">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">
                                {{ __('filament/items.field_codename') }}</dt>
                            <dd
                                class="font-mono text-xs text-gray-700 dark:text-gray-300 break-all text-right max-w-[60%]">
                                {{ $item->CodeName128 }}</dd>
                        </div>
                        <div class="flex items-center justify-between py-2">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">{{ __('filament/items.field_type') }}
                            </dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ \App\Filament\Pages\ItemDetail::typeName((int) $item->TypeID2, (int) $item->TypeID3, (int) $item->TypeID4) }}
                            </dd>
                        </div>
                        @if ((int) $item->TypeID2 === 1 && !in_array((int) $item->TypeID3, [4, 6], true) && $stats && in_array((int) $stats->ReqGender, [0, 1], true))
                            <div class="flex items-center justify-between py-2">
                                <dt class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('filament/items.field_gender') }}</dt>
                                <dd class="text-sm font-medium
                                    {{ (int) $stats->ReqGender === 0 ? 'text-pink-500 dark:text-pink-400' : 'text-blue-500 dark:text-blue-400' }}">
                                    {{ \App\Filament\Pages\ItemDetail::genderLabel((int) $stats->ReqGender) }}
                                </dd>
                            </div>
                        @endif
                        <div class="flex items-center justify-between py-2">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">
                                {{ __('filament/items.field_country') }}</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ \App\Filament\Pages\ItemDetail::countryLabel((int) $item->Country) }}
                            </dd>
                        </div>
                        @if ($item->Price > 0)
                            <div class="flex items-center justify-between py-2">
                                <dt class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('filament/items.field_price') }}</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-gray-100 tabular-nums">
                                    {{ number_format($item->Price) }} gold
                                </dd>
                            </div>
                        @endif
                        @if ($item->SellPrice > 0)
                            <div class="flex items-center justify-between py-2">
                                <dt class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('filament/items.field_sell_price') }}</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-gray-100 tabular-nums">
                                    {{ number_format($item->SellPrice) }} gold
                                </dd>
                            </div>
                        @endif
                    </dl>
                </x-filament::section>

                {{-- Trade & Drop flags --}}
                <x-filament::section>
                    <x-slot name="heading">{{ __('filament/items.section_flags') }}</x-slot>

                    <div class="grid grid-cols-2 gap-2">
                        @php
                            $flags = [
                                [__('filament/items.flag_tradeable'), (bool) $item->CanTrade],
                                [__('filament/items.flag_droppable'), (bool) $item->CanDrop],
                                [__('filament/items.flag_sellable'), (bool) $item->CanSell],
                                [__('filament/items.flag_buyable'), (bool) $item->CanBuy],
                                [__('filament/items.flag_cash'), (bool) $item->CashItem],
                            ];
                        @endphp
                        @foreach ($flags as [$label, $value])
                            <div
                                class="flex items-center justify-between rounded-lg border border-gray-100 dark:border-gray-700 px-3 py-2">
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $label }}</span>
                                @if ($value)
                                    <x-filament::badge color="success"
                                        size="sm">{{ __('filament/items.yes') }}</x-filament::badge>
                                @else
                                    <x-filament::badge color="gray"
                                        size="sm">{{ __('filament/items.no') }}</x-filament::badge>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </x-filament::section>
            </div>
        </div>
    @endif
</x-filament-panels::page>
