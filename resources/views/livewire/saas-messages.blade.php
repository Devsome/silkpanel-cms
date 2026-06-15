<div>
    {{-- Persistent Notifications --}}
    @foreach($notifications as $notification)
        <div
            x-data="{ show: true }"
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            class="fi-banner bg-primary-600 text-white"
            style="padding: 0.75rem 1.25rem; display: flex; align-items: flex-start; gap: 0.75rem; border-bottom: 1px solid rgba(255,255,255,0.15);"
        >
            <x-heroicon-o-bell class="w-5 h-5 mt-0.5 shrink-0" />
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold">{{ $notification['title'] }}</p>
                <p class="text-sm opacity-90 mt-0.5">{{ $notification['body'] }}</p>
            </div>
            <button
                wire:click="dismissNotification({{ $notification['id'] }})"
                wire:loading.attr="disabled"
                type="button"
                class="shrink-0 opacity-75 hover:opacity-100 transition-opacity"
                title="Mark as read"
            >
                <x-heroicon-o-x-mark class="w-5 h-5" />
            </button>
        </div>
    @endforeach

    {{-- Survey Modal --}}
    @if($activeSurvey)
        <div
            x-data="{ open: true }"
            x-show="open"
            x-trap.noscroll="open"
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            style="background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);"
        >
            <div
                x-show="open"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                class="fi-modal-window w-full max-w-lg rounded-xl bg-white dark:bg-gray-900 shadow-2xl"
                style="padding: 1.5rem;"
            >
                {{-- Header --}}
                <div class="flex items-start gap-3 mb-4">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-warning-100 dark:bg-warning-900">
                        <x-heroicon-o-clipboard-document-list class="w-5 h-5 text-warning-600 dark:text-warning-400" />
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-gray-950 dark:text-white">
                            {{ $activeSurvey['title'] }}
                        </h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                            Survey from SilkPanel
                        </p>
                    </div>
                </div>

                {{-- Body --}}
                <p class="text-sm text-gray-700 dark:text-gray-300 mb-5">
                    {{ $activeSurvey['body'] }}
                </p>

                {{-- Input --}}
                <div class="mb-5">
                    <label class="fi-fo-field-wrp-label block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">
                        {{ $activeSurvey['input_label'] ?? 'Your answer' }}
                    </label>
                    <textarea
                        wire:model="surveyResponse"
                        rows="4"
                        class="fi-fo-textarea w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none resize-none"
                        placeholder="Type your answer here…"
                    ></textarea>
                </div>

                {{-- Actions --}}
                <div class="flex justify-end gap-2">
                    <button
                        wire:click="submitSurvey"
                        wire:loading.attr="disabled"
                        type="button"
                        class="fi-btn fi-btn-color-primary fi-btn-size-md fi-color-primary fi-btn-filled inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg bg-primary-600 text-white hover:bg-primary-500 disabled:opacity-50 transition-colors"
                    >
                        <span wire:loading.remove wire:target="submitSurvey">Submit Answer</span>
                        <span wire:loading wire:target="submitSurvey">Sending…</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
