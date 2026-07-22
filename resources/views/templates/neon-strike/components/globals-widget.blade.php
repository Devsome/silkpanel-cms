@if ($available)
    <div class="border border-violet-500/20 bg-zinc-950/50 p-5">
        <div class="mb-4">
            <h3 class="text-xs font-black uppercase tracking-[0.2em] bg-linear-to-r from-violet-400 to-fuchsia-400 bg-clip-text text-transparent">
                {{ __('history.widget_title') }}
            </h3>
        </div>

        <ul class="divide-y divide-zinc-800/50">
            @forelse ($globals as $g)
                @php $slug = \Illuminate\Support\Str::slug($g->CharName ?? ''); @endphp
                <li class="py-3 first:pt-0 last:pb-0 text-sm">
                    <p class="text-zinc-200 break-words">{{ $g->Comment }}</p>
                    <div class="mt-1 flex flex-wrap items-center gap-x-1.5 text-xs font-mono uppercase tracking-wider text-zinc-500">
                        @if (filled($g->CharName) && $slug !== '')
                            <a href="{{ route('ranking.characters.show', ['idOrSlug' => $slug]) }}"
                                class="font-bold text-violet-400 hover:text-violet-300 transition">{{ $g->CharName }}</a>
                        @elseif (filled($g->CharName))
                            <span class="font-bold text-zinc-300">{{ $g->CharName }}</span>
                        @endif
                        <span aria-hidden="true">·</span>
                        <span title="{{ \Carbon\Carbon::make($g->EventTime)?->toDayDateTimeString() }}">
                            {{ \Carbon\Carbon::make($g->EventTime)?->diffForHumans() }}
                        </span>
                    </div>
                </li>
            @empty
                <li class="py-3 text-xs font-mono uppercase tracking-[0.2em] text-zinc-600">{{ __('history.widget_empty') }}</li>
            @endforelse
        </ul>
    </div>
@endif
