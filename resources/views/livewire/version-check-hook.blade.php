@if (!$hasSeen && !empty($currentVersion) && $currentVersion !== 'unknown')
    <div
        x-data="{ open: true }"
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[60] flex items-center justify-center p-4"
        style="display: none;"
    >
        {{-- Backdrop --}}
        <div
            class="absolute inset-0 bg-gray-950/60 backdrop-blur-sm"
            @click="open = false; $wire.dismiss()"
        ></div>

        {{-- Modal --}}
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            class="relative w-full max-w-lg bg-white dark:bg-gray-900 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-800 overflow-hidden"
        >
            {{-- Purple gradient top bar --}}
            <div class="h-1 bg-gradient-to-r from-violet-600 via-purple-500 to-indigo-500"></div>

            <div class="p-6">
                {{-- Header --}}
                <div class="flex items-start justify-between mb-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 flex items-center justify-center shadow-lg shadow-purple-500/30">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-gray-900 dark:text-white">What's New</h2>
                            <span class="inline-flex items-center gap-1 text-xs font-mono font-semibold text-violet-600 dark:text-violet-400">
                                v{{ $currentVersion }}
                            </span>
                        </div>
                    </div>
                    <button
                        @click="open = false; $wire.dismiss()"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                @if (!empty($entry['description']))
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-5">{{ $entry['description'] }}</p>
                @endif

                {{-- Features --}}
                @if (!empty($entry['features']))
                    <div class="space-y-3 mb-6">
                        @foreach ($entry['features'] as $feature)
                            <div class="flex items-start gap-3 p-3 rounded-xl bg-gray-50 dark:bg-gray-800/60 border border-gray-100 dark:border-gray-700/50">
                                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-violet-100 to-purple-50 dark:from-violet-900/40 dark:to-purple-900/20 border border-violet-200/60 dark:border-violet-700/40 flex items-center justify-center flex-shrink-0">
                                    <x-filament::icon :icon="$feature['icon'] ?? 'heroicon-o-star'" class="w-4 h-4 text-violet-600 dark:text-violet-400" />
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-900 dark:text-white">{{ $feature['title'] ?? '' }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 leading-relaxed">{{ $feature['description'] ?? '' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Footer --}}
                <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-800">
                    <a
                        href="{{ route('filament.admin.pages.whats-new') }}"
                        class="text-xs text-violet-600 dark:text-violet-400 hover:underline font-medium"
                        @click="open = false; $wire.dismiss()"
                    >
                        View full changelog →
                    </a>
                    <button
                        wire:click="dismiss"
                        @click="open = false"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-semibold bg-gradient-to-r from-violet-600 to-purple-600 text-white hover:from-violet-700 hover:to-purple-700 transition-all shadow-md shadow-purple-500/20"
                    >
                        Got it
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
