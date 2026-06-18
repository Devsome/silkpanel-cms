<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Search --}}
        <div class="max-w-lg">
            <x-filament::input.wrapper>
                <x-filament::input
                    type="text"
                    wire:model.live.debounce.350ms="search"
                    placeholder="{{ __('filament/monster-drops.search_placeholder') }}"
                    autofocus
                />
            </x-filament::input.wrapper>
            <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">
                {{ __('filament/monster-drops.search_hint') }}
            </p>
        </div>

        {{-- Empty state: Drop Statistics --}}
        @if(strlen($search) < 2)
            @php $stats = $this->getStats(); @endphp

            {{-- Drop system breakdown --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                {{-- Named Drop Groups --}}
                <x-filament::section>
                    <x-slot name="heading">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-squares-2x2 class="h-4 w-4 text-primary-500" />
                            Named Drop Pools
                        </div>
                    </x-slot>
                    <x-slot name="description">Specific item pools (equipment sets, coins, COS items)</x-slot>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Total pools</span>
                            <span class="font-semibold tabular-nums text-gray-900 dark:text-gray-100">{{ number_format($stats['named_groups']) }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Total items</span>
                            <span class="font-semibold tabular-nums text-gray-900 dark:text-gray-100">{{ number_format($stats['named_group_items']) }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Active items</span>
                            <span class="font-semibold tabular-nums text-success-600 dark:text-success-400">
                                {{ number_format($stats['named_group_items'] - $stats['named_group_disabled']) }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Disabled (CanDrop=0)</span>
                            <span class="font-semibold tabular-nums text-danger-600 dark:text-danger-400">
                                {{ number_format($stats['named_group_disabled']) }}
                            </span>
                        </div>
                        <div class="pt-1">
                            @php
                                $activeRatio = $stats['named_group_items'] > 0
                                    ? ($stats['named_group_items'] - $stats['named_group_disabled']) / $stats['named_group_items']
                                    : 0;
                            @endphp
                            <div class="flex items-center justify-between text-xs text-gray-400 mb-1">
                                <span>Active rate</span>
                                <span>{{ round($activeRatio * 100, 1) }}%</span>
                            </div>
                            <div class="h-1.5 rounded-full bg-gray-200 dark:bg-gray-700 overflow-hidden">
                                <div class="h-full rounded-full bg-success-500" style="width: {{ round($activeRatio * 100, 1) }}%"></div>
                            </div>
                        </div>
                    </div>
                </x-filament::section>

                {{-- Tiered Assign Pools --}}
                <x-filament::section>
                    <x-slot name="heading">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-arrow-trending-up class="h-4 w-4 text-info-500" />
                            Tiered Drop Pools
                        </div>
                    </x-slot>
                    <x-slot name="description">Level-tiered pools (potions, elixirs, scrolls, alchemy)</x-slot>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Tiers (1–{{ $stats['tiered_pools'] }})</span>
                            <span class="font-semibold tabular-nums text-gray-900 dark:text-gray-100">{{ number_format($stats['tiered_pools']) }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Items per tier (avg)</span>
                            <span class="font-semibold tabular-nums text-gray-900 dark:text-gray-100">
                                ~{{ $stats['tiered_pools'] > 0 ? round($stats['tiered_pool_items'] / $stats['tiered_pools']) : 0 }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Total pool entries</span>
                            <span class="font-semibold tabular-nums text-gray-900 dark:text-gray-100">{{ number_format($stats['tiered_pool_items']) }}</span>
                        </div>
                        <div class="pt-1 border-t border-gray-100 dark:border-gray-800">
                            <p class="text-xs text-gray-400 dark:text-gray-500 leading-relaxed">
                                Each tier contains ~{{ $stats['tiered_pools'] > 0 ? round($stats['tiered_pool_items'] / $stats['tiered_pools']) : 0 }} items
                                across all consumable categories.
                                Higher monster levels unlock higher tiers with better item grades.
                            </p>
                        </div>
                    </div>
                </x-filament::section>

            </div>

            {{-- Monster drop assignment stats --}}
            <x-filament::section>
                <x-slot name="heading">Monster-Specific Assignments</x-slot>
                <x-slot name="description">Drops assigned directly to individual monsters</x-slot>

                <div class="grid grid-cols-2 gap-4">
                    <div class="flex items-start gap-3 rounded-lg bg-gray-50 dark:bg-gray-800/50 px-4 py-3">
                        <x-heroicon-o-bolt class="h-5 w-5 text-warning-500 shrink-0 mt-0.5" />
                        <div>
                            <div class="text-xl font-bold tabular-nums text-gray-900 dark:text-gray-100">
                                {{ number_format($stats['monsters_with_direct']) }}
                            </div>
                            <div class="text-xs text-gray-500 mt-0.5">Monsters with direct item drops</div>
                            <div class="text-xs text-gray-400 mt-0.5">Fixed items with absolute drop %</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 rounded-lg bg-gray-50 dark:bg-gray-800/50 px-4 py-3">
                        <x-heroicon-o-rectangle-stack class="h-5 w-5 text-info-500 shrink-0 mt-0.5" />
                        <div>
                            <div class="text-xl font-bold tabular-nums text-gray-900 dark:text-gray-100">
                                {{ number_format($stats['monsters_with_groups']) }}
                            </div>
                            <div class="text-xs text-gray-500 mt-0.5">Monsters with assigned group drops</div>
                            <div class="text-xs text-gray-400 mt-0.5">Random item pools with group chance</div>
                        </div>
                    </div>
                </div>
            </x-filament::section>

        @endif

        {{-- Results --}}
        @if(strlen($search) >= 2)
            @php $monsters = $this->getMonsters(); @endphp

            @if($monsters->isEmpty())
                <x-filament::section>
                    <div class="py-8 text-center text-gray-400 dark:text-gray-500">
                        {{ __('filament/monster-drops.no_monsters_found', ['search' => $search]) }}
                    </div>
                </x-filament::section>
            @else
                <x-filament::section>
                    <x-slot name="heading">
                        {{ __('filament/monster-drops.monsters_found', ['count' => $monsters->count()]) }}
                        @if($monsters->count() === 50)
                            <span class="text-xs font-normal text-gray-400">({{ __('filament/monster-drops.max_shown') }})</span>
                        @endif
                    </x-slot>

                    <div class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach($monsters as $monster)
                            @php
                                $name = ($monster->NameENG && $monster->NameENG !== '0')
                                    ? $monster->NameENG
                                    : $monster->ObjName128;
                                $rarityLabel = \App\Filament\Pages\MonsterDrops::rarityLabel((int)$monster->Rarity);
                                $rarityColor = \App\Filament\Pages\MonsterDrops::rarityColor((int)$monster->Rarity);
                            @endphp
                            <a
                                href="{{ \App\Filament\Pages\MonsterDrops::getDetailUrl($monster->CodeName128) }}"
                                class="flex items-center justify-between px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors group"
                            >
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-medium text-gray-900 dark:text-gray-100 group-hover:text-primary-600 dark:group-hover:text-primary-400">
                                            {{ $name }}
                                        </span>
                                        @if($rarityLabel)
                                            <x-filament::badge color="{{ $rarityColor }}" size="sm">{{ $rarityLabel }}</x-filament::badge>
                                        @endif
                                    </div>
                                    @if($monster->ObjName128 && $monster->ObjName128 !== $name)
                                        <span class="text-xs text-gray-400">({{ $monster->ObjName128 }})</span>
                                    @endif
                                    <div class="mt-0.5 font-mono text-xs text-gray-400 dark:text-gray-500">
                                        {{ $monster->CodeName128 }}
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <x-filament::badge color="gray">
                                        {{ __('filament/monster-drops.level') }} {{ $monster->Lvl }}
                                    </x-filament::badge>
                                    <x-heroicon-o-chevron-right class="h-4 w-4 text-gray-400 group-hover:text-primary-500" />
                                </div>
                            </a>
                        @endforeach
                    </div>
                </x-filament::section>
            @endif
        @endif
    </div>
</x-filament-panels::page>
