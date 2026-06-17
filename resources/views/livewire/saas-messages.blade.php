<div>
    {{-- Persistent Notifications --}}
    @if(count($notifications))
        <div class="space-y-px">
            @foreach($notifications as $notification)
                <div
                    x-data="{ visible: true }"
                    x-show="visible"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 max-h-24"
                    x-transition:leave-end="opacity-0 max-h-0"
                    class="flex items-start gap-3 px-4 py-3 bg-primary-600 dark:bg-primary-700 text-white text-sm border-b border-primary-700 dark:border-primary-800"
                >
                    <x-heroicon-o-bell class="w-4 h-4 mt-0.5 shrink-0 opacity-80" />
                    <div class="flex-1 min-w-0 leading-snug">
                        <span class="font-semibold">{{ $notification['title'] }}</span>
                        <span class="opacity-80 ml-2">{{ $notification['body'] }}</span>
                    </div>
                    <button
                        wire:click="dismissNotification({{ $notification['id'] }})"
                        type="button"
                        class="shrink-0 opacity-60 hover:opacity-100 transition-opacity ml-2"
                        title="Mark as read"
                    >
                        <x-heroicon-o-x-mark class="w-4 h-4" />
                    </button>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Survey Modal --}}
    @if($activeSurvey)
        <div
            x-data="{ open: true }"
            x-show="open"
            class="fixed inset-0 z-[100] flex items-center justify-center p-4"
            style="background: rgba(0,0,0,0.45); backdrop-filter: blur(3px);"
        >
            <div
                x-show="open"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                class="w-full max-w-md rounded-xl shadow-2xl overflow-hidden bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800"
                @click.outside=""
            >
                {{-- Modal Header --}}
                <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
                        <x-heroicon-o-clipboard-document-list class="w-4 h-4 text-gray-500 dark:text-gray-400" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $activeSurvey['title'] }}</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Survey · SilkPanel</p>
                    </div>
                </div>

                {{-- Modal Body --}}
                <div class="px-5 py-4 bg-white dark:bg-gray-900">
                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-4 leading-relaxed">
                        {{ $activeSurvey['body'] }}
                    </p>

                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">
                        {{ $activeSurvey['input_label'] ?? 'Your answer' }}
                    </label>
                    <textarea
                        wire:model="surveyResponse"
                        rows="4"
                        class="w-full rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-sm text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 px-3 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none resize-none transition"
                        placeholder="Type your answer…"
                    ></textarea>
                </div>

                {{-- Modal Footer --}}
                <div class="flex justify-end gap-2 px-5 py-3 border-t border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50">
                    <button
                        wire:click="submitSurvey"
                        wire:loading.attr="disabled"
                        type="button"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-primary-600 hover:bg-primary-500 disabled:opacity-50 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-colors"
                    >
                        <span wire:loading.remove wire:target="submitSurvey">
                            <x-heroicon-o-paper-airplane class="w-3.5 h-3.5 inline -mt-0.5 mr-1" />
                            Submit Answer
                        </span>
                        <span wire:loading wire:target="submitSurvey" class="flex items-center gap-1.5">
                            <svg class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                            </svg>
                            Sending…
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
