<div>
    {{-- Blitz-Button in der Topbar --}}
    <button
        wire:click="openModal"
        title="{{ __('filament/cache.button_tooltip') }}"
        class="flex items-center justify-center w-9 h-9 rounded-lg border-none cursor-pointer bg-transparent text-gray-400 hover:bg-gray-100 hover:text-amber-500 dark:text-gray-500 dark:hover:bg-white/5 dark:hover:text-amber-400 transition-colors duration-150"
    >
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
            <path fill-rule="evenodd" d="M14.615 1.595a.75.75 0 0 1 .359.852L12.982 9.75h7.268a.75.75 0 0 1 .548 1.262l-10.5 11.25a.75.75 0 0 1-1.272-.71l1.992-7.302H3.818a.75.75 0 0 1-.548-1.262l10.5-11.25a.75.75 0 0 1 .845-.143Z" clip-rule="evenodd" />
        </svg>
    </button>

    {{-- Confirmation Modal --}}
    @if($showModal)
        <div
            x-data
            x-init="$nextTick(() => $el.querySelector('[data-modal-backdrop]').focus())"
            class="fixed inset-0 z-[9998] flex items-center justify-center p-4"
        >
            {{-- Backdrop --}}
            <div
                data-modal-backdrop
                wire:click="closeModal"
                tabindex="-1"
                class="absolute inset-0 bg-black/50 backdrop-blur-sm"
            ></div>

            {{-- Modal Card --}}
            <div
                role="dialog"
                aria-modal="true"
                class="relative z-10 w-full max-w-md rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-white/10 shadow-2xl p-7"
                @keydown.escape.window="$wire.closeModal()"
            >
                {{-- Icon + Title --}}
                <div class="flex items-center gap-3.5 mb-4">
                    <div class="flex-shrink-0 flex items-center justify-center w-11 h-11 rounded-xl bg-amber-500/10 dark:bg-amber-400/10">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-amber-500 dark:text-amber-400">
                            <path fill-rule="evenodd" d="M14.615 1.595a.75.75 0 0 1 .359.852L12.982 9.75h7.268a.75.75 0 0 1 .548 1.262l-10.5 11.25a.75.75 0 0 1-1.272-.71l1.992-7.302H3.818a.75.75 0 0 1-.548-1.262l10.5-11.25a.75.75 0 0 1 .845-.143Z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="m-0 text-base font-bold text-gray-900 dark:text-white">{{ __('filament/cache.modal_title') }}</h2>
                        <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ __('filament/cache.modal_subtitle') }}</p>
                    </div>
                </div>

                {{-- Body --}}
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-5 leading-relaxed">
                    {{ __('filament/cache.modal_description') }}
                </p>

                {{-- What gets cleared --}}
                <ul class="mb-6 p-0 list-none flex flex-col gap-1.5">
                    @foreach(['cache', 'config', 'view', 'route'] as $item)
                        <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-3.5 h-3.5 flex-shrink-0 text-amber-500 dark:text-amber-400">
                                <path fill-rule="evenodd" d="M12.416 3.376a.75.75 0 0 1 .208 1.04l-5 7.5a.75.75 0 0 1-1.154.114l-3-3a.75.75 0 0 1 1.06-1.06l2.353 2.353 4.493-6.74a.75.75 0 0 1 1.04-.207Z" clip-rule="evenodd" />
                            </svg>
                            {{ __("filament/cache.item_{$item}") }}
                        </li>
                    @endforeach
                </ul>

                {{-- Buttons --}}
                <div class="flex flex-wrap gap-3 justify-end">
                    <button
                        wire:click="closeModal"
                        class="px-4 py-2 rounded-lg border border-gray-300 dark:border-white/10 bg-transparent text-gray-700 dark:text-gray-300 text-sm font-medium cursor-pointer hover:bg-gray-50 dark:hover:bg-white/5 transition-colors"
                    >
                        {{ __('filament/cache.cancel') }}
                    </button>
                    <button
                        wire:click="optimize"
                        wire:loading.attr="disabled"
                        class="px-5 py-2 rounded-lg border-none bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold cursor-pointer flex items-center gap-1.5 transition-colors disabled:opacity-60"
                    >
                        <span wire:loading.remove wire:target="optimize">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5">
                                <path fill-rule="evenodd" d="M10 1a6 6 0 0 0-3.815 10.631C7.237 12.5 8 13.443 8 14.456v.644a.75.75 0 0 0 .572.729 6.016 6.016 0 0 0 2.856 0A.75.75 0 0 0 12 15.1v-.644c0-1.013.762-1.957 1.815-2.825A6 6 0 0 0 10 1ZM8.863 17.06a.75.75 0 0 0-.226 1.483 9.066 9.066 0 0 0 2.726 0 .75.75 0 0 0-.226-1.483 7.553 7.553 0 0 1-2.274 0Z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <span wire:loading wire:target="optimize">
                            <svg class="w-3.5 h-3.5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                        </span>
                        {{ __('filament/cache.optimize') }}
                    </button>
                    <button
                        wire:click="clearCache"
                        wire:loading.attr="disabled"
                        class="px-5 py-2 rounded-lg border-none bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold cursor-pointer flex items-center gap-1.5 transition-colors disabled:opacity-60"
                    >
                        <span wire:loading.remove wire:target="clearCache">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5">
                                <path fill-rule="evenodd" d="M14.615 1.595a.75.75 0 0 1 .359.852L12.982 9.75h7.268a.75.75 0 0 1 .548 1.262l-10.5 11.25a.75.75 0 0 1-1.272-.71l1.992-7.302H3.818a.75.75 0 0 1-.548-1.262l10.5-11.25a.75.75 0 0 1 .845-.143Z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <span wire:loading wire:target="clearCache">
                            <svg class="w-3.5 h-3.5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                        </span>
                        {{ __('filament/cache.confirm') }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
