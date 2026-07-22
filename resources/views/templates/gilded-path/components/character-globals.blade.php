@if ($available)
    <div class="gp-card gp-ghost-border p-5">
        <h2 class="text-sm font-bold font-headline gp-text-primary uppercase tracking-widest mb-4">
            {{ __('ranking.character_globals') }}
        </h2>

        <div class="divide-y" style="border-color: rgba(77, 70, 53, 0.2);">
            @forelse ($globals as $g)
                <div class="py-2.5 first:pt-0 last:pb-0 text-sm">
                    <p class="gp-text-on-surface break-words">{{ $g->Comment }}</p>
                    <p class="mt-0.5 text-xs gp-text-on-surface-variant"
                        title="{{ \Carbon\Carbon::make($g->EventTime)?->toDayDateTimeString() }}">
                        {{ \Carbon\Carbon::make($g->EventTime)?->diffForHumans() }}
                    </p>
                </div>
            @empty
                <p class="text-sm gp-text-on-surface-variant">{{ __('ranking.character_globals_empty') }}</p>
            @endforelse
        </div>
    </div>
@endif
