@if ($available)
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider mb-4">
            {{ __('ranking.character_globals') }}
        </h2>

        @forelse ($globals as $g)
            <div class="text-sm border-b border-gray-100 dark:border-gray-700/60 last:border-0 py-2.5 first:pt-0 last:pb-0">
                <p class="text-gray-800 dark:text-gray-200 break-words">{{ $g->Comment }}</p>
                <p class="mt-0.5 text-xs text-gray-400 dark:text-gray-500"
                    title="{{ \Carbon\Carbon::make($g->EventTime)?->toDayDateTimeString() }}">
                    {{ \Carbon\Carbon::make($g->EventTime)?->diffForHumans() }}
                </p>
            </div>
        @empty
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('ranking.character_globals_empty') }}</p>
        @endforelse
    </div>
@endif
