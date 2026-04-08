<div>
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">
            {{ e($title) }}
        </h2>
        <div class="relative w-full sm:w-64">
            <div
                class="flex items-center rounded-md bg-white outline-1 -outline-offset-1 outline-gray-300 focus-within:outline-2 focus-within:-outline-offset-2 focus-within:outline-indigo-600 dark:bg-gray-800 dark:outline-gray-600 dark:focus-within:outline-indigo-500">
                <svg class="ml-3 w-4 h-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="{{ __('ranking.search_guild_placeholder') }}"
                    class="block min-w-0 grow bg-transparent py-1.5 pr-3 pl-2 text-base text-gray-900 placeholder:text-gray-400 focus:outline-none dark:text-white dark:placeholder:text-gray-500 sm:text-sm/6">
            </div>
        </div>
    </div>

    @if ($rows->count() > 0)
        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            #
                        </th>
                        @foreach ($columns as $col)
                            <th
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ e($col['label']) }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($rows as $row)
                        @php $rank = $startRank + $loop->index; @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                            <td
                                class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-500 dark:text-gray-400">
                                @if ($rank <= 3)
                                    <span
                                        class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold
                                        {{ $rank === 1 ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' : '' }}
                                        {{ $rank === 2 ? 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300' : '' }}
                                        {{ $rank === 3 ? 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400' : '' }}">
                                        {{ $rank }}
                                    </span>
                                @else
                                    {{ $rank }}
                                @endif
                            </td>
                            @foreach ($columns as $col)
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    @php $value = $row->{$col['column']} ?? '—'; @endphp
                                    @if ($col['column'] === 'Name')
                                        <a href="{{ route('ranking.guilds.show', $row->ID) }}"
                                            class="font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                                            {{ e((string) $value) }}
                                        </a>
                                    @elseif ($col['column'] === 'CrestIcon' && !empty($row->CrestDataUri))
                                        <img src="{{ $row->CrestDataUri }}" alt="Crest" class="w-8 h-8">
                                    @elseif ($col['column'] === 'ItemPoints')
                                        <span class="font-semibold text-indigo-600 dark:text-indigo-400">
                                            {{ number_format((int) $value) }}
                                        </span>
                                    @elseif ($col['column'] === 'TotalMember')
                                        <span class="font-medium">{{ $value }}</span>
                                    @elseif ($col['column'] === 'FoundationDate' && $value !== '—')
                                        {{ \Carbon\Carbon::parse($value)->format('d M Y') }}
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
            <p class="text-gray-500 dark:text-gray-400">{{ __('ranking.no_data') }}</p>
        </div>
    @endif
</div>
