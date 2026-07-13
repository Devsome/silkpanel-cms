@if ($available)
    <div class="ag-card p-5">
        <p class="ag-section-eyebrow mb-4">{{ __('ranking.character_globals') }}</p>

        <div class="space-y-2.5">
            @forelse ($globals as $g)
                <div class="text-sm border-b ag-divider last:border-0 pb-2.5 last:pb-0">
                    <p class="ag-text-surface break-words">{{ $g->Comment }}</p>
                    <p class="mt-0.5 text-xs ag-text-muted"
                        title="{{ \Carbon\Carbon::make($g->EventTime)?->toDayDateTimeString() }}">
                        {{ \Carbon\Carbon::make($g->EventTime)?->diffForHumans() }}
                    </p>
                </div>
            @empty
                <p class="text-sm ag-text-muted">{{ __('ranking.character_globals_empty') }}</p>
            @endforelse
        </div>
    </div>
@endif
