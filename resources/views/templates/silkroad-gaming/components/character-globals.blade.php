@if ($available)
    <div class="rounded-2xl border border-gray-800 bg-gray-900/50 p-5">
        <h2 class="text-sm font-bold uppercase tracking-widest text-white mb-4">
            {{ __('ranking.character_globals') }}
        </h2>

        <div class="divide-y divide-gray-800/60">
            @forelse ($globals as $g)
                <div class="py-2.5 first:pt-0 last:pb-0 text-sm">
                    <p class="text-gray-200 break-words">{{ $g->Comment }}</p>
                    <p class="mt-0.5 text-xs text-gray-500"
                        title="{{ \Carbon\Carbon::make($g->EventTime)?->toDayDateTimeString() }}">
                        {{ \Carbon\Carbon::make($g->EventTime)?->diffForHumans() }}
                    </p>
                </div>
            @empty
                <p class="text-sm text-gray-500">{{ __('ranking.character_globals_empty') }}</p>
            @endforelse
        </div>
    </div>
@endif
