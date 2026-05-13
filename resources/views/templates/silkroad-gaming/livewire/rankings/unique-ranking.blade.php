<div>
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h2 class="text-xl font-bold text-white uppercase tracking-widest">{{ e($title) }}</h2>
        @if ($configured)
            <div class="relative w-full sm:w-64">
                <div class="flex items-center rounded-xl border border-gray-700 bg-gray-800/80 overflow-hidden">
                    <svg class="ml-3 w-4 h-4 shrink-0 text-gray-500" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="{{ __('ranking.search_unique_placeholder') }}"
                        class="block min-w-0 grow bg-transparent py-1.5 pr-3 pl-2 text-sm text-gray-100 placeholder:text-gray-600 focus:outline-none border-0">
                </div>
            </div>
        @endif
    </div>

    @if (!$configured)
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.573-1.066z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <p class="mt-4 text-gray-500">{{ __('ranking.unique_not_configured') }}</p>
        </div>
    @elseif ($rows->count() > 0)
        <div class="overflow-x-auto rounded-2xl border border-gray-800 bg-gray-900/50">
            <table class="min-w-full divide-y divide-gray-800">
                <thead class="bg-gray-900/80">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#
                        </th>
                        @foreach ($columns as $col)
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ e($col['label']) }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/50">
                    @foreach ($rows as $row)
                        @php $rank = $startRank + $loop->index; @endphp
                        <tr class="hover:bg-emerald-500/5 transition">
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-500">
                                @if ($rank <= 3)
                                    <span
                                        class="inline-flex items-center justify-center w-6 h-6 rounded text-xs font-bold
                                        {{ $rank === 1 ? 'bg-amber-500/20 text-amber-400' : '' }}
                                        {{ $rank === 2 ? 'bg-gray-600/50 text-gray-300' : '' }}
                                        {{ $rank === 3 ? 'bg-amber-900/30 text-amber-600' : '' }}">
                                        {{ $rank }}
                                    </span>
                                @else
                                    {{ $rank }}
                                @endif
                            </td>
                            @foreach ($columns as $col)
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-200">
                                    @php $value = $row->{$col['column']} ?? '—'; @endphp
                                    @if ($col['column'] === '__unique_points')
                                        <span
                                            class="font-semibold text-emerald-400">{{ number_format((int) $value) }}</span>
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
            <div class="mt-4">{{ $rows->links() }}</div>
        @endif
    @else
        <div class="text-center py-12">
            <p class="text-gray-500">{{ __('ranking.no_data') }}</p>
        </div>
    @endif
</div>
