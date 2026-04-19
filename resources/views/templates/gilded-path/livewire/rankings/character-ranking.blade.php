<div>
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h2 class="text-xl font-bold font-headline gp-text-primary uppercase tracking-widest">
            {{ e($title) }}
        </h2>
        <div class="relative w-full sm:w-64">
            <div class="flex items-center gp-ghost-border">
                <svg class="ml-3 w-4 h-4 shrink-0 gp-text-outline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="{{ __('ranking.search_placeholder') }}"
                    class="block min-w-0 grow gp-input py-1.5 pr-3 pl-2 text-base placeholder:gp-text-outline focus:outline-none sm:text-sm/6 border-0">
            </div>
        </div>
    </div>

    @if ($rows->count() > 0)
        <div class="overflow-x-auto gp-card gp-ghost-border">
            <table class="min-w-full divide-y" style="border-color: rgba(77, 70, 53, 0.2);">
                <thead class="gp-card-high">
                    <tr>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium gp-text-on-surface-variant uppercase tracking-wider">
                            #
                        </th>
                        @foreach ($columns as $col)
                            <th
                                class="px-4 py-3 text-left text-xs font-medium gp-text-on-surface-variant uppercase tracking-wider">
                                {{ e($col['label']) }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color: rgba(77, 70, 53, 0.2);">
                    @foreach ($rows as $row)
                        @php $rank = $startRank + $loop->index; @endphp
                        <tr class="transition-colors hover:gp-card-high"
                            style="background-color: var(--gp-surface-container);">
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium gp-text-outline">
                                @if ($rank <= 3)
                                    <span
                                        class="inline-flex items-center justify-center w-6 h-6 text-xs font-bold
                                        {{ $rank === 1 ? 'gp-gold-btn' : '' }}
                                        {{ $rank === 2 ? 'bg-gray-600 text-gray-200' : '' }}
                                        {{ $rank === 3 ? 'bg-amber-900/40 text-amber-500' : '' }}">
                                        {{ $rank }}
                                    </span>
                                @else
                                    {{ $rank }}
                                @endif
                            </td>
                            @foreach ($columns as $col)
                                <td class="px-4 py-3 whitespace-nowrap text-sm gp-text-on-surface">
                                    @php $value = $row->{$col['column']} ?? '—'; @endphp
                                    @if ($col['column'] === 'CharName16')
                                        <a href="{{ route('ranking.characters.show', $row->CharID) }}"
                                            class="font-medium gp-text-primary hover:text-yellow-300 hover:underline transition-colors">
                                            {{ e((string) $value) }}
                                        </a>
                                    @elseif ($col['column'] === 'GuildName' && !empty($row->GuildID))
                                        <a href="{{ route('ranking.guilds.show', $row->GuildID) }}"
                                            class="gp-text-primary hover:text-yellow-300 hover:underline transition-colors">
                                            {{ e((string) $value) }}
                                        </a>
                                    @elseif ($col['column'] === 'ItemPoints')
                                        <span class="font-semibold gp-text-primary">
                                            {{ number_format((int) $value) }}
                                        </span>
                                    @elseif ($col['column'] === 'CurLevel')
                                        <span class="font-medium">{{ $value }}</span>
                                    @else
                                        {{ e((string) $value) }}
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($paginate && $rows->hasPages())
            <div class="mt-4">
                {{ $rows->links() }}
            </div>
        @endif
    @else
        <div class="text-center py-12">
            <p class="gp-text-on-surface-variant">{{ __('ranking.no_data') }}</p>
        </div>
    @endif
</div>
