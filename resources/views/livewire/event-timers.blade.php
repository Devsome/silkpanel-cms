<div>
    @if (count($timers) > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($timers as $timer)
                <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-center gap-3 mb-3">
                        @if ($timer['image'])
                            <img src="{{ asset('storage/' . $timer['image']) }}" alt="{{ e($timer['name']) }}"
                                class="h-[50px] w-[50px] rounded object-cover shrink-0">
                        @elseif ($timer['icon'])
                            <x-dynamic-component :component="'heroicon-o-' . $timer['icon']" class="h-6 w-6 text-primary-500 shrink-0" />
                        @else
                            <x-heroicon-o-clock class="h-6 w-6 text-primary-500 shrink-0" />
                        @endif
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                            {{ e($timer['name']) }}
                        </h3>
                    </div>

                    @if ($timer['type'] === 'static')
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ e($timer['time']) }}
                        </p>
                    @elseif ($timer['next_event'])
                        <div x-data="eventCountdown('{{ $timer['next_event']->toIso8601String() }}')" class="text-center">
                            <div class="flex items-center justify-center gap-2">
                                <template x-if="parseInt(days) > 0">
                                    <div class="flex items-center gap-2">
                                        <div class="text-center">
                                            <span x-text="days"
                                                class="text-2xl font-bold tabular-nums text-gray-900 dark:text-white">00</span>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ __('event-timers.days') }}</p>
                                        </div>
                                        <span class="text-2xl font-bold text-gray-400">:</span>
                                    </div>
                                </template>
                                <div class="text-center">
                                    <span x-text="hours"
                                        class="text-2xl font-bold tabular-nums text-gray-900 dark:text-white">00</span>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('event-timers.hours') }}
                                    </p>
                                </div>
                                <span class="text-2xl font-bold text-gray-400">:</span>
                                <div class="text-center">
                                    <span x-text="minutes"
                                        class="text-2xl font-bold tabular-nums text-gray-900 dark:text-white">00</span>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('event-timers.minutes') }}
                                    </p>
                                </div>
                                <span class="text-2xl font-bold text-gray-400">:</span>
                                <div class="text-center">
                                    <span x-text="seconds"
                                        class="text-2xl font-bold tabular-nums text-gray-900 dark:text-white">00</span>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('event-timers.seconds') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
