<div>
    @if (count($timers) > 0)
        <div class="space-y-2">
            @foreach ($timers as $timer)
                <div
                    class="group relative flex items-center justify-between border border-violet-500/15 bg-zinc-950 px-4 py-3 overflow-hidden
                    hover:border-violet-500/40 transition-all duration-200">

                    {{-- Subtle left accent bar --}}
                    <div
                        class="absolute left-0 inset-y-0 w-[2px] bg-gradient-to-b from-violet-500/0 via-violet-500/60 to-violet-500/0 group-hover:via-fuchsia-500 transition-colors duration-300">
                    </div>

                    {{-- Left: Icon + Name --}}
                    <div class="flex items-center gap-3 min-w-0 pl-1">
                        @if ($timer['image'])
                            <div class="relative shrink-0">
                                <img src="{{ asset('storage/' . $timer['image']) }}" alt="{{ e($timer['name']) }}"
                                    class="h-10 w-10 object-cover border border-zinc-700 group-hover:border-violet-500/40 transition">
                                <div
                                    class="absolute inset-0 border border-violet-500/0 group-hover:border-violet-500/20 transition">
                                </div>
                            </div>
                        @elseif ($timer['icon'])
                            <div
                                class="shrink-0 flex h-9 w-9 items-center justify-center border border-zinc-700 bg-zinc-900 group-hover:border-violet-500/40 text-violet-400 transition">
                                <x-dynamic-component :component="'heroicon-o-' . $timer['icon']" class="h-4 w-4" />
                            </div>
                        @else
                            <div
                                class="shrink-0 flex h-9 w-9 items-center justify-center border border-zinc-700 bg-zinc-900 group-hover:border-violet-500/40 text-violet-400/60 transition">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        @endif

                        <span
                            class="text-xs font-mono font-bold uppercase tracking-wider text-zinc-300 truncate group-hover:text-violet-300 transition">
                            {{ e($timer['name']) }}
                        </span>
                    </div>

                    {{-- Right: Countdown / Static time --}}
                    <div class="shrink-0 ml-4">
                        @if ($timer['type'] === 'static')
                            <span
                                class="text-xs font-mono text-zinc-500 bg-zinc-900 border border-zinc-700/60 px-2.5 py-1">
                                {{ e($timer['time']) }}
                            </span>
                        @elseif ($timer['next_event'])
                            <div x-data="eventCountdown('{{ $timer['next_event']->toIso8601String() }}')"
                                class="flex items-center gap-0.5 font-mono text-sm font-bold tabular-nums">

                                <template x-if="parseInt(days) > 0">
                                    <span class="flex items-baseline gap-0.5">
                                        <span x-text="days" class="text-cyan-400"></span>
                                        <span class="text-[10px] text-zinc-600 mr-1">{{ __('event-timers.d') }}</span>
                                    </span>
                                </template>

                                <span class="inline-flex items-center gap-0.5">
                                    <span x-text="hours" class="text-violet-400 min-w-[1.5rem] text-center">00</span>
                                    <span class="text-zinc-700 animate-pulse">:</span>
                                    <span x-text="minutes" class="text-violet-400 min-w-[1.5rem] text-center">00</span>
                                    <span class="text-zinc-700 animate-pulse">:</span>
                                    <span x-text="seconds" class="text-fuchsia-400 min-w-[1.5rem] text-center">00</span>
                                </span>
                            </div>
                        @endif
                    </div>

                    {{-- Hover glow effect --}}
                    <div
                        class="absolute inset-0 bg-linear-to-r from-violet-500/0 via-violet-500/0 to-violet-500/0 group-hover:from-violet-500/3 group-hover:via-transparent group-hover:to-transparent pointer-events-none transition-all duration-300">
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="py-8 text-center">
            <p class="text-xs font-mono uppercase tracking-[0.3em] text-zinc-700">{{ __('index.event_timers') }}</p>
        </div>
    @endif
</div>
