@if ($available)
    <div class="rounded-2xl border border-gray-800 bg-gray-900/50 p-5">
        <div class="mb-4">
            <h3 class="text-sm font-bold uppercase tracking-widest text-white">
                {{ __('history.widget_title') }}
            </h3>
        </div>

        <ul class="divide-y divide-gray-800/60">
            @forelse ($globals as $g)
                @php $slug = \Illuminate\Support\Str::slug($g->CharName ?? ''); @endphp
                <li class="py-3 first:pt-0 last:pb-0 text-sm">
                    <p class="text-gray-200 break-words">{{ $g->Comment }}</p>
                    <div class="mt-1 flex flex-wrap items-center gap-x-1.5 text-xs text-gray-500">
                        @if (filled($g->CharName) && $slug !== '')
                            <a href="{{ route('ranking.characters.show', ['idOrSlug' => $slug]) }}"
                                class="font-semibold text-emerald-400 hover:text-emerald-300 transition">{{ $g->CharName }}</a>
                        @elseif (filled($g->CharName))
                            <span class="font-semibold text-gray-300">{{ $g->CharName }}</span>
                        @endif
                        <span aria-hidden="true">·</span>
                        <span title="{{ \Carbon\Carbon::make($g->EventTime)?->toDayDateTimeString() }}">
                            {{ \Carbon\Carbon::make($g->EventTime)?->diffForHumans() }}
                        </span>
                    </div>
                </li>
            @empty
                <li class="py-3 text-sm text-gray-500">{{ __('history.widget_empty') }}</li>
            @endforelse
        </ul>
    </div>
@endif
