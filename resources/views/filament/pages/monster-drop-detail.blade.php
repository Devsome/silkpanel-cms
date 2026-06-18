<x-filament-panels::page>
    @php
        $monster = $this->getMonster();
        $monsterName = $monster
            ? (($monster->NameENG && $monster->NameENG !== '0') ? $monster->NameENG : $monster->ObjName128)
            : null;
    @endphp

    {{-- Back link --}}
    <div class="mb-2">
        <a href="{{ \App\Filament\Pages\MonsterDrops::getUrl() }}"
           class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
            <x-heroicon-o-arrow-left class="h-4 w-4" />
            {{ __('filament/monster-drops.back_to_search') }}
        </a>
    </div>

    @if(!$monster)
        <x-filament::section>
            <div class="py-8 text-center text-gray-400">
                {{ __('filament/monster-drops.monster_not_found', ['code' => $code]) }}
            </div>
        </x-filament::section>
    @else
        {{-- Monster info header --}}
        <x-filament::section>
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ $monsterName }}</h2>
                        @if($monster->Rarity > 0)
                            @php
                                $rarityLabel = \App\Filament\Pages\MonsterDrops::rarityLabel((int)$monster->Rarity);
                                $rarityColor = \App\Filament\Pages\MonsterDrops::rarityColor((int)$monster->Rarity);
                            @endphp
                            @if($rarityLabel)
                                <x-filament::badge color="{{ $rarityColor }}">{{ $rarityLabel }}</x-filament::badge>
                            @endif
                        @endif
                    </div>
                    @if($monster->ObjName128 && $monster->ObjName128 !== $monsterName)
                        <div class="text-sm text-gray-500 mt-0.5">{{ $monster->ObjName128 }}</div>
                    @endif
                    <div class="mt-1 font-mono text-xs text-gray-400">{{ $monster->CodeName128 }}</div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <x-filament::badge color="info" size="lg">
                        {{ __('filament/monster-drops.level') }} {{ $monster->Lvl }}
                    </x-filament::badge>
                </div>
            </div>

            {{-- Stats row --}}
            <div class="mt-4 flex flex-wrap gap-x-6 gap-y-1 text-sm">
                <div class="flex items-center gap-1.5 text-gray-500 dark:text-gray-400">
                    <x-heroicon-o-heart class="h-4 w-4 text-red-400" />
                    <span class="font-medium text-gray-700 dark:text-gray-300">{{ number_format($monster->MaxHP) }}</span>
                    <span class="text-xs">HP</span>
                </div>
                <div class="flex items-center gap-1.5 text-gray-500 dark:text-gray-400">
                    <x-heroicon-o-star class="h-4 w-4 text-yellow-400" />
                    <span class="font-medium text-gray-700 dark:text-gray-300">{{ number_format($monster->ExpToGive) }}</span>
                    <span class="text-xs">EXP</span>
                </div>
            </div>
        </x-filament::section>

        @php
            $assignedDrops      = $this->getAssignedDrops();
            $assignedGroupDrops = $this->getAssignedGroupDrops();
            $goldDrop           = $this->getGoldDrop();
            $dropGroups         = $this->getDropGroups();
        @endphp

        {{-- Gold --}}
        @if($goldDrop)
            <x-filament::section>
                <x-slot name="heading">
                    <span class="text-yellow-600 dark:text-yellow-400">{{ __('filament/monster-drops.section_gold') }}</span>
                </x-slot>
                <div class="flex items-center gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">{{ __('filament/monster-drops.gold_chance') }}</span>
                        <x-filament::badge color="warning">
                            {{ round($goldDrop->DropProb * 100, 4) }}%
                        </x-filament::badge>
                    </div>
                    <div class="text-gray-700 dark:text-gray-300">
                        <span class="text-gray-500">{{ __('filament/monster-drops.gold_amount') }}</span>
                        {{ number_format($goldDrop->GoldMin) }} – {{ number_format($goldDrop->GoldMax) }}
                    </div>
                </div>
            </x-filament::section>
        @endif

        {{-- Assigned group drops (_RefMonster_AssignedItemRndDrop) --}}
        @foreach($assignedGroupDrops as $group)
            <x-filament::section>
                <x-slot name="heading">
                    <span @class(['opacity-50' => $group['status'] === 'disabled'])>
                        {{ $group['category'] }}
                    </span>
                    <span class="ml-2 text-sm font-normal text-gray-400 inline-flex items-center gap-1.5 flex-wrap">
                        <span class="font-mono text-xs">{{ $group['groupCode'] }}</span> ·
                        <x-filament::badge color="{{ $group['prob'] >= 0.5 ? 'success' : ($group['prob'] >= 0.1 ? 'warning' : ($group['prob'] >= 0.01 ? 'info' : 'gray')) }}">
                            {{ round($group['prob'] * 100, 2) }}% {{ __('filament/monster-drops.group_probability') }}
                        </x-filament::badge>
                        @if($group['amountMin'] != $group['amountMax'])
                            <x-filament::badge color="gray">{{ $group['amountMin'] }}–{{ $group['amountMax'] }}x</x-filament::badge>
                        @elseif($group['amountMin'] > 1)
                            <x-filament::badge color="gray">{{ $group['amountMin'] }}x</x-filament::badge>
                        @endif
                        @if($group['status'] === 'disabled')
                            <x-filament::badge color="danger">Disabled (CanDrop=0)</x-filament::badge>
                        @elseif($group['status'] === 'partial')
                            <x-filament::badge color="warning">{{ $group['disabledCount'] }}/{{ $group['totalCount'] }} items disabled</x-filament::badge>
                        @endif
                    </span>
                </x-slot>
                @include('filament.pages.partials.drop-table', [
                    'drops' => $group['items'],
                    'ratioField' => 'SelectRatio',
                    'ratioIsAbsolute' => false,
                    'showDisabled' => true,
                ])
            </x-filament::section>
        @endforeach

        {{-- Assigned (special) drops --}}
        @if($assignedDrops->isNotEmpty())
            <x-filament::section>
                <x-slot name="heading">{{ __('filament/monster-drops.section_assigned') }}</x-slot>
                @include('filament.pages.partials.drop-table', ['drops' => $assignedDrops, 'ratioField' => 'DropRatio', 'ratioIsAbsolute' => true])
            </x-filament::section>
        @endif

        {{-- Drop class groups --}}
        @if(!empty($dropGroups))
            @foreach($dropGroups as $group)
                <x-filament::section>
                    <x-slot name="heading">
                        <span @class(['opacity-50' => $group['status'] === 'disabled'])>
                            {{ $group['category'] }}
                        </span>
                        <span class="ml-2 text-sm font-normal text-gray-400 inline-flex items-center gap-1.5 flex-wrap">
                            <span class="font-mono text-xs">{{ $group['groupCode'] }}</span> ·
                            <x-filament::badge color="{{ $group['prob'] >= 0.5 ? 'success' : ($group['prob'] >= 0.1 ? 'warning' : ($group['prob'] >= 0.01 ? 'info' : 'gray')) }}">
                                {{ round($group['prob'] * 100, 4) }}% {{ __('filament/monster-drops.group_probability') }}
                            </x-filament::badge>
                            @if($group['status'] === 'disabled')
                                <x-filament::badge color="danger">Disabled (CanDrop=0)</x-filament::badge>
                            @elseif($group['status'] === 'partial')
                                <x-filament::badge color="warning">{{ $group['disabledCount'] }}/{{ $group['totalCount'] }} items disabled</x-filament::badge>
                            @endif
                        </span>
                    </x-slot>
                    @include('filament.pages.partials.drop-table', [
                        'drops' => $group['items'],
                        'ratioField' => $group['ratioField'] ?? 'SelectRatio',
                        'ratioIsAbsolute' => false,
                        'showDisabled' => true,
                    ])
                </x-filament::section>
            @endforeach
        @endif

        @if($assignedDrops->isEmpty() && empty($assignedGroupDrops) && empty($dropGroups) && !$goldDrop)
            <x-filament::section>
                <div class="py-8 text-center text-gray-400 dark:text-gray-500">
                    {{ __('filament/monster-drops.no_drops') }}
                </div>
            </x-filament::section>
        @endif
    @endif
</x-filament-panels::page>
