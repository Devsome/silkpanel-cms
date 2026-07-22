@if ($available)
    <div class="ag-card p-5">
        <div class="mb-4">
            <p class="ag-section-eyebrow">{{ __('history.widget_title') }}</p>
        </div>

        <ul class="space-y-3">
            @forelse ($globals as $g)
                @php $slug = \Illuminate\Support\Str::slug($g->CharName ?? ''); @endphp
                <li class="text-sm border-b ag-divider last:border-0 pb-3 last:pb-0">
                    <p class="ag-text-surface break-words">{{ $g->Comment }}</p>
                    <div class="mt-1 flex flex-wrap items-center gap-x-1.5 text-xs ag-text-muted">
                        @if (filled($g->CharName) && $slug !== '')
                            <a href="{{ route('ranking.characters.show', ['idOrSlug' => $slug]) }}"
                                class="font-semibold ag-text-primary hover:underline">{{ $g->CharName }}</a>
                        @elseif (filled($g->CharName))
                            <span class="font-semibold ag-text-surface">{{ $g->CharName }}</span>
                        @endif
                        <span aria-hidden="true">·</span>
                        <span title="{{ \Carbon\Carbon::make($g->EventTime)?->toDayDateTimeString() }}">
                            {{ \Carbon\Carbon::make($g->EventTime)?->diffForHumans() }}
                        </span>
                    </div>
                </li>
            @empty
                <li class="text-sm ag-text-muted">{{ __('history.widget_empty') }}</li>
            @endforelse
        </ul>
    </div>
@endif
