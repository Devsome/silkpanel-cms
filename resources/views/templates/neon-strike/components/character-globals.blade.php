@if ($available)
    <div class="border border-violet-500/20 bg-zinc-950/50 p-5">
        <h2 class="text-xs font-black uppercase tracking-[0.2em] bg-linear-to-r from-violet-400 to-fuchsia-400 bg-clip-text text-transparent mb-4">
            {{ __('ranking.character_globals') }}
        </h2>

        <div class="divide-y divide-zinc-800/50">
            @forelse ($globals as $g)
                <div class="py-2.5 first:pt-0 last:pb-0 text-sm">
                    <p class="text-zinc-200 break-words">{{ $g->Comment }}</p>
                    <p class="mt-0.5 text-xs font-mono uppercase tracking-wider text-zinc-500"
                        title="{{ \Carbon\Carbon::make($g->EventTime)?->toDayDateTimeString() }}">
                        {{ \Carbon\Carbon::make($g->EventTime)?->diffForHumans() }}
                    </p>
                </div>
            @empty
                <p class="text-xs font-mono uppercase tracking-[0.2em] text-zinc-600">{{ __('ranking.character_globals_empty') }}</p>
            @endforelse
        </div>
    </div>
@endif
