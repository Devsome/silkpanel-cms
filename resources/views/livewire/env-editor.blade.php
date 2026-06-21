<div>
    {{-- Button in der Topbar --}}
    <button
        wire:click="openModal"
        title="{{ __('filament/env-editor.button_tooltip') }}"
        class="flex items-center justify-center w-9 h-9 rounded-lg border-none cursor-pointer bg-transparent text-gray-400 hover:bg-gray-100 hover:text-primary-500 dark:text-gray-500 dark:hover:bg-white/5 dark:hover:text-primary-400 transition-colors duration-150"
    >
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
            <path fill-rule="evenodd" d="M12 1.5a5.25 5.25 0 0 0-5.25 5.25v3a3 3 0 0 0-3 3v6.75a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3v-6.75a3 3 0 0 0-3-3v-3c0-2.9-2.35-5.25-5.25-5.25Zm3.75 8.25v-3a3.75 3.75 0 1 0-7.5 0v3h7.5Z" clip-rule="evenodd" />
        </svg>
    </button>

    {{-- Password Confirmation Modal --}}
    @if($showPasswordModal)
        <div
            x-data
            x-init="$nextTick(() => $el.querySelector('[data-modal-backdrop]').focus())"
            class="fixed inset-0 z-[9998] flex items-center justify-center p-4"
        >
            <div
                data-modal-backdrop
                wire:click="closePasswordModal"
                tabindex="-1"
                class="absolute inset-0 bg-black/50 backdrop-blur-sm"
            ></div>

            <div
                role="dialog"
                aria-modal="true"
                class="relative z-10 w-full max-w-sm rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-white/10 shadow-2xl p-7"
                @keydown.escape.window="$wire.closePasswordModal()"
            >
                <form wire:submit="confirmPassword">
                    <div class="flex items-center gap-3.5 mb-4">
                        <div class="flex-shrink-0 flex items-center justify-center w-11 h-11 rounded-xl bg-primary-500/10 dark:bg-primary-400/10">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-primary-500 dark:text-primary-400">
                                <path fill-rule="evenodd" d="M12 1.5a5.25 5.25 0 0 0-5.25 5.25v3a3 3 0 0 0-3 3v6.75a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3v-6.75a3 3 0 0 0-3-3v-3c0-2.9-2.35-5.25-5.25-5.25Zm3.75 8.25v-3a3.75 3.75 0 1 0-7.5 0v3h7.5Z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="m-0 text-base font-bold text-gray-900 dark:text-white">{{ __('filament/env-editor.password_modal_title') }}</h2>
                            <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ __('filament/env-editor.password_modal_subtitle') }}</p>
                        </div>
                    </div>

                    <div class="mb-5">
                        <input
                            type="password"
                            wire:model="password"
                            autofocus
                            placeholder="{{ __('filament/env-editor.password_placeholder') }}"
                            class="w-full rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500"
                        />
                        @if($passwordError)
                            <p class="mt-2 text-xs text-red-500">{{ $passwordError }}</p>
                        @endif
                    </div>

                    <div class="flex flex-wrap gap-3 justify-end">
                        <button
                            type="button"
                            wire:click="closePasswordModal"
                            class="px-4 py-2 rounded-lg border border-gray-300 dark:border-white/10 bg-transparent text-gray-700 dark:text-gray-300 text-sm font-medium cursor-pointer hover:bg-gray-50 dark:hover:bg-white/5 transition-colors"
                        >
                            {{ __('filament/env-editor.cancel') }}
                        </button>
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            class="px-5 py-2 rounded-lg border-none bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold cursor-pointer transition-colors disabled:opacity-60"
                        >
                            {{ __('filament/env-editor.confirm_password') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Editor Modal --}}
    @if($showEditorModal)
        <div
            x-data
            x-init="$nextTick(() => $el.querySelector('[data-modal-backdrop]').focus())"
            class="fixed inset-0 z-[9998] flex items-center justify-center p-4"
        >
            <div
                data-modal-backdrop
                wire:click="closeEditorModal"
                tabindex="-1"
                class="absolute inset-0 bg-black/50 backdrop-blur-sm"
            ></div>

            <div
                role="dialog"
                aria-modal="true"
                class="relative z-10 w-full max-w-2xl rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-white/10 shadow-2xl p-7"
                @keydown.escape.window="$wire.closeEditorModal()"
            >
                <div class="flex items-center gap-3.5 mb-4">
                    <div class="flex-shrink-0 flex items-center justify-center w-11 h-11 rounded-xl bg-primary-500/10 dark:bg-primary-400/10">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-primary-500 dark:text-primary-400">
                            <path fill-rule="evenodd" d="M12 1.5a5.25 5.25 0 0 0-5.25 5.25v3a3 3 0 0 0-3 3v6.75a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3v-6.75a3 3 0 0 0-3-3v-3c0-2.9-2.35-5.25-5.25-5.25Zm3.75 8.25v-3a3.75 3.75 0 1 0-7.5 0v3h7.5Z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="m-0 text-base font-bold text-gray-900 dark:text-white">{{ __('filament/env-editor.editor_modal_title') }}</h2>
                        <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ __('filament/env-editor.editor_modal_subtitle') }}</p>
                    </div>
                </div>

                <textarea
                    wire:model="content"
                    rows="18"
                    spellcheck="false"
                    autocomplete="off"
                    class="w-full rounded-lg border border-gray-300 dark:border-white/10 bg-gray-50 dark:bg-gray-950 px-3 py-2 text-xs font-mono leading-relaxed text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500 resize-y"
                ></textarea>

                <p class="mt-3 mb-5 text-xs text-gray-500 dark:text-gray-400">
                    {{ __('filament/env-editor.cache_hint') }}
                </p>

                <div class="flex flex-wrap gap-3 justify-end">
                    <button
                        wire:click="closeEditorModal"
                        class="px-4 py-2 rounded-lg border border-gray-300 dark:border-white/10 bg-transparent text-gray-700 dark:text-gray-300 text-sm font-medium cursor-pointer hover:bg-gray-50 dark:hover:bg-white/5 transition-colors"
                    >
                        {{ __('filament/env-editor.cancel') }}
                    </button>
                    <button
                        wire:click="save"
                        wire:loading.attr="disabled"
                        class="px-5 py-2 rounded-lg border-none bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold cursor-pointer flex items-center gap-1.5 transition-colors disabled:opacity-60"
                    >
                        <span wire:loading.remove wire:target="save">{{ __('filament/env-editor.save') }}</span>
                        <span wire:loading wire:target="save" class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            {{ __('filament/env-editor.save') }}
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
