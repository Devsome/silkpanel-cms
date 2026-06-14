<div>
    @if (!$configured)
        <div class="text-center py-12">
            <p class="text-gray-500 dark:text-gray-400">{{ __('ranking.custom_not_configured') }}</p>
        </div>
    @else
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                {{ e($title) }}
            </h2>
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
                                        {{ e((string) ($row->{$col['column']} ?? '—')) }}
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
    @endif
</div>
