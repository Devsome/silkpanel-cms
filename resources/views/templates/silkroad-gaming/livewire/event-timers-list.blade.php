<div>
    @if (count($timers) > 0)
        <div class="space-y-2">
            @foreach ($timers as $timer)
                <div
                    class="flex items-center justify-between rounded-lg border border-gray-200/10 bg-gray-900/50 px-4 py-3">
                    <div class="flex items-center gap-3 min-w-0">
                        @if ($timer['image'])
                            <img src="{{ asset('storage/' . $timer['image']) }}" alt="{{ e($timer['name']) }}"
                                class="h-12.5 w-12.5 rounded object-cover shrink-0">
                        @elseif ($timer['icon'])
                            <x-dynamic-component :component="'heroicon-o-' . $timer['icon']" class="h-5 w-5 text-primary-500 shrink-0" />
                        @else
                            <x-heroicon-o-clock class="h-5 w-5 text-primary-500 shrink-0" />
                        @endif
                        <span class="text-sm font-semibold text-white-900 truncate">
                            {{ e($timer['name']) }}
                        </span>
                    </div>

                    <div class="shrink-0 ml-4">
                        @if ($timer['type'] === 'static')
                            <span class="text-sm text-white-600 dark:text-gray-400">
                                {{ e($timer['time']) }}
                            </span>
                        @elseif ($timer['next_event'])
                            <div x-data="eventCountdown('{{ $timer['next_event']->toIso8601String() }}')"
                                class="flex items-center gap-1 font-mono text-sm tabular-nums text-white-900">
                                <template x-if="parseInt(days) > 0">
                                    <span>
                                        <span x-text="days">00</span><span
                                            class="text-white-400">{{ __('event-timers.d') }}</span>
                                    </span>
                                </template>
                                <span x-text="hours">00</span><span class="text-white-400">:</span><span
                                    x-text="minutes">00</span><span class="text-white-400">:</span><span
                                    x-text="seconds">00</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
