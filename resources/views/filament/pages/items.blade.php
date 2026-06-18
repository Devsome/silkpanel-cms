<x-filament-panels::page>
    @php
        $stats = $this->getStats();
    @endphp

    {{-- Stats row --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3 text-center shadow-sm">
            <div class="text-2xl font-bold tabular-nums text-gray-900 dark:text-gray-100">{{ number_format($stats['total']) }}</div>
            <div class="mt-0.5 text-xs text-gray-400 dark:text-gray-500">{{ __('filament/items.stat_total') }}</div>
        </div>
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3 text-center shadow-sm">
            <div class="text-2xl font-bold tabular-nums text-red-600 dark:text-red-400">{{ number_format($stats['weapons']) }}</div>
            <div class="mt-0.5 text-xs text-gray-400 dark:text-gray-500">{{ __('filament/items.stat_weapons') }}</div>
        </div>
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3 text-center shadow-sm">
            <div class="text-2xl font-bold tabular-nums text-blue-600 dark:text-blue-400">{{ number_format($stats['armors']) }}</div>
            <div class="mt-0.5 text-xs text-gray-400 dark:text-gray-500">{{ __('filament/items.stat_armors') }}</div>
        </div>
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3 text-center shadow-sm">
            <div class="text-2xl font-bold tabular-nums text-amber-600 dark:text-amber-400">{{ number_format($stats['accessories']) }}</div>
            <div class="mt-0.5 text-xs text-gray-400 dark:text-gray-500">{{ __('filament/items.stat_accessories') }}</div>
        </div>
    </div>

    {{-- Search & Filter bar --}}
    <div class="flex flex-col sm:flex-row gap-3">
        <div class="flex-1">
            <x-filament::input.wrapper>
                <x-filament::input
                    type="text"
                    wire:model.live.debounce.350ms="search"
                    placeholder="{{ __('filament/items.search_placeholder') }}"
                    autofocus
                />
            </x-filament::input.wrapper>
        </div>
        <div class="sm:w-48">
            <x-filament::input.wrapper>
                <x-filament::input.select wire:model.live="typeFilter">
                    <option value="">{{ __('filament/items.filter_all_types') }}</option>
                    <option value="1">Weapon</option>
                    <option value="2">Protector</option>
                    <option value="3">Accessory</option>
                    <option value="4">Avatar</option>
                    <option value="5">Cosmetic</option>
                </x-filament::input.select>
            </x-filament::input.wrapper>
        </div>
    </div>

    {{-- Table --}}
    @php $paginator = $this->getItems(); @endphp

    <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
        {{-- Results count --}}
        <div class="flex items-center justify-between px-4 py-2.5 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
            <span class="text-sm text-gray-500 dark:text-gray-400">
                {{ number_format($paginator->total()) }} items
                @if(strlen($search) >= 2)
                    for <span class="font-medium text-gray-700 dark:text-gray-300">"{{ $search }}"</span>
                @endif
            </span>
            <span class="text-xs text-gray-400">
                Page {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
            </span>
        </div>

        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 dark:border-gray-700 bg-gray-50/80 dark:bg-gray-800/80">
                    <th class="w-12 px-3 py-2.5 text-center text-xs font-semibold uppercase tracking-wide text-gray-500"></th>
                    <th class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('filament/items.col_name') }}</th>
                    <th class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('filament/items.col_type') }}</th>
                    <th class="px-4 py-2.5 text-center text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('filament/items.col_level') }}</th>
                    <th class="hidden sm:table-cell px-4 py-2.5 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('filament/items.col_price') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
                @forelse($paginator->items() as $item)
                    @php
                        $name = ($item->NameENG && $item->NameENG !== '0') ? $item->NameENG : $item->CodeName128;
                        $iconUrl = \App\Filament\Pages\Items::iconUrl($item->AssocFileIcon128 ?? '');
                        $detailUrl = \App\Filament\Pages\Items::getDetailUrl($item->ID);
                    @endphp
                    <tr
                        wire:key="item-{{ $item->ID }}"
                        class="group cursor-pointer transition-colors hover:bg-primary-50 dark:hover:bg-primary-900/10"
                        onclick="window.location.href='{{ $detailUrl }}'"
                    >
                        {{-- Icon --}}
                        <td class="px-3 py-2 text-center">
                            <img
                                src="{{ $iconUrl }}"
                                alt="{{ $name }}"
                                class="h-8 w-8 mx-auto object-contain rounded"
                                loading="lazy"
                                onerror="this.src='{{ asset('images/silkroad/icon_default.png') }}'"
                            />
                        </td>

                        {{-- Name --}}
                        <td class="px-4 py-2">
                            @php
                                $itemClass = (int)($item->getRefObjItem?->ItemClass ?? 0);
                                $soxLabel  = \App\Filament\Pages\Items::soxLabel($item->CodeName128, $itemClass);
                                $soxColor  = \App\Filament\Pages\Items::soxColor($soxLabel);
                            @endphp
                            <div class="font-medium text-gray-900 dark:text-gray-100 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                                {{ $name }}
                            </div>
                            <div class="mt-0.5 flex items-center gap-1.5 flex-wrap">
                                <span class="font-mono text-xs text-gray-400 dark:text-gray-500">{{ $item->CodeName128 }}</span>
                                @if($soxLabel)
                                    <x-filament::badge color="{{ $soxColor }}" size="sm">{{ $soxLabel }}</x-filament::badge>
                                @elseif($item->Rarity > 0)
                                    @php $rarityLabel = \App\Filament\Pages\Items::rarityLabel((int)$item->Rarity); @endphp
                                    @if($rarityLabel)
                                        <x-filament::badge color="{{ \App\Filament\Pages\Items::rarityColor((int)$item->Rarity) }}" size="sm">{{ $rarityLabel }}</x-filament::badge>
                                    @endif
                                @endif
                            </div>
                        </td>

                        {{-- Code column removed — now shown inline under name --}}

                        {{-- Type --}}
                        <td class="px-4 py-2">
                            <x-filament::badge color="{{ \App\Filament\Pages\Items::typeColor((int)$item->TypeID2, (int)$item->TypeID3) }}" size="sm">
                                {{ \App\Filament\Pages\Items::typeName((int)$item->TypeID2, (int)$item->TypeID3, (int)$item->TypeID4) }}
                            </x-filament::badge>
                        </td>

                        {{-- Level --}}
                        <td class="px-4 py-2 text-center">
                            @if($item->ReqLevel1 > 0)
                                <span class="inline-flex items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700 px-2 py-0.5 text-xs font-semibold text-gray-700 dark:text-gray-300">
                                    {{ $item->ReqLevel1 }}
                                </span>
                            @else
                                <span class="text-gray-300 dark:text-gray-600">—</span>
                            @endif
                        </td>

                        {{-- Price --}}
                        <td class="hidden sm:table-cell px-4 py-2 text-right">
                            @if($item->Price > 0)
                                <span class="text-gray-700 dark:text-gray-300 font-medium tabular-nums">
                                    {{ number_format($item->Price) }}
                                </span>
                                <span class="text-xs text-gray-400">g</span>
                            @else
                                <span class="text-gray-300 dark:text-gray-600">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-12 text-center text-gray-400 dark:text-gray-500">
                            No items found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        @if($paginator->lastPage() > 1)
            <div class="flex items-center justify-between px-4 py-3 border-t border-gray-100 dark:border-gray-700 bg-gray-50/80 dark:bg-gray-800/80">
                <button
                    wire:click="previousPage"
                    @disabled($paginator->currentPage() <= 1)
                    class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-sm font-medium transition-colors
                           @if($paginator->currentPage() <= 1) text-gray-300 dark:text-gray-600 cursor-not-allowed
                           @else text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer @endif"
                >
                    <x-heroicon-o-chevron-left class="h-4 w-4" /> Previous
                </button>

                {{-- Page numbers --}}
                <div class="flex items-center gap-1">
                    @php
                        $lastPage = $paginator->lastPage();
                        $current  = $paginator->currentPage();
                        $pages    = [];
                        if ($lastPage <= 7) {
                            $pages = range(1, $lastPage);
                        } else {
                            $pages[] = 1;
                            if ($current > 3) $pages[] = '…';
                            for ($p = max(2, $current - 1); $p <= min($lastPage - 1, $current + 1); $p++) $pages[] = $p;
                            if ($current < $lastPage - 2) $pages[] = '…';
                            $pages[] = $lastPage;
                        }
                    @endphp
                    @foreach($pages as $page)
                        @if($page === '…')
                            <span class="px-1.5 text-gray-400">…</span>
                        @else
                            <button
                                wire:click="goToPage({{ $page }})"
                                class="h-8 w-8 rounded-lg text-sm font-medium transition-colors
                                       {{ $page === $current
                                           ? 'bg-primary-600 text-white'
                                           : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                            >{{ $page }}</button>
                        @endif
                    @endforeach
                </div>

                <button
                    wire:click="nextPage({{ $paginator->lastPage() }})"
                    @disabled($paginator->currentPage() >= $paginator->lastPage())
                    class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-sm font-medium transition-colors
                           @if($paginator->currentPage() >= $paginator->lastPage()) text-gray-300 dark:text-gray-600 cursor-not-allowed
                           @else text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer @endif"
                >
                    Next <x-heroicon-o-chevron-right class="h-4 w-4" />
                </button>
            </div>
        @endif
    </div>
</x-filament-panels::page>
