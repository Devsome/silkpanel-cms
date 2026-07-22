@if ($available)
    <div class="gp-card gp-ghost-border p-5">
        <div class="mb-4">
            <h3 class="text-sm font-bold font-headline gp-text-primary uppercase tracking-widest">
                {{ __('history.widget_title') }}
            </h3>
        </div>

        <ul class="divide-y" style="border-color: rgba(77, 70, 53, 0.2);">
            @forelse ($globals as $g)
                @php $slug = \Illuminate\Support\Str::slug($g->CharName ?? ''); @endphp
                <li class="py-3 first:pt-0 last:pb-0 text-sm">
                    <p class="gp-text-on-surface break-words">{{ $g->Comment }}</p>
                    <div class="mt-1 flex flex-wrap items-center gap-x-1.5 text-xs gp-text-on-surface-variant">
                        @if (filled($g->CharName) && $slug !== '')
                            <a href="{{ route('ranking.characters.show', ['idOrSlug' => $slug]) }}"
                                class="font-semibold gp-text-primary hover:text-yellow-400 transition">{{ $g->CharName }}</a>
                        @elseif (filled($g->CharName))
                            <span class="font-semibold gp-text-on-surface">{{ $g->CharName }}</span>
                        @endif
                        <span aria-hidden="true">·</span>
                        <span title="{{ \Carbon\Carbon::make($g->EventTime)?->toDayDateTimeString() }}">
                            {{ \Carbon\Carbon::make($g->EventTime)?->diffForHumans() }}
                        </span>
                    </div>
                </li>
            @empty
                <li class="py-3 text-sm gp-text-on-surface-variant">{{ __('history.widget_empty') }}</li>
            @endforelse
        </ul>
    </div>
@endif
