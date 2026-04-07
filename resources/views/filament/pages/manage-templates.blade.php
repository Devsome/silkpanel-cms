<x-filament::page>
    <div class="mb-8">
        <h2 class="text-lg font-semibold mb-4 dark:text-white">
            {{ __('filament/templates.blade.section') }}
        </h2>
        {{ $this->form }}
    </div>

    <div class="mb-8">
        <x-filament::button wire:click="downloadStarter" icon="heroicon-o-arrow-down-tray" color="gray">
            {{ __('filament/templates.blade.download_starter') }}
        </x-filament::button>
        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
            {{ __('filament/templates.blade.download_helper') }}
        </p>
    </div>

    <div>
        <h2 class="text-lg font-semibold mb-4 dark:text-white">
            {{ __('filament/templates.blade.installed_templates') }}
        </h2>

        @if (count($this->templates) === 0)
            <div
                class="text-center py-12 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                <x-heroicon-o-paint-brush class="w-12 h-12 mx-auto text-gray-400" />
                <h3 class="mt-4 text-sm font-medium text-gray-900 dark:text-white">
                    {{ __('filament/templates.blade.no_templates_found') }}
                </h3>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('filament/templates.blade.no_templates_description') }}
                </p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($this->templates as $template)
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden
                        {{ $template['is_active'] ? 'ring-2 ring-primary-500' : '' }}">

                        <div class="h-40 bg-gray-100 dark:bg-gray-900 flex items-center justify-center">
                            @if ($template['metadata'] && !empty($template['metadata']['preview_image']))
                                <img src="{{ route('template.preview-image', $template['slug']) }}"
                                    alt="{{ $template['name'] }}" class="w-full h-full object-cover" />
                            @else
                                <x-heroicon-o-paint-brush class="size-16 text-gray-300 dark:text-gray-600" />
                            @endif
                        </div>

                        <div class="p-4">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="font-semibold text-gray-900 dark:text-white">
                                    {{ $template['name'] }}
                                </h3>
                                @if ($template['is_active'])
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800 dark:bg-primary-800 dark:text-primary-100">
                                        {{ __('filament/templates.blade.active_badge') }}
                                    </span>
                                @endif
                            </div>

                            @if ($template['metadata'])
                                @if (!empty($template['metadata']['version']))
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ __('filament/templates.blade.version', ['version' => $template['metadata']['version']]) }}
                                        @if (!empty($template['metadata']['author']))
                                            {{ __('filament/templates.blade.by', ['author' => $template['metadata']['author']]) }}
                                        @endif
                                    </p>
                                @endif
                                @if (!empty($template['metadata']['description']))
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                        {{ $template['metadata']['description'] }}
                                    </p>
                                @endif
                            @endif

                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">
                                {{ $template['file_count'] }} {{ Str::plural('file', $template['file_count']) }}
                            </p>

                            <div class="flex gap-2 mt-4">
                                @if (!$template['is_active'])
                                    <x-filament::button wire:click="activateTemplate('{{ $template['slug'] }}')"
                                        size="sm" color="primary">
                                        {{ __('filament/templates.blade.activate_button') }}
                                    </x-filament::button>
                                @else
                                    <x-filament::button wire:click="deactivateTemplate" size="sm" color="warning">
                                        {{ __('filament/templates.blade.deactivate_button') }}
                                    </x-filament::button>
                                @endif

                                @if ($template['slug'] !== 'basic')
                                    @if (!$template['is_active'])
                                        <x-filament::button wire:click="deleteTemplate('{{ $template['slug'] }}')"
                                            wire:confirm="Are you sure you want to delete this template?" size="sm"
                                            color="danger">
                                            {{ __('filament/templates.blade.delete_button') }}
                                        </x-filament::button>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-filament::page>
