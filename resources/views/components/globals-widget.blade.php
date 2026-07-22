@if ($available)
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                {{ __('history.widget_title') }}
            </h3>
        </div>

        <ul class="space-y-3">
            @forelse ($globals as $g)
                @php $slug = \Illuminate\Support\Str::slug($g->CharName ?? ''); @endphp
                <li class="text-sm border-b border-gray-100 dark:border-gray-700/60 last:border-0 pb-3 last:pb-0">
                    <p class="text-gray-800 dark:text-gray-200 break-words">{{ $g->Comment }}</p>
                    <div class="mt-1 flex flex-wrap items-center gap-x-1.5 text-xs text-gray-400 dark:text-gray-500">
                        @if (filled($g->CharName) && $slug !== '')
                            <a href="{{ route('ranking.characters.show', ['idOrSlug' => $slug]) }}"
                                class="font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">{{ $g->CharName }}</a>
                        @elseif (filled($g->CharName))
                            <span class="font-semibold text-gray-600 dark:text-gray-300">{{ $g->CharName }}</span>
                        @endif
                        <span aria-hidden="true">·</span>
                        <span title="{{ \Carbon\Carbon::make($g->EventTime)?->toDayDateTimeString() }}">
                            {{ \Carbon\Carbon::make($g->EventTime)?->diffForHumans() }}
                        </span>
                    </div>
                </li>
            @empty
                <li class="text-sm text-gray-400 dark:text-gray-500">{{ __('history.widget_empty') }}</li>
            @endforelse
        </ul>
    </div>
@endif
