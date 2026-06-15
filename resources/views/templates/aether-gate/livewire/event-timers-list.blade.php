<div>
    @if (count($timers) > 0)
        <div class="ag-card-high p-6 shadow-2xl">
            <h3 class="ag-font-display ag-text-primary font-bold uppercase tracking-widest mb-6 text-sm text-center">
                {{ __('index.event_timers') }}
            </h3>
            <div class="space-y-4">
                @foreach ($timers as $timer)
                    <div class="p-3 ag-card-low flex justify-between items-center">
                        <div class="flex items-center gap-3 min-w-0">
                            @if ($timer['image'])
                                <img src="{{ asset('storage/' . $timer['image']) }}" alt="{{ e($timer['name']) }}"
                                    class="h-[40px] w-[40px] object-cover shrink-0">
                            @elseif ($timer['icon'])
                                <x-dynamic-component :component="'heroicon-o-' . $timer['icon']" class="h-5 w-5 ag-text-primary shrink-0" />
                            @else
                                <x-heroicon-o-clock class="h-5 w-5 ag-text-primary shrink-0" />
                            @endif
                            <span class="text-[10px] ag-text-muted uppercase truncate">
                                {{ e($timer['name']) }}
                            </span>
                        </div>

                        <div class="shrink-0 ml-4">
                            @if ($timer['type'] === 'static')
                                <span class="text-sm ag-text-muted">
                                    {{ e($timer['time']) }}
                                </span>
                            @elseif ($timer['next_event'])
                                <div x-data="eventCountdown('{{ $timer['next_event']->toIso8601String() }}')"
                                    class="flex items-center gap-1 ag-font-display text-base font-bold tabular-nums ag-text-primary">
                                    <template x-if="parseInt(days) > 0">
                                        <span>
                                            <span x-text="days">00</span><span class="ag-text-muted">{{ __('event-timers.d') }}</span>
                                        </span>
                                    </template>
                                    <span x-text="hours">00</span><span class="ag-text-muted">:</span><span
                                        x-text="minutes">00</span><span class="ag-text-muted">:</span><span
                                        x-text="seconds">00</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
